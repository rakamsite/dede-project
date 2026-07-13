(function () {
    'use strict';

    var provinceAreaCodes = {
        'آذربایجانشرقی': '41',
        'آذربایجانغربی': '44',
        'اردبیل': '45',
        'اصفهان': '31',
        'ایلام': '84',
        'بوشهر': '77',
        'تهران': '21',
        'چهارمحالوبختیاری': '38',
        'خراسانرضوی': '51',
        'خراسانجنوبی': '56',
        'خراسانشمالی': '58',
        'خوزستان': '61',
        'زنجان': '24',
        'سمنان': '23',
        'سیستانوبلوچستان': '54',
        'فارس': '71',
        'البرز': '26',
        'قم': '25',
        'قزوین': '28',
        'کردستان': '87',
        'کرمان': '34',
        'کرمانشاه': '83',
        'کهگیلویهوبویراحمد': '74',
        'گلستان': '17',
        'گیلان': '13',
        'لرستان': '66',
        'مازندران': '11',
        'مرکزی': '86',
        'هرمزگان': '76',
        'همدان': '81',
        'یزد': '35'
    };

    var validAreaCodes = Object.keys(provinceAreaCodes).map(function (key) {
        return provinceAreaCodes[key];
    });

    function digits(value) {
        return String(value || '')
            .replace(/[۰-۹]/g, function (digit) { return String('۰۱۲۳۴۵۶۷۸۹'.indexOf(digit)); })
            .replace(/[٠-٩]/g, function (digit) { return String('٠١٢٣٤٥٦٧٨٩'.indexOf(digit)); })
            .replace(/\D+/g, '');
    }

    function normalizePersian(value) {
        return String(value || '')
            .replace(/[يى]/g, 'ی')
            .replace(/ك/g, 'ک')
            .replace(/[ۀة]/g, 'ه')
            .replace(/[\s\u200c]+/g, '')
            .trim();
    }

    function isObviousPattern(value) {
        var code = digits(value);
        if (code.length < 4) return false;
        if (/^(\d)\1+$/.test(code)) return true;

        var ascending = true;
        var descending = true;
        for (var index = 1; index < code.length; index += 1) {
            var previous = Number(code.charAt(index - 1));
            var current = Number(code.charAt(index));
            if (current !== ((previous + 1) % 10)) ascending = false;
            if (current !== ((previous + 9) % 10)) descending = false;
        }
        return ascending || descending;
    }

    function selectedOptionText(select) {
        if (!select || select.selectedIndex < 0) return '';
        return select.options[select.selectedIndex] ? select.options[select.selectedIndex].text : '';
    }

    function localMobile(value) {
        var normalized = digits(value);
        if (/^989\d{9}$/.test(normalized)) return '0' + normalized.slice(2);
        if (/^9\d{9}$/.test(normalized)) return '0' + normalized;
        if (/^09\d{9}$/.test(normalized)) return normalized;
        return value;
    }

    function enhanceOptionalSection(root) {
        var details = root.querySelector('.dede-profile__optional');
        if (!details) return;

        var summary = details.querySelector('summary');
        if (summary && !summary.querySelector('.dede-profile__optional-meta')) {
            var badge = summary.querySelector(':scope > small');
            var meta = document.createElement('span');
            meta.className = 'dede-profile__optional-meta';
            if (badge) meta.appendChild(badge);

            var toggle = document.createElement('i');
            toggle.className = 'dede-profile__optional-toggle';
            toggle.setAttribute('aria-hidden', 'true');
            meta.appendChild(toggle);
            summary.appendChild(meta);
        }

        var grid = details.querySelector(':scope > .dede-profile__grid');
        if (!grid || grid.querySelector('.dede-profile__optional-contact-row')) return;

        var emailField = grid.querySelector('input[name="email"]')?.closest('.dede-field');
        var telegramField = grid.querySelector('input[name="telegram"]')?.closest('.dede-field');
        var birthdayField = grid.querySelector('.dede-birthday');
        if (!emailField || !telegramField || !birthdayField) return;

        var row = document.createElement('div');
        row.className = 'dede-profile__optional-contact-row';
        grid.insertBefore(row, emailField);
        row.appendChild(emailField);
        row.appendChild(telegramField);
        row.appendChild(birthdayField);
    }

    function enhanceMobileDisplay(root) {
        root.querySelectorAll('.dede-field').forEach(function (field) {
            var label = field.querySelector(':scope > span');
            var input = field.querySelector('input[readonly]');
            if (!label || !input || label.textContent.indexOf('شماره همراه') === -1) return;
            input.value = localMobile(input.value);
            input.setAttribute('dir', 'ltr');
        });
    }

    function addErrorHolder(wrapper, key) {
        var holder = document.createElement('small');
        holder.setAttribute('data-error-for', key);
        wrapper.appendChild(holder);
        return holder;
    }

    function enhanceLandline(root, scope) {
        var original = root.querySelector('input[name="' + scope + '_phone"]');
        var stateSelect = root.querySelector('select[name="' + scope + '_state"]');
        if (!original || original.dataset.landlineEnhanced === '1') return;

        var full = digits(original.value);
        var initialArea = /^0\d{10}$/.test(full) ? full.slice(1, 3) : '';
        var initialNumber = /^0\d{10}$/.test(full) ? full.slice(3) : '';
        original.type = 'hidden';
        original.required = false;
        original.dataset.landlineEnhanced = '1';

        var wrapper = document.createElement('div');
        wrapper.className = 'dede-landline';
        wrapper.innerHTML =
            '<div class="dede-landline__part dede-landline__part--area">' +
                '<span>کد شهر</span>' +
                '<div class="dede-landline__area-input"><b aria-hidden="true">0</b>' +
                    '<input type="text" inputmode="numeric" maxlength="2" minlength="2" autocomplete="off" required ' +
                        'name="' + scope + '_phone_area" aria-label="کد شهر بدون صفر" placeholder="21">' +
                '</div>' +
            '</div>' +
            '<div class="dede-landline__part dede-landline__part--number">' +
                '<span>شماره تلفن</span>' +
                '<input type="text" inputmode="numeric" maxlength="8" minlength="8" autocomplete="tel-national" required ' +
                    'name="' + scope + '_phone_number" aria-label="شماره تلفن ثابت هشت رقمی" placeholder="12345678">' +
            '</div>';

        original.insertAdjacentElement('afterend', wrapper);
        var areaInput = wrapper.querySelector('[name="' + scope + '_phone_area"]');
        var numberInput = wrapper.querySelector('[name="' + scope + '_phone_number"]');
        var areaHolder = addErrorHolder(wrapper.querySelector('.dede-landline__part--area'), scope + '_phone_area');
        var numberHolder = addErrorHolder(wrapper.querySelector('.dede-landline__part--number'), scope + '_phone_number');
        areaInput.value = initialArea;
        numberInput.value = initialNumber;

        function suggestedArea() {
            return provinceAreaCodes[normalizePersian(selectedOptionText(stateSelect))] || '';
        }

        function sync() {
            areaInput.value = digits(areaInput.value).slice(0, 2);
            numberInput.value = digits(numberInput.value).slice(0, 8);
            original.value = areaInput.value || numberInput.value
                ? '0' + areaInput.value + numberInput.value
                : '';
        }

        function validate(showMessage) {
            var area = digits(areaInput.value);
            var number = digits(numberInput.value);
            var areaValid = area.length === 2 && validAreaCodes.indexOf(area) !== -1;
            var numberValid = number.length === 8 && !isObviousPattern(number);
            var areaMessage = areaValid ? '' : 'کد شهر باید دو رقم معتبر باشد.';
            var numberMessage = numberValid ? '' : 'شماره تلفن باید ۸ رقم معتبر باشد.';
            areaInput.setCustomValidity(areaMessage);
            numberInput.setCustomValidity(numberMessage);
            if (showMessage) {
                areaHolder.textContent = areaMessage;
                numberHolder.textContent = numberMessage;
            } else {
                if (areaValid) areaHolder.textContent = '';
                if (numberValid) numberHolder.textContent = '';
            }
            sync();
        }

        function applySuggestion(force) {
            var suggested = suggestedArea();
            var previous = areaInput.dataset.suggested || '';
            if (suggested && (force || !areaInput.value || areaInput.value === previous)) {
                areaInput.value = suggested;
                areaInput.dataset.suggested = suggested;
                sync();
                validate(false);
            }
        }

        areaInput.addEventListener('input', function () {
            areaInput.dataset.suggested = '';
            validate(false);
        });
        numberInput.addEventListener('input', function () { validate(false); });
        areaInput.addEventListener('blur', function () { validate(true); });
        numberInput.addEventListener('blur', function () { validate(true); });
        stateSelect && stateSelect.addEventListener('change', function () { applySuggestion(false); });

        if (!initialArea) applySuggestion(true);
        validate(false);
    }

    function enhancePostcodes(root) {
        root.querySelectorAll('input[name="billing_postcode"], input[name="shipping_postcode"]').forEach(function (input) {
            var holder = root.querySelector('[data-error-for="' + input.name + '"]');

            function validate(showMessage) {
                input.value = digits(input.value).slice(0, 10);
                var valid = /^\d{10}$/.test(input.value) && !isObviousPattern(input.value);
                var message = valid ? '' : 'کد پستی باید ۱۰ رقم معتبر و غیرتکراری باشد.';
                input.setCustomValidity(message);
                if (holder && (showMessage || valid)) holder.textContent = message;
            }

            input.addEventListener('input', function () { validate(false); });
            input.addEventListener('blur', function () { validate(true); });
            validate(false);
        });
    }

    function init(root) {
        if (root.dataset.contactEnhanced === '1') return;
        root.dataset.contactEnhanced = '1';
        enhanceOptionalSection(root);
        enhanceMobileDisplay(root);
        enhanceLandline(root, 'billing');
        enhanceLandline(root, 'shipping');
        enhancePostcodes(root);
    }

    function start() {
        document.querySelectorAll('[data-dede-profile]').forEach(init);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
}());
