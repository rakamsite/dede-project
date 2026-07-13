<?php

if (!defined('ABSPATH')) {
    exit;
}

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
    if (!preg_match('/^0\d{10}$/', $phone)) {
        return false;
    }

    $area = substr($phone, 0, 3);
    $local = substr($phone, 3);
    return in_array($area, dede_store_features_landline_area_codes(), true)
        && !dede_store_features_is_obvious_numeric_pattern($local);
}
