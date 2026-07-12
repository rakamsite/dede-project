(function () {
    'use strict';

    const config = window.DedeStoreFeatures || {};

    function request(formData) {
        return fetch(config.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData,
        }).then(async function (response) {
            const payload = await response.json().catch(function () {
                return null;
            });
            if (!payload) {
                throw new Error(config.messages?.genericError || 'خطای نامشخص');
            }
            return payload;
        });
    }

    function installLegacyAccountType() {
        const legacy = document.querySelector('#AfterSuccessRegister');
        if (!legacy || !config.accountTypeHtml || legacy.querySelector('[data-dede-account-type]')) {
            return;
        }
        legacy.innerHTML = config.accountTypeHtml;
    }

    function initAccountType(root) {
        if (root.dataset.initialized === '1') {
            return;
        }
        root.dataset.initialized = '1';

        const options = Array.from(root.querySelectorAll('[data-account-type]'));
        const submit = root.querySelector('.dede-account-type__submit');
        const error = root.querySelector('[data-account-type-error]');
        const mode = root.dataset.accountTypeMode || 'create';
        const currentType = root.dataset.currentType || '';
        const action = root.dataset.accountTypeAction || config.accountTypeAction || 'dede_store_select_account_type';
        const checked = root.querySelector('input[type="radio"]:checked');
        let selected = checked ? checked.value : '';

        function syncSubmit() {
            if (!submit) {
                return;
            }
            submit.disabled = !selected || ('change' === mode && selected === currentType);
        }

        options.forEach(function (option) {
            option.addEventListener('click', function () {
                selected = option.dataset.accountType || '';
                options.forEach(function (item) {
                    const active = item === option;
                    item.classList.toggle('is-selected', active);
                    item.setAttribute('aria-checked', active ? 'true' : 'false');
                    const radio = item.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = active;
                    }
                });
                syncSubmit();
                if (error) {
                    error.textContent = '';
                }
            });
        });

        syncSubmit();
        if (!submit) {
            return;
        }

        submit.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (!selected || submit.disabled) {
                return;
            }

            submit.disabled = true;
            submit.classList.add('is-loading');
            if (error) {
                error.textContent = '';
            }

            const data = new FormData();
            data.append('action', action);
            data.append('nonce', config.accountTypeNonce || '');
            data.append('select_type', selected);
            data.append('redirect_url', window.location.href);

            request(data).then(function (response) {
                if (!response.success) {
                    throw new Error(response.data?.message || config.messages?.genericError);
                }
                window.location.replace(response.data?.redirect || '/my-account');
            }).catch(function (exception) {
                if (error) {
                    error.textContent = exception.message || config.messages?.genericError;
                }
                syncSubmit();
            }).finally(function () {
                submit.classList.remove('is-loading');
            });
        });
    }

    function initProfile(root) {
        const form = root.querySelector('.dede-profile__form');
        if (!form) {
            return;
        }

        const panels = Array.from(root.querySelectorAll('[data-step]'));
        const stepButtons = Array.from(root.querySelectorAll('[data-step-target]'));
        const next = root.querySelector('[data-next]');
        const previous = root.querySelector('[data-previous]');
        const submit = root.querySelector('[data-submit]');
        const mobileStep = root.querySelector('[data-mobile-step]');
        const mobileTitle = root.querySelector('[data-mobile-title]');
        const mobileProgress = root.querySelector('[data-mobile-progress]');
        const message = root.querySelector('[data-form-message]');
        const sameAddress = form.elements.namedItem('same_as_billing');
        const shippingBlock = root.querySelector('[data-shipping-fields]');
        const role = root.dataset.accountRole || 'personal';
        const identifierLabel = root.dataset.identifierLabel || ('company' === role ? 'شناسه ملی' : 'کد ملی');
        const sameAddressLabel = root.dataset.sameAddressLabel || 'همان آدرس اصلی';
        const cityMapElement = root.querySelector('[data-dede-city-map]');
        let citiesByState = {};
        let currentStep = Math.max(1, Math.min(3, Number(root.dataset.startStep || 1)));

        if (cityMapElement) {
            try {
                citiesByState = JSON.parse(cityMapElement.textContent || '{}') || {};
            } catch (exception) {
                citiesByState = {};
            }
        }

        function field(name) {
            return form.elements.namedItem(name);
        }

        function value(name) {
            const item = field(name);
            return item ? String(item.value || '').trim() : '';
        }

        function selectedText(name) {
            const item = field(name);
            if (!item || item.tagName !== 'SELECT' || item.selectedIndex < 0) {
                return '';
            }
            return item.options[item.selectedIndex]?.text || '';
        }

        function clearErrors() {
            root.querySelectorAll('[data-error-for]').forEach(function (item) {
                item.textContent = '';
            });
            root.querySelectorAll('.has-error').forEach(function (item) {
                item.classList.remove('has-error');
            });
            if (message) {
                message.textContent = '';
                message.className = 'dede-profile__notice';
            }
        }

        function showErrors(errors) {
            let first = null;
            Object.keys(errors || {}).forEach(function (name) {
                const holder = root.querySelector('[data-error-for="' + CSS.escape(name) + '"]');
                const input = field(name);
                if (holder) {
                    holder.textContent = errors[name];
                }
                if (input) {
                    input.closest('.dede-field')?.classList.add('has-error');
                    if (!first) {
                        first = input;
                    }
                }
            });
            if (first) {
                const panel = first.closest('[data-step]');
                if (panel) {
                    setStep(Number(panel.dataset.step || 1));
                }
                first.scrollIntoView({behavior: 'smooth', block: 'center'});
                first.focus({preventScroll: true});
            }
        }

        function panelIsValid(step) {
            clearErrors();
            const panel = panels.find(function (item) {
                return Number(item.dataset.step) === step;
            });
            if (!panel) {
                return true;
            }

            const controls = Array.from(panel.querySelectorAll('input, select, textarea')).filter(function (item) {
                return !item.disabled && item.required;
            });
            for (const control of controls) {
                if (!control.checkValidity()) {
                    const holder = root.querySelector('[data-error-for="' + CSS.escape(control.name) + '"]');
                    if (holder) {
                        holder.textContent = config.messages?.required || 'تکمیل این فیلد الزامی است.';
                    }
                    control.closest('.dede-field')?.classList.add('has-error');
                    control.scrollIntoView({behavior: 'smooth', block: 'center'});
                    control.focus({preventScroll: true});
                    return false;
                }
            }
            return true;
        }

        function updateReview() {
            const company = value('company_name');
            const store = value('store_name');
            const fullName = [value('first_name'), value('last_name')].filter(Boolean).join(' ');
            const identity = company || (store ? store + (fullName ? ' — ' + fullName : '') : fullName);
            const identifier = 'company' === role ? value('national_id') : value('national_code');
            const billing = [selectedText('billing_state'), selectedText('billing_city'), value('billing_address_1')].filter(function (item) {
                return item && item.indexOf('انتخاب') !== 0;
            }).join('، ');
            const shipping = sameAddress?.checked
                ? sameAddressLabel
                : [selectedText('shipping_state'), selectedText('shipping_city'), value('shipping_address_1')].filter(function (item) {
                    return item && item.indexOf('انتخاب') !== 0;
                }).join('، ');

            const identityNode = root.querySelector('[data-review="identity"]');
            const identifierNode = root.querySelector('[data-review="identifier"]');
            const billingNode = root.querySelector('[data-review="billing"]');
            const shippingNode = root.querySelector('[data-review="shipping"]');
            if (identityNode) identityNode.textContent = identity || '—';
            if (identifierNode) identifierNode.textContent = identifier ? identifierLabel + ': ' + identifier : '';
            if (billingNode) billingNode.textContent = billing || '—';
            if (shippingNode) shippingNode.textContent = shipping || '—';
        }

        function setStep(step) {
            currentStep = Math.max(1, Math.min(3, step));
            panels.forEach(function (panel) {
                panel.hidden = Number(panel.dataset.step) !== currentStep;
            });
            stepButtons.forEach(function (button) {
                const target = Number(button.dataset.stepTarget || 0);
                button.classList.toggle('is-active', target === currentStep);
                button.classList.toggle('is-done', target < currentStep);
                button.setAttribute('aria-current', target === currentStep ? 'step' : 'false');
            });

            if (previous) previous.hidden = currentStep === 1;
            if (next) next.hidden = currentStep === 3;
            if (submit) submit.hidden = currentStep !== 3;

            const titles = {1: 'مشخصات', 2: 'آدرس', 3: 'تأیید'};
            if (mobileStep) mobileStep.textContent = 'مرحله ' + currentStep + ' از ۳';
            if (mobileTitle) mobileTitle.textContent = titles[currentStep];
            if (mobileProgress) mobileProgress.value = currentStep;
            if (currentStep === 3) updateReview();
            root.dataset.currentStep = String(currentStep);
        }

        function toggleShipping() {
            const same = Boolean(sameAddress?.checked);
            shippingBlock?.classList.toggle('is-collapsed', same);
            shippingBlock?.querySelectorAll('input, select, textarea').forEach(function (item) {
                item.disabled = same;
            });
        }

        function fillCities(citySelect, cities, currentValue) {
            citySelect.innerHTML = '<option value="">انتخاب شهر</option>';
            (cities || []).forEach(function (city) {
                const option = document.createElement('option');
                option.value = city.id;
                option.textContent = city.name;
                option.selected = String(currentValue || '') === String(city.id);
                citySelect.appendChild(option);
            });
        }

        function loadCities(scope, state, currentValue) {
            const citySelect = root.querySelector('[data-city-select="' + scope + '"]');
            if (!citySelect) {
                return;
            }

            if (!state) {
                fillCities(citySelect, [], '');
                return;
            }

            if (Object.prototype.hasOwnProperty.call(citiesByState, state)) {
                fillCities(citySelect, citiesByState[state], currentValue);
                citySelect.disabled = Boolean(sameAddress?.checked && scope === 'shipping');
                return;
            }

            citySelect.disabled = true;
            fillCities(citySelect, [], '');
            const data = new FormData();
            data.append('action', config.citiesAction || 'dede_store_get_cities');
            data.append('nonce', config.profileNonce || '');
            data.append('state', state);

            request(data).then(function (response) {
                if (!response.success) {
                    throw new Error(response.data?.message || config.messages?.genericError);
                }
                const cities = response.data?.cities || [];
                citiesByState[state] = cities;
                fillCities(citySelect, cities, currentValue);
            }).catch(function () {
                citySelect.innerHTML = '<option value="">دریافت فهرست شهرها ناموفق بود</option>';
            }).finally(function () {
                if (!(sameAddress?.checked && scope === 'shipping')) {
                    citySelect.disabled = false;
                }
            });
        }

        root.querySelectorAll('[data-state-select]').forEach(function (stateSelect) {
            stateSelect.addEventListener('change', function () {
                loadCities(stateSelect.dataset.stateSelect, stateSelect.value, '');
            });
        });

        sameAddress?.addEventListener('change', function () {
            toggleShipping();
            updateReview();
        });

        next?.addEventListener('click', function () {
            if (panelIsValid(currentStep)) {
                setStep(currentStep + 1);
                root.scrollIntoView({behavior: 'smooth', block: 'start'});
            }
        });

        previous?.addEventListener('click', function () {
            setStep(currentStep - 1);
            root.scrollIntoView({behavior: 'smooth', block: 'start'});
        });

        stepButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const target = Number(button.dataset.stepTarget || 1);
                if (target <= currentStep || root.dataset.complete === '1') {
                    setStep(target);
                }
            });
        });

        const modal = root.querySelector('[data-account-type-modal]');
        function openAccountTypeModal() {
            if (!modal) {
                return;
            }
            modal.hidden = false;
            document.documentElement.classList.add('dede-modal-open');
            modal.querySelector('[data-account-type]')?.querySelector('[data-account-type]')?.focus();
        }
        function closeAccountTypeModal() {
            if (!modal) {
                return;
            }
            modal.hidden = true;
            document.documentElement.classList.remove('dede-modal-open');
        }

        root.querySelectorAll('[data-open-account-type]').forEach(function (button) {
            button.addEventListener('click', openAccountTypeModal);
        });
        modal?.querySelectorAll('[data-close-account-type]').forEach(function (button) {
            button.addEventListener('click', closeAccountTypeModal);
        });
        modal?.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeAccountTypeModal();
            }
        });
        document.addEventListener('keydown', function (event) {
            if ('Escape' === event.key && modal && !modal.hidden) {
                closeAccountTypeModal();
            }
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            clearErrors();
            if (!submit) {
                return;
            }
            submit.disabled = true;
            submit.classList.add('is-loading');

            const data = new FormData(form);
            request(data).then(function (response) {
                if (!response.success) {
                    showErrors(response.data?.errors || {});
                    throw new Error(response.data?.message || config.messages?.genericError);
                }

                root.dataset.complete = '1';
                root.querySelector('[data-profile-status]')?.classList.remove('is-incomplete');
                root.querySelector('[data-profile-status]')?.classList.add('is-complete');
                const statusText = root.querySelector('[data-profile-status-text]');
                if (statusText) statusText.textContent = 'اطلاعات کامل است';
                if (message) {
                    message.textContent = response.data?.message || config.messages?.saved || 'اطلاعات ذخیره شد.';
                    message.classList.add('is-success');
                }
                updateReview();
                setStep(3);
                document.body.dispatchEvent(new CustomEvent('dede:profile-saved', {detail: response.data || {}}));
                if (root.dataset.context === 'checkout') {
                    if (window.jQuery) {
                        window.jQuery(document.body).trigger('update_checkout');
                    }
                    window.setTimeout(function () {
                        document.querySelector('form.checkout')?.scrollIntoView({behavior: 'smooth', block: 'start'});
                    }, 350);
                }
            }).catch(function (exception) {
                if (message && !message.textContent) {
                    message.textContent = exception.message || config.messages?.genericError;
                    message.classList.add('is-error');
                }
            }).finally(function () {
                submit.disabled = false;
                submit.classList.remove('is-loading');
            });
        });

        toggleShipping();
        setStep(currentStep);
    }

    document.addEventListener('DOMContentLoaded', function () {
        installLegacyAccountType();
        document.querySelectorAll('[data-dede-account-type]').forEach(initAccountType);
        document.querySelectorAll('[data-dede-profile]').forEach(initProfile);
    });
}());
