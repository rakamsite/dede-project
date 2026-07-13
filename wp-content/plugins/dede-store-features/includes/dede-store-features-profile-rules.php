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
 * Keep a hidden compatibility national-code field in sync for company users.
 * The canonical company identifier remains national_id.
 */
function dede_store_features_sync_company_national_code_request()
{
    if (!is_user_logged_in()) {
        return;
    }

    $user_id = get_current_user_id();
    if ('company' !== dede_store_features_current_account_type($user_id)) {
        return;
    }

    $national_id = wp_unslash($_POST['national_id'] ?? '');
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

if (function_exists('add_action')) {
    add_action('wp_ajax_dede_store_change_account_type', 'dede_store_features_guard_account_type_transition', 0);
    add_action('wp_ajax_dede_store_save_profile', 'dede_store_features_sync_company_national_code_request', 0);
    add_action('dede_store_features_after_profile_save', 'dede_store_features_copy_company_national_id_meta', 20, 3);
    add_action('wp_loaded', 'dede_store_features_backfill_company_national_code', 25);
}
