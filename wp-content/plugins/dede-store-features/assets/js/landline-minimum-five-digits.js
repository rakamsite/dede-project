(function () {
    'use strict';

    var validAreaCodes = [
        '11', '13', '17', '21', '23', '24', '25', '26', '28',
        '31', '34', '35', '38', '41', '44', '45', '51', '54',
        '56', '58', '61', '66', '71', '74', '76', '77', '81',
        '83', '84', '86', '87'
    ];

    function digits(value) {
        return String(value || '')
            .replace(/[۰-۹]/g, function (digit) { return String('۰۱۲۳۴۵۶۷۸۹'.indexOf(digit)); })
            .replace(/[٠-٩]/g, function (digit) { return String('٠١٢٣٤٥٦٧٨٩'.indexOf(digit)); })
            .replace(/\D+/g, '');
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

    function snapshotOriginalValues() {
        document.querySelectorAll('[data-dede-profile]').forEach(function (root) {
            ['billing', 'shipping'].forEach(function (scope) {
                var original = root.querySelector('input[name="' + scope + '_phone"]');
                if (original && !original.dataset.dedeOriginalLandline) {
                    original.dataset.dedeOriginalLandline = digits(original.value);
                }
            });
        });
    }

    function configureLandline(root, scope) {
        var original = root.querySelector('input[name="' + scope + '_phone"]');
        var areaInput = root.querySelector('input[name="' + scope + '_phone_area"]');
        var numberInput = root.querySelector('input[name="' + scope + '_phone_number"]');
        var stateSelect = root.querySelector('select[name="' + scope + '_state"]');
        if (!original || !areaInput || !numberInput || numberInput.dataset.minimumFiveReady === '1') {
            return;
        }

        var saved = digits(original.dataset.dedeOriginalLandline || original.value);
        if (/^0\d{2}\d{5,}$/.test(saved)) {
            areaInput.value = saved.slice(1, 3);
            numberInput.value = saved.slice(3);
        }

        numberInput.removeAttribute('maxlength');
        numberInput.setAttribute('minlength', '5');
        numberInput.setAttribute('aria-label', 'شماره تلفن ثابت با حداقل پنج رقم');
        numberInput.dataset.minimumFiveReady = '1';
        numberInput.dataset.fullLandlineNumber = digits(numberInput.value);

        var areaHolder = root.querySelector('[data-error-for="' + scope + '_phone_area"]');
        var numberHolder = root.querySelector('[data-error-for="' + scope + '_phone_number"]');
        var field = original.closest('.dede-field');

        function sync() {
            areaInput.value = digits(areaInput.value).slice(0, 2);
            numberInput.value = digits(numberInput.value);
            numberInput.dataset.fullLandlineNumber = numberInput.value;
            original.value = areaInput.value || numberInput.value
                ? '0' + areaInput.value + numberInput.value
                : '';
        }

        function validate(force) {
            sync();
            var area = areaInput.value;
            var number = numberInput.value;
            var areaComplete = area.length === 2;
            var numberComplete = number.length >= 5;
            var areaValid = areaComplete && validAreaCodes.indexOf(area) !== -1;
            var numberValid = numberComplete && !isObviousPattern(number);
            var areaMessage = (force || areaComplete) && !areaValid ? 'کد شهر معتبر نیست.' : '';
            var numberMessage = (force || numberComplete) && !numberValid
                ? 'شماره تلفن باید حداقل ۵ رقم معتبر باشد.'
                : '';

            areaInput.setCustomValidity(areaMessage);
            numberInput.setCustomValidity(numberMessage);
            if (areaHolder) areaHolder.textContent = areaMessage;
            if (numberHolder) numberHolder.textContent = numberMessage;
            if (field) field.classList.toggle('has-error', Boolean(areaMessage || numberMessage));
            return areaValid && numberValid;
        }

        function interceptInput(event) {
            event.stopImmediatePropagation();
            sync();
            validate(false);
        }

        function interceptAreaInput(event) {
            areaInput.dataset.suggested = '';
            interceptInput(event);
        }

        function interceptBlur(event) {
            event.stopImmediatePropagation();
            event.currentTarget.dataset.touched = '1';
            validate(true);
        }

        function interceptInvalid(event) {
            event.stopImmediatePropagation();
            validate(true);
        }

        areaInput.addEventListener('input', interceptAreaInput, true);
        numberInput.addEventListener('input', interceptInput, true);
        areaInput.addEventListener('blur', interceptBlur, true);
        numberInput.addEventListener('blur', interceptBlur, true);
        areaInput.addEventListener('invalid', interceptInvalid, true);
        numberInput.addEventListener('invalid', interceptInvalid, true);

        areaInput._dedeValidate = validate;
        numberInput._dedeValidate = validate;

        if (stateSelect) {
            stateSelect.addEventListener('change', function () {
                var preservedNumber = numberInput.dataset.fullLandlineNumber || numberInput.value;
                window.setTimeout(function () {
                    numberInput.removeAttribute('maxlength');
                    numberInput.setAttribute('minlength', '5');
                    numberInput.value = preservedNumber;
                    sync();
                    validate(false);
                }, 0);
            });
        }

        sync();
    }

    function init() {
        document.querySelectorAll('[data-dede-profile]').forEach(function (root) {
            configureLandline(root, 'billing');
            configureLandline(root, 'shipping');
        });
    }

    snapshotOriginalValues();
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            window.setTimeout(init, 0);
        });
    } else {
        window.setTimeout(init, 0);
    }
}());
