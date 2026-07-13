<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/dede-store-features-profile-rules.php';

/**
 * Convert Persian/Arabic digits to ASCII digits and remove other characters.
 */
function dede_store_features_contact_digits($value)
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
 * Reject only unmistakably fabricated patterns. This intentionally avoids
 * geography-specific postcode checks so valid rural and newly assigned codes
 * are not rejected.
 */
function dede_store_features_is_obvious_numeric_pattern($digits)
{
    $digits = dede_store_features_contact_digits($digits);
    $length = strlen($digits);
    if ($length < 4) {
        return false;
    }

    if (preg_match('/^(\d)\1+$/', $digits)) {
        return true;
    }

    $ascending = true;
    $descending = true;
    for ($index = 1; $index < $length; $index++) {
        $previous = (int) $digits[$index - 1];
        $current = (int) $digits[$index];
        if ($current !== (($previous + 1) % 10)) {
            $ascending = false;
        }
        if ($current !== (($previous + 9) % 10)) {
            $descending = false;
        }
    }

    return $ascending || $descending;
}

function dede_store_features_is_valid_postcode_value($postcode)
{
    $postcode = dede_store_features_contact_digits($postcode);
    return (bool) preg_match('/^\d{10}$/', $postcode)
        && !dede_store_features_is_obvious_numeric_pattern($postcode);
}

/**
 * Count meaningful address words. Punctuation and repeated spaces do not count
 * as words; Persian, Latin and numeric address tokens do.
 */
function dede_store_features_address_word_count($address)
{
    $address = trim((string) $address);
    if ('' === $address) {
        return 0;
    }

    $matched = preg_match_all('/[\p{L}\p{N}]+/u', $address, $words);
    return false === $matched ? 0 : (int) $matched;
}

function dede_store_features_is_valid_address_value($address)
{
    return dede_store_features_address_word_count($address) >= 4;
}

/**
 * Current province-level Iranian landline area codes after nationwide
 * normalization. Numbers are stored with the leading zero.
 */
function dede_store_features_landline_area_codes()
{
    return array(
        '011', '013', '017', '021', '023', '024', '025', '026', '028',
        '031', '034', '035', '038', '041', '044', '045', '051', '054',
        '056', '058', '061', '066', '071', '074', '076', '077', '081',
        '083', '084', '086', '087',
    );
}

function dede_store_features_normalize_landline_value($legacy, $area, $number)
{
    $legacy = dede_store_features_contact_digits($legacy);
    $area = dede_store_features_contact_digits($area);
    $number = dede_store_features_contact_digits($number);

    if ($area || $number) {
        if (2 === strlen($area)) {
            $area = '0' . $area;
        }
        return $area . $number;
    }

    return $legacy;
}

function dede_store_features_is_valid_landline_value($phone)
{
    $phone = dede_store_features_contact_digits($phone);
    if (!preg_match('/^0\d{7,}$/', $phone)) {
        return false;
    }

    $area = substr($phone, 0, 3);
    $local = substr($phone, 3);
    return in_array($area, dede_store_features_landline_area_codes(), true)
        && (bool) preg_match('/^\d{5,}$/', $local)
        && !dede_store_features_is_obvious_numeric_pattern($local);
}

/**
 * Load a compatibility layer before the main profile enhancement script. It
 * preserves legacy values and changes the local landline part to a minimum of
 * five digits without imposing any maximum length.
 */
function dede_store_features_enqueue_landline_minimum_length_compat()
{
    if (is_admin()) {
        return;
    }

    $is_account = function_exists('is_account_page') && is_account_page();
    $is_checkout = function_exists('is_checkout') && is_checkout();
    if (!$is_account && !$is_checkout) {
        return;
    }

    $js_path = DEDE_STORE_FEATURES_PATH . 'assets/js/landline-minimum-five-digits.js';
    wp_enqueue_script(
        'dede-store-features-landline-minimum-five-digits',
        DEDE_STORE_FEATURES_URL . 'assets/js/landline-minimum-five-digits.js',
        array('dede-store-features-customer-profile'),
        file_exists($js_path) ? (string) filemtime($js_path) : DEDE_STORE_FEATURES_VERSION,
        true
    );
}

/**
 * Load profile-only UI enhancements after the base profile assets.
 */
function dede_store_features_enqueue_contact_enhancements()
{
    if (is_admin()) {
        return;
    }

    $is_account = function_exists('is_account_page') && is_account_page();
    $is_checkout = function_exists('is_checkout') && is_checkout();
    if (!$is_account && !$is_checkout) {
        return;
    }

    $css_path = DEDE_STORE_FEATURES_PATH . 'assets/css/profile-contact-enhancements.css';
    $js_path = DEDE_STORE_FEATURES_PATH . 'assets/js/profile-contact-enhancements.js';
    $address_js_path = DEDE_STORE_FEATURES_PATH . 'assets/js/address-four-word-validation.js';

    wp_enqueue_style(
        'dede-store-features-profile-contact-enhancements',
        DEDE_STORE_FEATURES_URL . 'assets/css/profile-contact-enhancements.css',
        array('dede-store-features-customer-profile'),
        file_exists($css_path) ? (string) filemtime($css_path) : DEDE_STORE_FEATURES_VERSION
    );

    wp_enqueue_script(
        'dede-store-features-profile-contact-enhancements',
        DEDE_STORE_FEATURES_URL . 'assets/js/profile-contact-enhancements.js',
        array('dede-store-features-customer-profile'),
        file_exists($js_path) ? (string) filemtime($js_path) : DEDE_STORE_FEATURES_VERSION,
        true
    );

    wp_enqueue_script(
        'dede-store-features-address-four-word-validation',
        DEDE_STORE_FEATURES_URL . 'assets/js/address-four-word-validation.js',
        array('dede-store-features-profile-contact-enhancements'),
        file_exists($address_js_path) ? (string) filemtime($address_js_path) : DEDE_STORE_FEATURES_VERSION,
        true
    );
}

if (function_exists('add_action')) {
    add_action('wp_enqueue_scripts', 'dede_store_features_enqueue_landline_minimum_length_compat', 139);
    add_action('wp_enqueue_scripts', 'dede_store_features_enqueue_contact_enhancements', 140);
}
