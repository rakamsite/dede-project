<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Normalize Persian/Arabic digits and remove non-digits.
 */
function dede_store_features_national_code_digits($value)
{
    $value = strtr((string) $value, array(
        '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
        '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
        '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
        '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
    ));

    return preg_replace('/\D+/', '', $value);
}

/**
 * Reject obviously synthetic values before the checksum calculation.
 */
function dede_store_features_is_synthetic_national_code($code)
{
    $code = dede_store_features_national_code_digits($code);
    if (!preg_match('/^\d{10}$/', $code)) {
        return false;
    }

    if (preg_match('/^(\d)\1{9}$/', $code)) {
        return true;
    }

    $ascending = true;
    $descending = true;
    for ($index = 1; $index < 10; $index++) {
        $previous = (int) $code[$index - 1];
        $current = (int) $code[$index];
        if ($current !== (($previous + 1) % 10)) {
            $ascending = false;
        }
        if ($current !== (($previous + 9) % 10)) {
            $descending = false;
        }
    }

    return $ascending || $descending;
}

/**
 * Validate an Iranian national code using format, anti-fake and checksum rules.
 */
function dede_store_features_is_valid_national_code_value($code)
{
    $code = dede_store_features_national_code_digits($code);
    if (!preg_match('/^\d{10}$/', $code)
        || dede_store_features_is_synthetic_national_code($code)) {
        return false;
    }

    $sum = 0;
    for ($index = 0; $index < 9; $index++) {
        $sum += ((int) $code[$index]) * (10 - $index);
    }

    $remainder = $sum % 11;
    $check_digit = (int) $code[9];
    return $remainder < 2
        ? $check_digit === $remainder
        : $check_digit === (11 - $remainder);
}

/**
 * Stop invalid profile requests before the normal save callback runs.
 */
function dede_store_features_guard_national_code_request()
{
    $raw_code = wp_unslash($_POST['national_code'] ?? '');
    if ('' === trim((string) $raw_code)) {
        return;
    }

    if (!dede_store_features_is_valid_national_code_value($raw_code)) {
        wp_send_json_error(array(
            'message' => 'لطفاً خطاهای فرم را اصلاح کنید.',
            'errors' => array(
                'national_code' => 'کد ملی معتبر نیست.',
            ),
        ), 422);
    }
}

/**
 * Remove previously accepted synthetic codes when their owner next visits.
 */
function dede_store_features_invalidate_stored_synthetic_national_code()
{
    if (!is_user_logged_in()) {
        return;
    }

    $user_id = get_current_user_id();
    $account_type = sanitize_key((string) get_user_meta($user_id, 'customer_type', true));
    if ('company' === $account_type) {
        return;
    }

    $stored = (string) get_user_meta($user_id, '_dede_national_code_', true);
    if ($stored && dede_store_features_is_synthetic_national_code($stored)) {
        update_user_meta($user_id, '_dede_national_code_', '');
        delete_user_meta($user_id, '_dede_profile_complete_');
    }
}

/**
 * Give immediate feedback in the three-step profile form.
 */
function dede_store_features_national_code_client_guard()
{
    $script = <<<'JS'
(function () {
    'use strict';

    function digits(value) {
        return String(value || '')
            .replace(/[۰-۹]/g, function (digit) { return String('۰۱۲۳۴۵۶۷۸۹'.indexOf(digit)); })
            .replace(/[٠-٩]/g, function (digit) { return String('٠١٢٣٤٥٦٧٨٩'.indexOf(digit)); })
            .replace(/\D+/g, '');
    }

    function isSynthetic(code) {
        if (/^(\d)\1{9}$/.test(code)) return true;
        var ascending = true;
        var descending = true;
        for (var index = 1; index < 10; index += 1) {
            var previous = Number(code.charAt(index - 1));
            var current = Number(code.charAt(index));
            if (current !== ((previous + 1) % 10)) ascending = false;
            if (current !== ((previous + 9) % 10)) descending = false;
        }
        return ascending || descending;
    }

    function isValid(value) {
        var code = digits(value);
        if (!/^\d{10}$/.test(code) || isSynthetic(code)) return false;
        var sum = 0;
        for (var index = 0; index < 9; index += 1) {
            sum += Number(code.charAt(index)) * (10 - index);
        }
        var remainder = sum % 11;
        var expected = remainder < 2 ? remainder : 11 - remainder;
        return Number(code.charAt(9)) === expected;
    }

    function validate(input) {
        var value = String(input.value || '').trim();
        input.setCustomValidity(value && !isValid(value) ? 'کد ملی معتبر نیست.' : '');
    }

    document.querySelectorAll('input[name="national_code"]').forEach(function (input) {
        validate(input);
        input.addEventListener('input', function () { validate(input); });
        input.addEventListener('blur', function () { validate(input); });
    });
}());
JS;

    wp_add_inline_script('dede-store-features-customer-profile', $script, 'after');
}

if (function_exists('add_action')) {
    add_action('wp_ajax_dede_store_save_profile', 'dede_store_features_guard_national_code_request', 0);
    add_action('wp_loaded', 'dede_store_features_invalidate_stored_synthetic_national_code', 20);
    add_action('wp_enqueue_scripts', 'dede_store_features_national_code_client_guard', 130);
}
