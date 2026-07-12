<?php
/**
 * Plugin Name: DeDe Store Features
 * Description: قابلیت‌های مشتری و فروشگاه DeDe شامل نوع حساب، پروفایل مشتری و کنترل اطلاعات Checkout.
 * Version: 1.0.4
 * Author: DeDe
 * Text Domain: dede-store-features
 */

if (!defined('ABSPATH')) {
    exit;
}

define('DEDE_STORE_FEATURES_VERSION', '1.0.4');
define('DEDE_STORE_FEATURES_FILE', __FILE__);
define('DEDE_STORE_FEATURES_PATH', plugin_dir_path(__FILE__));
define('DEDE_STORE_FEATURES_URL', plugin_dir_url(__FILE__));

require_once DEDE_STORE_FEATURES_PATH . 'includes/dede-store-features-national-code-guard.php';
require_once DEDE_STORE_FEATURES_PATH . 'includes/class-dede-store-features-account-type.php';
require_once DEDE_STORE_FEATURES_PATH . 'includes/trait-dede-store-features-validation.php';
require_once DEDE_STORE_FEATURES_PATH . 'includes/trait-dede-store-features-location.php';
require_once DEDE_STORE_FEATURES_PATH . 'includes/class-dede-store-features-profile.php';
require_once DEDE_STORE_FEATURES_PATH . 'includes/class-dede-store-features.php';

function dede_store_features()
{
    return DeDe_Store_Features::instance();
}

function dede_store_features_render_customer_profile($context = 'account')
{
    dede_store_features()->profile()->render($context);
}

function dede_store_features_render_account_type_selector()
{
    dede_store_features()->account_type()->render();
}

function dede_store_features_is_profile_complete($user_id = 0)
{
    return dede_store_features()->profile()->is_complete($user_id);
}

function dede_store_features_prepare_account_type_selection($user_id)
{
    return dede_store_features()->account_type()->prepare_selection($user_id);
}

function dede_store_features_handle_legacy_account_type_request()
{
    dede_store_features()->account_type()->handle_legacy_request();
}

/**
 * Hotfix for the account-type change modal.
 *
 * The theme's global modal styles can override the HTML hidden attribute. This
 * guard also forces change requests to use the authenticated change endpoint,
 * even when an older cached copy of customer-profile.js is still in the browser.
 */
function dede_store_features_account_type_modal_hotfix()
{
    if (is_admin()) {
        return;
    }

    wp_add_inline_style(
        'dede-store-features-customer-profile',
        '.dede-account-type-modal[hidden]{display:none!important;}'
    );

    $script = <<<'JS'
(function () {
    'use strict';

    var config = window.DedeStoreFeatures || {};

    function closeModal(modal) {
        if (!modal) {
            return;
        }
        modal.hidden = true;
        document.documentElement.classList.remove('dede-modal-open');
    }

    function openModal(modal) {
        if (!modal) {
            return;
        }
        modal.hidden = false;
        document.documentElement.classList.add('dede-modal-open');
    }

    document.querySelectorAll('[data-account-type-modal]').forEach(function (modal) {
        modal.hidden = true;
    });

    document.addEventListener('click', function (event) {
        var opener = event.target.closest && event.target.closest('[data-open-account-type]');
        if (opener) {
            var profile = opener.closest('[data-dede-profile]');
            var modalToOpen = profile && profile.querySelector('[data-account-type-modal]');
            if (modalToOpen) {
                event.preventDefault();
                event.stopImmediatePropagation();
                openModal(modalToOpen);
            }
            return;
        }

        var closer = event.target.closest && event.target.closest('[data-close-account-type]');
        if (closer) {
            event.preventDefault();
            event.stopImmediatePropagation();
            closeModal(closer.closest('[data-account-type-modal]'));
            return;
        }

        if (event.target.matches && event.target.matches('[data-account-type-modal]')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            closeModal(event.target);
            return;
        }

        var submit = event.target.closest && event.target.closest('.dede-account-type[data-account-type-mode="change"] .dede-account-type__submit');
        if (!submit) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        var root = submit.closest('.dede-account-type');
        var checked = root && root.querySelector('input[type="radio"]:checked');
        var selected = checked ? checked.value : '';
        var currentType = root ? (root.getAttribute('data-current-type') || '') : '';
        var error = root && root.querySelector('[data-account-type-error]');

        if (!selected || selected === currentType || submit.disabled) {
            return;
        }

        submit.disabled = true;
        submit.classList.add('is-loading');
        if (error) {
            error.textContent = '';
        }

        var data = new FormData();
        data.append('action', 'dede_store_change_account_type');
        data.append('nonce', config.accountTypeNonce || '');
        data.append('select_type', selected);
        data.append('redirect_url', window.location.href);

        fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
            method: 'POST',
            credentials: 'same-origin',
            body: data
        }).then(function (response) {
            return response.json();
        }).then(function (payload) {
            if (!payload || !payload.success) {
                throw new Error(payload && payload.data && payload.data.message
                    ? payload.data.message
                    : 'تغییر نوع حساب انجام نشد.');
            }
            window.location.replace(payload.data && payload.data.redirect
                ? payload.data.redirect
                : window.location.href);
        }).catch(function (exception) {
            if (error) {
                error.textContent = exception.message || 'تغییر نوع حساب انجام نشد.';
            }
            submit.disabled = false;
        }).finally(function () {
            submit.classList.remove('is-loading');
        });
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }
        document.querySelectorAll('[data-account-type-modal]:not([hidden])').forEach(closeModal);
    });
}());
JS;

    wp_add_inline_script('dede-store-features-customer-profile', $script, 'after');
}

register_activation_hook(__FILE__, array('DeDe_Store_Features', 'activate'));
add_action('plugins_loaded', 'dede_store_features');
add_action('wp_enqueue_scripts', 'dede_store_features_account_type_modal_hotfix', 120);
