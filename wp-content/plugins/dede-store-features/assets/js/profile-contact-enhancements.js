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

    function isPersianText(value) {
        value = String(value || '').trim();
        return value !== ''
            && !/[0-9۰-۹٠-٩]/u.test(value)
            && /^[\u0600-\u06FF\u200C\s\-\.\(\)]+$/u.test(value);
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

    function isValidNationalCode(value) {
        var code = digits(value);
        if (!/^\d{10}$/.test(code) || isObviousPattern(code)) return false;

        var sum = 0;
        for (var index = 0; index < 9; index += 1) {
            sum += Number(code.charAt(index)) * (10 - index);
        }
        var remainder = sum % 11;
        var expected = remainder < 2 ? remainder : 11 - remainder;
        return Number(code.charAt(9)) === expected;
    }

    function isValidNationalId(value) {
        var id = digits(value);
        if (!/^\d{11}$/.test(id) || /^(\d)\1{10}$/.test(id) || Number(id.slice(3, 9)) === 0) {
            return false;
        }

        var coefficients = [29, 27, 23, 19, 17, 29, 27, 23, 19, 17];
        var decimal = Number(id.charAt(9)) + 2;
        var sum = 0;
        for (var index = 0; index < 10; index += 1) {
            sum += (Number(id.charAt(index)) + decimal) * coefficients[index];
        }
        var remainder = sum % 11;
        return Number(id.charAt(10)) === (remainder === 10 ? 0 : remainder);
    }

    function normalizeMobile(value) {
        var valueDigits = digits(value);
        if (valueDigits.indexOf('0098') === 0) valueDigits = valueDigits.slice(2);
        if (/^09\d{9}$/.test(valueDigits)) valueDigits = '98' + valueDigits.slice(1);
        if (/^9\d{9}$/.test(valueDigits)) valueDigits = '98' + valueDigits;
        return /^989\d{9}$/.test(valueDigits) ? valueDigits : '';
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

    function errorHolder(root, name) {
        return root.querySelector('[data-error-for="' + name + '"]');
    }

    function setFieldError(root, input, message, showMessage) {
        if (!input) return;
        input.setCustomValidity(message || '');

        var field = input.closest('.dede-field');
        var holder = input.name ? errorHolder(root, input.name) : null;
        if (!message) {
            if (holder) holder.textContent = '';
            if (field) field.classList.remove('has-error');
            return;
        }

        if (showMessage) {
            if (holder) holder.textContent = message;
            if (field) field.classList.add('has-error');
        }
    }

    function enhanceOptionalSection(root) {
        var details = root.querySelector('.dede-profile__optional');
        if (!details) return;

        var summary = details.querySelector('summary');
        if (summary) {
            var title = summary.querySelector(':scope > span');
            if (title) {
                title.classList.add('dede-profile__optional-title');
                if (!title.querySelector('.dede-profile__optional-toggle')) {
                    var toggle = document.createElement('i');
                    toggle.className = 'dede-profile__optional-toggle';
                    toggle.setAttribute('aria-hidden', 'true');
                    title.appendChild(toggle);
                }
            }

            var oldMeta = summary.querySelector('.dede-profile__optional-meta');
            if (oldMeta) {
                var oldBadge = oldMeta.querySelector('small');
                if (oldBadge) summary.appendChild(oldBadge);
                oldMeta.remove();
            }
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

    function enhanceCompanyNationalId(root) {
        if (root.dataset.accountRole !== 'company') return;

        var form = root.querySelector('.dede-profile__form');
        var nationalId = form && form.elements.namedItem('national_id');
        if (!form || !nationalId) return;

        var nationalCode = form.elements.namedItem('national_code');
        if (!nationalCode) {
            nationalCode = document.createElement('input');
            nationalCode.type = 'hidden';
            nationalCode.name = 'national_code';
            form.appendChild(nationalCode);
        }

        function sync() {
            nationalId.value = digits(nationalId.value).slice(0, 11);
            nationalCode.value = nationalId.value;
        }

        nationalId.addEventListener('input', sync);
        nationalId.addEventListener('change', sync);
        sync();
    }

    function enhanceAccountTypeRules(root) {
        var role = root.dataset.accountRole || '';
        var modal = root.querySelector('[data-account-type-modal]');
        var openers = root.querySelectorAll('[data-open-account-type]');

        if (role === 'company') {
            root.classList.add('dede-profile--account-type-locked');
            openers.forEach(function (opener) {
                opener.removeAttribute('data-open-account-type');
                opener.setAttribute('aria-disabled', 'true');
                opener.classList.add('dede-account-type-change-locked');
                if (opener.classList.contains('dede-profile__account-type')) {
                    opener.disabled = true;
                    var change = opener.querySelector('b');
                    if (change) change.remove();
                } else {
                    opener.hidden = true;
                }
            });
            if (modal) modal.remove();
            return;
        }

        if (role === 'personal' || role === 'store') {
            var selector = modal && modal.querySelector('.dede-account-type[data-account-type-mode="change"]');
            var companyOption = selector && selector.querySelector('[data-account-type="company"]');
            if (companyOption) companyOption.remove();
            if (selector) selector.classList.add('dede-account-type--personal-store-only');
        }
    }

    function createErrorHolder(name) {
        var holder = document.createElement('small');
        holder.className = 'dede-landline__error';
        holder.setAttribute('data-error-for', name);
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
        original.dataset.progressiveSkip = '1';

        var wrapper = document.createElement('div');
        wrapper.className = 'dede-landline';
        wrapper.innerHTML =
            '<span class="dede-landline__hint">کد شهر</span>' +
            '<span class="dede-landline__zero" aria-hidden="true">0</span>' +
            '<input class="dede-landline__area" type="text" inputmode="numeric" maxlength="2" minlength="2" autocomplete="off" required ' +
                'name="' + scope + '_phone_area" aria-label="کد شهر بدون صفر" placeholder="21">' +
            '<span class="dede-landline__separator" aria-hidden="true">-</span>' +
            '<input class="dede-landline__number" type="text" inputmode="numeric" maxlength="8" minlength="8" autocomplete="tel-national" required ' +
                'name="' + scope + '_phone_number" aria-label="شماره تلفن ثابت هشت رقمی">';

        original.insertAdjacentElement('afterend', wrapper);
        var areaInput = wrapper.querySelector('[name="' + scope + '_phone_area"]');
        var numberInput = wrapper.querySelector('[name="' + scope + '_phone_number"]');
        var messages = document.createElement('div');
        messages.className = 'dede-landline__messages';
        messages.appendChild(createErrorHolder(scope + '_phone_area'));
        messages.appendChild(createErrorHolder(scope + '_phone_number'));
        wrapper.insertAdjacentElement('afterend', messages);

        var areaHolder = messages.querySelector('[data-error-for="' + scope + '_phone_area"]');
        var numberHolder = messages.querySelector('[data-error-for="' + scope + '_phone_number"]');
        areaInput.value = initialArea;
        numberInput.value = initialNumber;
        areaInput.dataset.progressiveSkip = '1';
        numberInput.dataset.progressiveSkip = '1';

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

        function validate(force) {
            sync();
            var area = areaInput.value;
            var number = numberInput.value;
            var areaComplete = area.length === 2;
            var numberComplete = number.length === 8;
            var areaValid = areaComplete && validAreaCodes.indexOf(area) !== -1;
            var numberValid = numberComplete && !isObviousPattern(number);
            var areaMessage = (force || areaComplete) && !areaValid ? 'کد شهر معتبر نیست.' : '';
            var numberMessage = (force || numberComplete) && !numberValid ? 'شماره تلفن باید ۸ رقم معتبر باشد.' : '';

            areaInput.setCustomValidity(areaMessage);
            numberInput.setCustomValidity(numberMessage);
            areaHolder.textContent = areaMessage;
            numberHolder.textContent = numberMessage;
            var field = original.closest('.dede-field');
            if (field) field.classList.toggle('has-error', Boolean(areaMessage || numberMessage));
            return !areaMessage && !numberMessage && areaValid && numberValid;
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
        areaInput.addEventListener('blur', function () { areaInput.dataset.touched = '1'; validate(true); });
        numberInput.addEventListener('blur', function () { numberInput.dataset.touched = '1'; validate(true); });
        areaInput.addEventListener('invalid', function () { validate(true); });
        numberInput.addEventListener('invalid', function () { validate(true); });
        stateSelect && stateSelect.addEventListener('change', function () { applySuggestion(false); });
        wrapper.addEventListener('click', function (event) {
            if (event.target === areaInput || event.target === numberInput) return;
            if (areaInput.value.length < 2) areaInput.focus();
            else numberInput.focus();
        });

        if (!initialArea) applySuggestion(true);
        sync();
    }

    function enhancePostcodes(root) {
        root.querySelectorAll('input[name="billing_postcode"], input[name="shipping_postcode"]').forEach(function (input) {
            var holder = errorHolder(root, input.name);
            input.dataset.progressiveSkip = '1';

            function validate(force) {
                input.value = digits(input.value).slice(0, 10);
                var complete = input.value.length === 10;
                var valid = complete && !isObviousPattern(input.value);
                var message = (force || complete) && !valid ? 'کد پستی باید ۱۰ رقم معتبر و غیرتکراری باشد.' : '';
                input.setCustomValidity(message);
                if (holder) holder.textContent = message;
                var field = input.closest('.dede-field');
                if (field) field.classList.toggle('has-error', Boolean(message));
                return valid;
            }

            input.addEventListener('input', function () { validate(false); });
            input.addEventListener('blur', function () { input.dataset.touched = '1'; validate(true); });
            input.addEventListener('invalid', function () { validate(true); });
            input._dedeValidate = validate;
        });
    }

    function validateBirthday(root, input, showMessage) {
        var form = input.form;
        var day = form && form.elements.namedItem('birthday_day');
        var month = form && form.elements.namedItem('birthday_month');
        var year = form && form.elements.namedItem('birthday_year');
        if (!day || !month || !year) return true;

        var values = [day.value, month.value, year.value];
        var filled = values.filter(Boolean).length;
        var message = filled === 0 || filled === 3 ? '' : 'تاریخ تولد را کامل انتخاب کنید.';
        [day, month, year].forEach(function (field) { field.setCustomValidity(message); });
        var holder = errorHolder(root, 'birthday_year');
        if (holder) holder.textContent = showMessage ? message : '';
        var wrapper = year.closest('.dede-field');
        if (wrapper) wrapper.classList.toggle('has-error', Boolean(showMessage && message));
        return !message;
    }

    function validateStandardField(root, input, showMessage) {
        if (!input || input.disabled || input.readOnly || input.type === 'hidden' || input.dataset.progressiveSkip === '1') {
            return true;
        }

        var name = input.name || '';
        var value = String(input.value || '').trim();
        var message = '';
        var valid = true;

        if (name === 'birthday_day' || name === 'birthday_month' || name === 'birthday_year') {
            return validateBirthday(root, input, showMessage);
        }

        if (input.tagName === 'SELECT') {
            if (input.required && !value) message = 'انتخاب این گزینه الزامی است.';
        } else if (input.required && !value) {
            message = 'تکمیل این فیلد الزامی است.';
        } else if (value) {
            if (['first_name', 'last_name', 'company_name', 'store_name'].indexOf(name) !== -1 && !isPersianText(value)) {
                message = 'این مقدار را به فارسی وارد کنید.';
            } else if (name === 'email') {
                input.setCustomValidity('');
                if (input.validity.typeMismatch) message = 'فرمت ایمیل صحیح نیست.';
            } else if (name === 'telegram' && !normalizeMobile(value)) {
                message = 'شماره تلگرام را مانند شماره همراه وارد کنید.';
            } else if (name === 'national_code') {
                input.value = digits(value).slice(0, 10);
                if (input.value.length === 10 && !isValidNationalCode(input.value)) message = 'کد ملی معتبر نیست.';
                else if (showMessage && input.value.length !== 10) message = 'کد ملی باید ۱۰ رقم باشد.';
            } else if (name === 'national_id') {
                input.value = digits(value).slice(0, 11);
                if (input.value.length === 11 && !isValidNationalId(input.value)) message = 'شناسه ملی معتبر نیست.';
                else if (showMessage && input.value.length !== 11) message = 'شناسه ملی باید ۱۱ رقم باشد.';
            } else if ((name === 'billing_address_1' || name === 'shipping_address_1') && value.length < 5) {
                message = 'آدرس کامل را وارد کنید.';
            } else if (name === 'economic_code') {
                input.value = digits(value);
            }
        }

        valid = !message;
        setFieldError(root, input, message, showMessage || (
            (name === 'national_code' && digits(input.value).length === 10) ||
            (name === 'national_id' && digits(input.value).length === 11)
        ));
        return valid;
    }

    function bindProgressiveValidation(root) {
        var form = root.querySelector('.dede-profile__form');
        if (!form) return;

        var controls = Array.from(form.querySelectorAll('input, select, textarea')).filter(function (input) {
            return input.type !== 'hidden' && !input.readOnly && input.dataset.progressiveSkip !== '1';
        });

        controls.forEach(function (input) {
            input.addEventListener('blur', function () {
                input.dataset.touched = '1';
                validateStandardField(root, input, true);
            });
            input.addEventListener('change', function () {
                if (input.tagName === 'SELECT') input.dataset.touched = '1';
                validateStandardField(root, input, input.dataset.touched === '1');
            });
            input.addEventListener('input', function () {
                var length = digits(input.value).length;
                var complete = (input.name === 'national_code' && length === 10)
                    || (input.name === 'national_id' && length === 11);
                validateStandardField(root, input, complete || input.dataset.touched === '1');
            });
            input.addEventListener('invalid', function () {
                input.dataset.touched = '1';
                validateStandardField(root, input, true);
            });
        });

        function validateContainer(container, showMessages) {
            var valid = true;
            var firstInvalid = null;
            container.querySelectorAll('input, select, textarea').forEach(function (input) {
                if (input.disabled || input.type === 'hidden' || input.readOnly) return;

                var fieldValid;
                if (input.dataset.progressiveSkip === '1' && typeof input._dedeValidate === 'function') {
                    fieldValid = input._dedeValidate(showMessages);
                } else if (input.dataset.progressiveSkip === '1') {
                    fieldValid = input.checkValidity();
                    if (!fieldValid) input.dispatchEvent(new Event('invalid'));
                } else {
                    fieldValid = validateStandardField(root, input, showMessages) && input.checkValidity();
                }
                if (!fieldValid && !firstInvalid) firstInvalid = input;
                valid = valid && fieldValid;
            });

            if (!valid && firstInvalid) {
                var focusTarget = firstInvalid.closest('.dede-birthday')?.querySelector('[data-birthday-toggle]') || firstInvalid;
                focusTarget.scrollIntoView({behavior: 'smooth', block: 'center'});
                focusTarget.focus({preventScroll: true});
            }
            return valid;
        }

        root.addEventListener('click', function (event) {
            var next = event.target.closest && event.target.closest('[data-next]');
            if (!next) return;
            var panel = next.closest('.dede-profile')?.querySelector('[data-step]:not([hidden])');
            if (panel && !validateContainer(panel, true)) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        }, true);

        form.addEventListener('submit', function (event) {
            if (!validateContainer(form, true)) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        }, true);
    }

    function init(root) {
        if (root.dataset.contactEnhanced === '1') return;
        root.dataset.contactEnhanced = '1';
        enhanceOptionalSection(root);
        enhanceMobileDisplay(root);
        enhanceCompanyNationalId(root);
        enhanceAccountTypeRules(root);
        enhanceLandline(root, 'billing');
        enhanceLandline(root, 'shipping');
        enhancePostcodes(root);
        bindProgressiveValidation(root);
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
