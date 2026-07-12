(function () {
    'use strict';

    const config = window.DedeStoreFeatures || {};
    const digitMap = {
        '۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4',
        '۵': '5', '۶': '6', '۷': '7', '۸': '8', '۹': '9',
        '٠': '0', '١': '1', '٢': '2', '٣': '3', '٤': '4',
        '٥': '5', '٦': '6', '٧': '7', '٨': '8', '٩': '9'
    };
    const numericFields = new Set([
        'national_code', 'national_id', 'economic_code', 'telegram',
        'billing_postcode', 'billing_phone', 'shipping_postcode', 'shipping_phone'
    ]);
    const legacyStateClasses = [
        'bg-red-50', 'border-red-500', 'text-red-900', 'placeholder-red-700',
        'focus:ring-red-500', 'focus:border-red-500'
    ];

    function normalizeDigits(value) {
        return String(value || '').replace(/[۰-۹٠-٩]/g, function (digit) {
            return digitMap[digit] || digit;
        });
    }

    function cleanOptionalEmailState(input) {
        if (input.name !== 'email' || String(input.value || '').trim() !== '') {
            return;
        }
        legacyStateClasses.forEach(function (className) {
            input.classList.remove(className);
        });
        input.closest('.dede-field')?.classList.remove('has-error');
        const error = input.closest('[data-dede-profile]')?.querySelector('[data-error-for="email"]');
        if (error) {
            error.textContent = '';
        }
    }

    function initGuard(root) {
        const profileForm = root.querySelector('.dede-profile__form');
        if (!profileForm) {
            return;
        }

        let dirty = false;

        function markDirty(event) {
            const target = event.target;
            if (!(target instanceof HTMLInputElement || target instanceof HTMLSelectElement || target instanceof HTMLTextAreaElement)) {
                return;
            }

            if (numericFields.has(target.name)) {
                const normalized = normalizeDigits(target.value);
                if (normalized !== target.value) {
                    target.value = normalized;
                }
            }

            cleanOptionalEmailState(target);
            dirty = true;
            root.dataset.dirty = '1';
        }

        profileForm.addEventListener('input', markDirty);
        profileForm.addEventListener('change', markDirty);
        profileForm.addEventListener('blur', function (event) {
            window.setTimeout(function () {
                cleanOptionalEmailState(event.target);
            }, 0);
        }, true);

        document.body.addEventListener('dede:profile-saved', function () {
            dirty = false;
            root.dataset.dirty = '0';
        });

        if (root.dataset.context !== 'checkout') {
            return;
        }

        document.addEventListener('submit', function (event) {
            if (!(event.target instanceof HTMLFormElement) || !event.target.matches('form.checkout') || !dirty) {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();

            const reviewButton = root.querySelector('[data-step-target="3"]');
            if (reviewButton && root.dataset.complete === '1') {
                reviewButton.click();
            }

            const message = root.querySelector('[data-form-message]');
            if (message) {
                message.textContent = config.messages?.unsavedCheckout
                    || 'تغییرات اطلاعات شما هنوز ذخیره نشده است. ابتدا اطلاعات را ذخیره کنید.';
                message.classList.remove('is-success');
                message.classList.add('is-error');
            }

            root.scrollIntoView({behavior: 'smooth', block: 'start'});
        }, true);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-dede-profile]').forEach(initGuard);
    });
}());
