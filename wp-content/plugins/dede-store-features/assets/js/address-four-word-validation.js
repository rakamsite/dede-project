(function () {
    'use strict';

    var ADDRESS_NAMES = ['billing_address_1', 'shipping_address_1'];
    var MESSAGE = 'آدرس خیلی کوتاه است.';

    function wordCount(value) {
        var matches = String(value || '').match(/[A-Za-z0-9\u0600-\u06FF]+/g);
        return matches ? matches.length : 0;
    }

    function errorHolder(root, input) {
        return root.querySelector('[data-error-for="' + input.name + '"]');
    }

    function validate(root, input, showMessage) {
        if (!input || input.disabled) {
            return true;
        }

        var valid = wordCount(input.value) >= 4;
        var message = valid ? '' : MESSAGE;
        input.setCustomValidity(message);

        var holder = errorHolder(root, input);
        var field = input.closest('.dede-field');
        var shouldShow = Boolean(showMessage && message);

        if (holder) {
            holder.textContent = shouldShow ? message : '';
        }
        if (field) {
            field.classList.toggle('has-error', shouldShow);
        }

        return valid;
    }

    function focusInvalid(input) {
        input.scrollIntoView({behavior: 'smooth', block: 'center'});
        input.focus({preventScroll: true});
    }

    function validateWithin(root, container, showMessages) {
        var firstInvalid = null;

        ADDRESS_NAMES.forEach(function (name) {
            var input = container.querySelector('[name="' + name + '"]');
            if (!input || input.disabled) {
                return;
            }
            if (!validate(root, input, showMessages) && !firstInvalid) {
                firstInvalid = input;
            }
        });

        if (firstInvalid && showMessages) {
            focusInvalid(firstInvalid);
        }

        return !firstInvalid;
    }

    function init(root) {
        if (root.dataset.addressFourWordValidation === '1') {
            return;
        }
        root.dataset.addressFourWordValidation = '1';

        var form = root.querySelector('.dede-profile__form');
        if (!form) {
            return;
        }

        ADDRESS_NAMES.forEach(function (name) {
            var input = form.elements.namedItem(name);
            if (!input) {
                return;
            }

            input.addEventListener('blur', function () {
                input.dataset.addressTouched = '1';
                validate(root, input, true);
            });

            input.addEventListener('input', function () {
                var touched = input.dataset.addressTouched === '1';
                validate(root, input, touched);
            });

            input.addEventListener('invalid', function () {
                input.dataset.addressTouched = '1';
                validate(root, input, true);
            });
        });

        root.addEventListener('click', function (event) {
            var next = event.target.closest && event.target.closest('[data-next]');
            if (!next) {
                return;
            }

            var panel = next.closest('.dede-profile')?.querySelector('[data-step]:not([hidden])');
            if (panel && !validateWithin(root, panel, true)) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        }, true);

        form.addEventListener('submit', function (event) {
            if (!validateWithin(root, form, true)) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        }, true);
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
