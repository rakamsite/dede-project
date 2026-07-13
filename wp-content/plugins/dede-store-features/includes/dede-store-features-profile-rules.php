<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Only personal and store accounts may switch to each other. Company accounts
 * are intentionally isolated in both directions because their accounting and
 * CRM identity contract is different.
 */
function dede_store_features_can_change_account_type($current_type, $target_type)
{
    $current_type = sanitize_key((string) $current_type);
    $target_type = sanitize_key((string) $target_type);

    if ($current_type === $target_type) {
        return in_array($current_type, array('personal', 'store', 'company'), true);
    }

    return in_array($current_type, array('personal', 'store'), true)
        && in_array($target_type, array('personal', 'store'), true);
}

function dede_store_features_current_account_type($user_id)
{
    $type = sanitize_key((string) get_user_meta(absint($user_id), 'customer_type', true));
    if (in_array($type, array('personal', 'store', 'company'), true)) {
        return $type;
    }

    $user = get_userdata(absint($user_id));
    foreach ($user ? (array) $user->roles : array() as $role) {
        if (in_array($role, array('personal', 'store', 'company'), true)) {
            return $role;
        }
    }

    return '';
}

/**
 * Enforce the transition matrix before the normal account-type callback runs.
 */
function dede_store_features_guard_account_type_transition()
{
    if (!is_user_logged_in() || 'POST' !== ($_SERVER['REQUEST_METHOD'] ?? '')) {
        return;
    }

    $user_id = get_current_user_id();
    $current_type = dede_store_features_current_account_type($user_id);
    $target_type = sanitize_key(wp_unslash($_POST['select_type'] ?? ''));

    if (!$current_type || !$target_type) {
        return;
    }

    if (!dede_store_features_can_change_account_type($current_type, $target_type)) {
        wp_send_json_error(array(
            'message' => 'تغییر بین حساب شرکتی و حساب شخص یا فروشگاه مجاز نیست.',
        ), 422);
    }
}

/**
 * The hidden compatibility field contains the 11-digit company national ID.
 * Temporarily clear it before the legacy 10-digit national-code guard runs.
 */
function dede_store_features_suspend_company_national_code_guard()
{
    if (!is_user_logged_in()) {
        return;
    }

    $user_id = get_current_user_id();
    if ('company' !== dede_store_features_current_account_type($user_id)) {
        return;
    }

    $national_id = wp_unslash($_POST['national_id'] ?? '');
    if ('' === trim((string) $national_id)) {
        return;
    }

    $GLOBALS['dede_store_company_national_id_request'] = $national_id;
    $_POST['national_code'] = '';
}

/**
 * Restore the compatibility field after the national-code guard and before the
 * normal profile validation/save callback.
 */
function dede_store_features_restore_company_national_code_request()
{
    $national_id = $GLOBALS['dede_store_company_national_id_request'] ?? '';
    if ('' !== trim((string) $national_id)) {
        $_POST['national_code'] = $national_id;
    }
}

/**
 * Preserve the old integration contract for code that still reads the national
 * code meta, while keeping the dedicated national-id meta as the source of truth.
 */
function dede_store_features_copy_company_national_id_meta($user_id, $role, $data)
{
    if ('company' !== $role) {
        return;
    }

    $national_id = preg_replace('/\D+/', '', (string) ($data['national_id'] ?? ''));
    if ($national_id) {
        update_user_meta(absint($user_id), '_dede_national_code_', $national_id);
    }
}

function dede_store_features_backfill_company_national_code()
{
    if (!is_user_logged_in()) {
        return;
    }

    $user_id = get_current_user_id();
    if ('company' !== dede_store_features_current_account_type($user_id)) {
        return;
    }

    $national_id = preg_replace('/\D+/', '', (string) get_user_meta($user_id, '_dede_national_id_', true));
    $national_code = preg_replace('/\D+/', '', (string) get_user_meta($user_id, '_dede_national_code_', true));
    if ($national_id && $national_id !== $national_code) {
        update_user_meta($user_id, '_dede_national_code_', $national_id);
    }
}

/**
 * The base profile script can disable the original shipping fields before the
 * split landline inputs are created. Mirror that initial state onto the newly
 * created inputs so a collapsed shipping section never blocks progression.
 */
function dede_store_features_sync_initial_shipping_landline_state()
{
    if (is_admin()) {
        return;
    }

    $is_account = function_exists('is_account_page') && is_account_page();
    $is_checkout = function_exists('is_checkout') && is_checkout();
    if (!$is_account && !$is_checkout) {
        return;
    }

    $script = <<<'JS'
(function () {
    'use strict';

    function init() {
        document.querySelectorAll('[data-dede-profile]').forEach(function (root) {
            var sameAddress = root.querySelector('[name="same_as_billing"]');
            var shippingBlock = root.querySelector('[data-shipping-fields]');
            if (!sameAddress || !shippingBlock) {
                return;
            }

            function sync() {
                var disabled = Boolean(sameAddress.checked);
                shippingBlock.querySelectorAll('.dede-landline__area, .dede-landline__number').forEach(function (input) {
                    input.disabled = disabled;
                    if (!disabled && input.value) {
                        input.dispatchEvent(new Event('input', {bubbles: true}));
                    }
                });
            }

            sameAddress.addEventListener('change', function () {
                window.setTimeout(sync, 0);
            });
            sync();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        window.setTimeout(init, 0);
    }
}());
JS;

    wp_add_inline_script('dede-store-features-profile-contact-enhancements', $script, 'after');
}

if (function_exists('add_action')) {
    add_action('wp_ajax_dede_store_change_account_type', 'dede_store_features_guard_account_type_transition', 0);
    add_action('wp_ajax_dede_store_save_profile', 'dede_store_features_suspend_company_national_code_guard', -1);
    add_action('wp_ajax_dede_store_save_profile', 'dede_store_features_restore_company_national_code_request', 1);
    add_action('dede_store_features_after_profile_save', 'dede_store_features_copy_company_national_id_meta', 20, 3);
    add_action('wp_loaded', 'dede_store_features_backfill_company_national_code', 25);
    add_action('wp_enqueue_scripts', 'dede_store_features_sync_initial_shipping_landline_state', 145);
}
