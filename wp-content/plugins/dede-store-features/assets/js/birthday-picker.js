(function () {
    'use strict';

    var config = window.DedeBirthdayPicker || {};
    var monthNames = [
        'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
        'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
    ];

    function pad(value) {
        return String(value).padStart(2, '0');
    }

    function parseDate(value) {
        var match = String(value || '').match(/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/);
        return match ? {year: Number(match[1]), month: Number(match[2]), day: Number(match[3])} : null;
    }

    function compareDates(left, right) {
        if (left.year !== right.year) return left.year - right.year;
        if (left.month !== right.month) return left.month - right.month;
        return left.day - right.day;
    }

    function jalaliToGregorian(jy, jm, jd) {
        jy += 1595;
        var days = -355668 + (365 * jy) + (Math.floor(jy / 33) * 8) + Math.floor(((jy % 33) + 3) / 4) + jd;
        days += jm < 7 ? (jm - 1) * 31 : ((jm - 7) * 30) + 186;
        var gy = 400 * Math.floor(days / 146097);
        days %= 146097;
        if (days > 36524) {
            gy += 100 * Math.floor((--days) / 36524);
            days %= 36524;
            if (days >= 365) days += 1;
        }
        gy += 4 * Math.floor(days / 1461);
        days %= 1461;
        if (days > 365) {
            gy += Math.floor((days - 1) / 365);
            days = (days - 1) % 365;
        }
        var gd = days + 1;
        var leap = (gy % 4 === 0 && gy % 100 !== 0) || gy % 400 === 0;
        var months = [0, 31, leap ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        var gm = 1;
        while (gm <= 12 && gd > months[gm]) {
            gd -= months[gm];
            gm += 1;
        }
        return {year: gy, month: gm, day: gd};
    }

    function utcValue(date) {
        var gregorian = jalaliToGregorian(date.year, date.month, date.day);
        return Date.UTC(gregorian.year, gregorian.month - 1, gregorian.day);
    }

    function daysInMonth(year, month) {
        var next = month === 12
            ? {year: year + 1, month: 1, day: 1}
            : {year: year, month: month + 1, day: 1};
        return Math.round((utcValue(next) - utcValue({year: year, month: month, day: 1})) / 86400000);
    }

    function firstWeekday(year, month) {
        var gregorian = jalaliToGregorian(year, month, 1);
        return (new Date(Date.UTC(gregorian.year, gregorian.month - 1, gregorian.day)).getUTCDay() + 1) % 7;
    }

    function calendarIcon() {
        return '<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false">' +
            '<path d="M7 2v3M17 2v3M3.5 9h17M5.5 4h13a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2h-13a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/>' +
            '</svg>';
    }

    function buildPicker(fieldset) {
        if (fieldset.dataset.birthdayInitialized === '1') return;

        var fallback = fieldset.querySelector(':scope > div');
        var dayField = fieldset.querySelector('[name="birthday_day"]');
        var monthField = fieldset.querySelector('[name="birthday_month"]');
        var yearField = fieldset.querySelector('[name="birthday_year"]');
        var maxDate = parseDate(config.maxDate);
        var minYear = Number(config.minYear || 1300);
        if (!fallback || !dayField || !monthField || !yearField || !maxDate) return;

        fallback.classList.add('dede-birthday__fallback');
        var control = document.createElement('div');
        control.className = 'dede-birthday__control';
        control.innerHTML =
            '<button type="button" class="dede-birthday__trigger" data-birthday-toggle aria-haspopup="dialog" aria-expanded="false">' +
                '<span data-birthday-display>انتخاب تاریخ تولد</span>' + calendarIcon() +
            '</button>' +
            '<div class="dede-birthday__popover" data-birthday-popover role="dialog" aria-label="انتخاب تاریخ تولد" hidden>' +
                '<div class="dede-birthday__toolbar">' +
                    '<select data-birthday-month aria-label="ماه تولد"></select>' +
                    '<select data-birthday-year aria-label="سال تولد"></select>' +
                '</div>' +
                '<div class="dede-birthday__weekdays" aria-hidden="true">' +
                    '<span>ش</span><span>ی</span><span>د</span><span>س</span><span>چ</span><span>پ</span><span>ج</span>' +
                '</div>' +
                '<div class="dede-birthday__days" data-birthday-days role="grid"></div>' +
                '<div class="dede-birthday__footer">' +
                    '<button type="button" data-birthday-clear>پاک کردن</button>' +
                    '<button type="button" data-birthday-close>بستن</button>' +
                '</div>' +
            '</div>';
        fieldset.insertBefore(control, fallback);

        var trigger = control.querySelector('[data-birthday-toggle]');
        var display = control.querySelector('[data-birthday-display]');
        var popover = control.querySelector('[data-birthday-popover]');
        var monthPicker = control.querySelector('[data-birthday-month]');
        var yearPicker = control.querySelector('[data-birthday-year]');
        var daysGrid = control.querySelector('[data-birthday-days]');
        var clearButton = control.querySelector('[data-birthday-clear]');
        var closeButton = control.querySelector('[data-birthday-close]');
        var selected = dayField.value && monthField.value && yearField.value
            ? {year: Number(yearField.value), month: Number(monthField.value), day: Number(dayField.value)}
            : null;
        var view = selected ? Object.assign({}, selected) : Object.assign({}, maxDate);

        monthNames.forEach(function (name, index) {
            var option = document.createElement('option');
            option.value = String(index + 1);
            option.textContent = name;
            monthPicker.appendChild(option);
        });
        for (var year = maxDate.year; year >= minYear; year -= 1) {
            var yearOption = document.createElement('option');
            yearOption.value = String(year);
            yearOption.textContent = String(year);
            yearPicker.appendChild(yearOption);
        }

        function selectedText() {
            return selected ? selected.year + '/' + pad(selected.month) + '/' + pad(selected.day) : 'انتخاب تاریخ تولد';
        }

        function syncFields() {
            dayField.value = selected ? String(selected.day) : '';
            monthField.value = selected ? String(selected.month) : '';
            yearField.value = selected ? String(selected.year) : '';
            display.textContent = selectedText();
            [dayField, monthField, yearField].forEach(function (field) {
                field.dispatchEvent(new Event('change', {bubbles: true}));
            });
        }

        function closePicker() {
            popover.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
        }

        function render() {
            if (view.year > maxDate.year) view.year = maxDate.year;
            if (view.year === maxDate.year && view.month > maxDate.month) view.month = maxDate.month;
            yearPicker.value = String(view.year);
            monthPicker.value = String(view.month);
            daysGrid.innerHTML = '';

            var offset = firstWeekday(view.year, view.month);
            var count = daysInMonth(view.year, view.month);
            for (var emptyIndex = 0; emptyIndex < offset; emptyIndex += 1) {
                var empty = document.createElement('span');
                empty.className = 'dede-birthday__empty';
                daysGrid.appendChild(empty);
            }

            for (var day = 1; day <= count; day += 1) {
                (function (candidate) {
                    var button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = String(candidate.day);
                    button.setAttribute('role', 'gridcell');
                    button.disabled = compareDates(candidate, maxDate) > 0;
                    if (selected && compareDates(candidate, selected) === 0) {
                        button.classList.add('is-selected');
                        button.setAttribute('aria-selected', 'true');
                    }
                    button.addEventListener('click', function () {
                        selected = candidate;
                        view = Object.assign({}, candidate);
                        syncFields();
                        render();
                        closePicker();
                        trigger.focus();
                    });
                    daysGrid.appendChild(button);
                }({year: view.year, month: view.month, day: day}));
            }
        }

        trigger.addEventListener('click', function () {
            var willOpen = popover.hidden;
            popover.hidden = !willOpen;
            trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            if (willOpen) render();
        });
        monthPicker.addEventListener('change', function () {
            view.month = Number(monthPicker.value);
            render();
        });
        yearPicker.addEventListener('change', function () {
            view.year = Number(yearPicker.value);
            render();
        });
        clearButton.addEventListener('click', function () {
            selected = null;
            view = Object.assign({}, maxDate);
            syncFields();
            render();
            closePicker();
            trigger.focus();
        });
        closeButton.addEventListener('click', function () {
            closePicker();
            trigger.focus();
        });
        document.addEventListener('click', function (event) {
            if (!popover.hidden && !control.contains(event.target)) closePicker();
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !popover.hidden) {
                closePicker();
                trigger.focus();
            }
        });

        fieldset.dataset.birthdayInitialized = '1';
        fieldset.classList.add('is-enhanced');
        display.textContent = selectedText();
        render();
    }

    function init() {
        document.querySelectorAll('.dede-birthday').forEach(buildPicker);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
