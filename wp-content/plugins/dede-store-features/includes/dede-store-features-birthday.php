<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Normalize Persian and Arabic digits for birthday fields.
 */
function dede_store_features_birthday_number($value)
{
    $value = strtr((string) $value, array(
        '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
        '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
        '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
        '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
    ));

    return absint(preg_replace('/\D+/', '', $value));
}

function dede_store_features_birthday_jalali_to_gregorian($jy, $jm, $jd)
{
    $jy += 1595;
    $days = -355668 + (365 * $jy) + ((int) ($jy / 33) * 8) + (int) ((($jy % 33) + 3) / 4) + $jd;
    $days += $jm < 7 ? ($jm - 1) * 31 : (($jm - 7) * 30) + 186;
    $gy = 400 * (int) ($days / 146097);
    $days %= 146097;
    if ($days > 36524) {
        $gy += 100 * (int) (--$days / 36524);
        $days %= 36524;
        if ($days >= 365) {
            $days++;
        }
    }
    $gy += 4 * (int) ($days / 1461);
    $days %= 1461;
    if ($days > 365) {
        $gy += (int) (($days - 1) / 365);
        $days = ($days - 1) % 365;
    }
    $gd = $days + 1;
    $month_days = array(0, 31, (($gy % 4 === 0 && $gy % 100 !== 0) || $gy % 400 === 0) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    $gm = 1;
    while ($gm <= 12 && $gd > $month_days[$gm]) {
        $gd -= $month_days[$gm];
        $gm++;
    }

    return array($gy, $gm, $gd);
}

function dede_store_features_birthday_gregorian_to_jalali($gy, $gm, $gd)
{
    $g_day_no = (365 * ($gy - 1600)) + (int) (($gy - 1600 + 3) / 4)
        - (int) (($gy - 1600 + 99) / 100) + (int) (($gy - 1600 + 399) / 400);
    $g_month_days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    for ($index = 0; $index < $gm - 1; $index++) {
        $g_day_no += $g_month_days[$index];
    }
    if ($gm > 2 && (($gy % 4 === 0 && $gy % 100 !== 0) || $gy % 400 === 0)) {
        $g_day_no++;
    }
    $g_day_no += $gd - 1;
    $j_day_no = $g_day_no - 79;
    $j_np = (int) ($j_day_no / 12053);
    $j_day_no %= 12053;
    $jy = 979 + (33 * $j_np) + (4 * (int) ($j_day_no / 1461));
    $j_day_no %= 1461;
    if ($j_day_no >= 366) {
        $jy += (int) (($j_day_no - 1) / 365);
        $j_day_no = ($j_day_no - 1) % 365;
    }
    if ($j_day_no < 186) {
        $jm = 1 + (int) ($j_day_no / 31);
        $jd = 1 + ($j_day_no % 31);
    } else {
        $jm = 7 + (int) (($j_day_no - 186) / 30);
        $jd = 1 + (($j_day_no - 186) % 30);
    }

    return array($jy, $jm, $jd);
}

/**
 * Birthday is optional. When present, it must be valid and the user must have
 * reached their fifteenth birthday. The normal profile saver still owns the
 * legacy YYYY/MM/DD and millisecond timestamp storage contract.
 */
function dede_store_features_validate_birthday_age($year, $month, $day, $now = null)
{
    $year = dede_store_features_birthday_number($year);
    $month = dede_store_features_birthday_number($month);
    $day = dede_store_features_birthday_number($day);

    if (!$year && !$month && !$day) {
        return true;
    }
    if (!$year || !$month || !$day || $month > 12 || $day > 31) {
        return new WP_Error('invalid_birthday', 'تاریخ تولد را کامل و صحیح انتخاب کنید.');
    }

    list($gy, $gm, $gd) = dede_store_features_birthday_jalali_to_gregorian($year, $month, $day);
    $round_trip = dede_store_features_birthday_gregorian_to_jalali($gy, $gm, $gd);
    if (!checkdate($gm, $gd, $gy)
        || (int) $round_trip[0] !== $year
        || (int) $round_trip[1] !== $month
        || (int) $round_trip[2] !== $day) {
        return new WP_Error('invalid_birthday', 'تاریخ تولد معتبر نیست.');
    }

    $birth_date = DateTimeImmutable::createFromFormat(
        '!Y-n-j',
        sprintf('%d-%d-%d', $gy, $gm, $gd),
        wp_timezone()
    );
    $now = $now instanceof DateTimeImmutable ? $now : current_datetime();
    $cutoff = $now->modify('-15 years')->setTime(0, 0, 0);
    if (!$birth_date || $birth_date > $cutoff) {
        return new WP_Error('under_age', 'سن کاربر باید حداقل ۱۵ سال باشد.');
    }

    return true;
}

function dede_store_features_guard_birthday_request()
{
    $result = dede_store_features_validate_birthday_age(
        wp_unslash($_POST['birthday_year'] ?? ''),
        wp_unslash($_POST['birthday_month'] ?? ''),
        wp_unslash($_POST['birthday_day'] ?? '')
    );

    if (is_wp_error($result)) {
        wp_send_json_error(array(
            'message' => 'لطفاً خطاهای فرم را اصلاح کنید.',
            'errors' => array(
                'birthday_year' => $result->get_error_message(),
            ),
        ), 422);
    }
}

function dede_store_features_birthday_max_date()
{
    $cutoff = current_datetime()->modify('-15 years')->setTime(0, 0, 0);
    $jalali = dede_store_features_birthday_gregorian_to_jalali(
        (int) $cutoff->format('Y'),
        (int) $cutoff->format('n'),
        (int) $cutoff->format('j')
    );

    return sprintf('%04d/%02d/%02d', $jalali[0], $jalali[1], $jalali[2]);
}

function dede_store_features_enqueue_birthday_picker()
{
    if (is_admin()) {
        return;
    }

    $is_account = function_exists('is_account_page') && is_account_page();
    $is_checkout = function_exists('is_checkout') && is_checkout();
    if (!$is_account && !$is_checkout) {
        return;
    }

    $css_path = DEDE_STORE_FEATURES_PATH . 'assets/css/birthday-picker.css';
    $js_path = DEDE_STORE_FEATURES_PATH . 'assets/js/birthday-picker.js';

    wp_enqueue_style(
        'dede-store-features-birthday-picker',
        DEDE_STORE_FEATURES_URL . 'assets/css/birthday-picker.css',
        array('dede-store-features-customer-profile'),
        file_exists($css_path) ? (string) filemtime($css_path) : DEDE_STORE_FEATURES_VERSION
    );
    wp_enqueue_script(
        'dede-store-features-birthday-picker',
        DEDE_STORE_FEATURES_URL . 'assets/js/birthday-picker.js',
        array('dede-store-features-customer-profile'),
        file_exists($js_path) ? (string) filemtime($js_path) : DEDE_STORE_FEATURES_VERSION,
        true
    );
    wp_localize_script('dede-store-features-birthday-picker', 'DedeBirthdayPicker', array(
        'maxDate' => dede_store_features_birthday_max_date(),
        'minYear' => 1300,
    ));
}

if (function_exists('add_action')) {
    add_action('wp_ajax_dede_store_save_profile', 'dede_store_features_guard_birthday_request', 1);
    add_action('wp_enqueue_scripts', 'dede_store_features_enqueue_birthday_picker', 135);
}
