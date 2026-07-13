<?php

if (!defined('ABSPATH')) {
    exit;
}

trait DeDe_Store_Features_Validation
{
    private function validate_request($request, $role, $user_id)
    {
        $data = array(
            'first_name' => sanitize_text_field($request['first_name'] ?? ''),
            'last_name' => sanitize_text_field($request['last_name'] ?? ''),
            'email' => sanitize_email($request['email'] ?? ''),
            'gender' => $this->normalize_gender($request['gender'] ?? ''),
            'national_code' => $this->digits_only($request['national_code'] ?? ''),
            'national_id' => $this->digits_only($request['national_id'] ?? ''),
            'company_name' => sanitize_text_field($request['company_name'] ?? ''),
            'store_name' => sanitize_text_field($request['store_name'] ?? ''),
            'economic_code' => $this->digits_only($request['economic_code'] ?? ''),
            'telegram' => sanitize_text_field($this->normalize_digits($request['telegram'] ?? '')),
            'birthday_year' => absint($this->normalize_digits($request['birthday_year'] ?? 0)),
            'birthday_month' => absint($this->normalize_digits($request['birthday_month'] ?? 0)),
            'birthday_day' => absint($this->normalize_digits($request['birthday_day'] ?? 0)),
            'billing_state' => sanitize_text_field($request['billing_state'] ?? ''),
            'billing_city' => absint($this->normalize_digits($request['billing_city'] ?? 0)),
            'billing_postcode' => $this->digits_only($request['billing_postcode'] ?? ''),
            'billing_phone' => dede_store_features_normalize_landline_value(
                $request['billing_phone'] ?? '',
                $request['billing_phone_area'] ?? '',
                $request['billing_phone_number'] ?? ''
            ),
            'billing_address_1' => sanitize_textarea_field($request['billing_address_1'] ?? ''),
            'same_as_billing' => !empty($request['same_as_billing']),
            'shipping_state' => sanitize_text_field($request['shipping_state'] ?? ''),
            'shipping_city' => absint($this->normalize_digits($request['shipping_city'] ?? 0)),
            'shipping_postcode' => $this->digits_only($request['shipping_postcode'] ?? ''),
            'shipping_phone' => dede_store_features_normalize_landline_value(
                $request['shipping_phone'] ?? '',
                $request['shipping_phone_area'] ?? '',
                $request['shipping_phone_number'] ?? ''
            ),
            'shipping_address_1' => sanitize_textarea_field($request['shipping_address_1'] ?? ''),
        );

        if ($data['same_as_billing']) {
            foreach (array('state', 'city', 'postcode', 'phone', 'address_1') as $key) {
                $data['shipping_' . $key] = $data['billing_' . $key];
            }
        }

        $errors = $this->validate_profile_array(array_merge($data, array(
            'mobile' => $this->get_verified_mobile(get_userdata($user_id)),
        )), $role, $user_id);

        if ($data['email'] && !is_email($data['email'])) {
            $errors['email'] = 'فرمت ایمیل صحیح نیست.';
        }

        if ($data['telegram']) {
            $data['telegram'] = $this->normalize_mobile($data['telegram']);
            if (!$data['telegram']) {
                $errors['telegram'] = 'شماره تلگرام را مانند شماره موبایل وارد کنید.';
            }
        }

        $birthday = $this->validate_birthday($data);
        if (is_wp_error($birthday)) {
            $errors['birthday_year'] = $birthday->get_error_message();
        } else {
            $data['birthday'] = $birthday['jalali'];
            $data['birthday_timestamp'] = $birthday['timestamp'];
        }

        return array('data' => $data, 'errors' => $errors);
    }

    private function validate_profile_array($data, $role, $user_id)
    {
        $errors = array();
        $mobile = $data['mobile'] ?? $this->get_verified_mobile(get_userdata($user_id));
        if (!$this->normalize_mobile($mobile)) {
            $errors['mobile'] = 'شماره همراه حساب کاربری معتبر نیست.';
        }

        $gender = $this->normalize_gender($data['gender'] ?? '');
        if ('company' === $role) {
            $this->require_persian($data, 'company_name', 'نام شرکت', $errors, true);
            if (!$this->is_valid_national_id($data['national_id'] ?? '')) {
                $errors['national_id'] = 'شناسه ملی معتبر نیست.';
            }
            $this->require_persian($data, 'first_name', 'نام رابط', $errors, false);
            $this->require_persian($data, 'last_name', 'نام خانوادگی رابط', $errors, false);
            if ($gender && !in_array($gender, array('آقای', 'خانم'), true)) {
                $errors['gender'] = 'جنسیت رابط معتبر نیست.';
            }
        } else {
            $this->require_persian($data, 'first_name', 'نام', $errors, true);
            $this->require_persian($data, 'last_name', 'نام خانوادگی', $errors, true);
            if (!in_array($gender, array('آقای', 'خانم'), true)) {
                $errors['gender'] = 'انتخاب جنسیت الزامی است.';
            }
            if (!$this->is_valid_national_code($data['national_code'] ?? '')) {
                $errors['national_code'] = 'کد ملی معتبر نیست.';
            }
            if ('store' === $role) {
                $this->require_persian($data, 'store_name', 'نام فروشگاه', $errors, true);
            }
        }

        $this->validate_address($data, 'billing', $errors);
        $this->validate_address($data, 'shipping', $errors);
        return $errors;
    }

    private function require_persian($data, $key, $label, &$errors, $required)
    {
        $value = trim((string) ($data[$key] ?? ''));
        if ($required && '' === $value) {
            $errors[$key] = $label . ' الزامی است.';
        } elseif ('' !== $value && !$this->is_persian_text($value)) {
            $errors[$key] = $label . ' را به فارسی وارد کنید.';
        }
    }

    private function validate_address($data, $prefix, &$errors)
    {
        $state = $data[$prefix . '_state'] ?? '';
        $city = absint($data[$prefix . '_city'] ?? 0);
        $postcode = (string) ($data[$prefix . '_postcode'] ?? '');
        $phone = (string) ($data[$prefix . '_phone'] ?? '');
        $address = trim((string) ($data[$prefix . '_address_1'] ?? ''));

        $states = $this->get_state_records();
        if (!$state || !isset($states[$state])) {
            $errors[$prefix . '_state'] = 'انتخاب استان الزامی است.';
        }
        if (!$city || !$this->city_belongs_to_state($city, $state)) {
            $errors[$prefix . '_city'] = 'انتخاب شهر معتبر الزامی است.';
        }
        if (!dede_store_features_is_valid_postcode_value($postcode)) {
            $errors[$prefix . '_postcode'] = 'کد پستی باید ۱۰ رقم معتبر و غیرتکراری باشد.';
        }
        if (!dede_store_features_is_valid_landline_value($phone)) {
            $errors[$prefix . '_phone'] = 'کد شهر و شماره تلفن ثابت را صحیح وارد کنید.';
        }
        $length = function_exists('mb_strlen') ? mb_strlen($address, 'UTF-8') : strlen($address);
        if ($length < 5) {
            $errors[$prefix . '_address_1'] = 'آدرس کامل را وارد کنید.';
        }
    }

    private function normalize_gender($value)
    {
        $value = trim(sanitize_text_field((string) $value));
        $aliases = array(
            'male' => 'آقای',
            'آقا' => 'آقای',
            'آقای' => 'آقای',
            'female' => 'خانم',
            'خانم' => 'خانم',
        );
        return $aliases[$value] ?? $value;
    }

    private function normalize_digits($value)
    {
        return strtr((string) $value, array(
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ));
    }

    private function digits_only($value)
    {
        return preg_replace('/\D+/', '', $this->normalize_digits($value));
    }

    private function normalize_mobile($value)
    {
        $digits = $this->digits_only($value);
        if (0 === strpos($digits, '0098')) {
            $digits = substr($digits, 2);
        }
        if (0 === strpos($digits, '0') && 11 === strlen($digits)) {
            $digits = '98' . substr($digits, 1);
        }
        if (0 === strpos($digits, '9') && 10 === strlen($digits)) {
            $digits = '98' . $digits;
        }
        return preg_match('/^989\d{9}$/', $digits) ? $digits : '';
    }

    private function is_persian_text($value)
    {
        $value = trim((string) $value);
        if (preg_match('/[0-9۰-۹٠-٩]/u', $value)) {
            return false;
        }
        return (bool) preg_match('/^[\x{0600}-\x{06FF}\x{200C}\s\-\.\(\)]+$/u', $value);
    }

    public function is_valid_national_code($code)
    {
        if (function_exists('dede_store_features_is_valid_national_code_value')) {
            return dede_store_features_is_valid_national_code_value($code);
        }

        $code = $this->digits_only($code);
        if (!preg_match('/^\d{10}$/', $code) || preg_match('/^(\d)\1{9}$/', $code)) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += ((int) $code[$i]) * (10 - $i);
        }
        $remainder = $sum % 11;
        $check = (int) $code[9];
        return $remainder < 2 ? $check === $remainder : $check === (11 - $remainder);
    }

    public function is_valid_national_id($id)
    {
        $id = $this->digits_only($id);
        if (!preg_match('/^\d{11}$/', $id)
            || preg_match('/^(\d)\1{10}$/', $id)
            || 0 === (int) substr($id, 3, 6)) {
            return false;
        }
        $coefficients = array(29, 27, 23, 19, 17, 29, 27, 23, 19, 17);
        $decimal = ((int) $id[9]) + 2;
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (((int) $id[$i]) + $decimal) * $coefficients[$i];
        }
        $remainder = $sum % 11;
        return (int) $id[10] === (10 === $remainder ? 0 : $remainder);
    }

    private function validate_birthday($data)
    {
        $year = absint($data['birthday_year']);
        $month = absint($data['birthday_month']);
        $day = absint($data['birthday_day']);
        if (!$year && !$month && !$day) {
            return array('jalali' => '', 'timestamp' => '');
        }
        if (!$year || !$month || !$day || $month > 12 || $day > 31) {
            return new WP_Error('invalid_birthday', 'تاریخ تولد را کامل و صحیح انتخاب کنید.');
        }

        list($gy, $gm, $gd) = $this->jalali_to_gregorian($year, $month, $day);
        $round_trip = $this->gregorian_to_jalali($gy, $gm, $gd);
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
        if (!$birth_date) {
            return new WP_Error('invalid_birthday', 'تاریخ تولد معتبر نیست.');
        }
        $cutoff = current_datetime()->modify('-15 years')->setTime(0, 0, 0);
        if ($birth_date > $cutoff) {
            return new WP_Error('under_age', 'سن کاربر باید حداقل ۱۵ سال باشد.');
        }
        return array(
            'jalali' => sprintf('%04d/%02d/%02d', $year, $month, $day),
            // Persian Datepicker legacy altField stored JavaScript Unix time in milliseconds.
            'timestamp' => (string) ($birth_date->getTimestamp() * 1000),
        );
    }

    public function jalali_to_gregorian($jy, $jm, $jd)
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
        $months = array(0, 31, (($gy % 4 === 0 && $gy % 100 !== 0) || ($gy % 400 === 0)) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        for ($gm = 1; $gm <= 12 && $gd > $months[$gm]; $gm++) {
            $gd -= $months[$gm];
        }
        return array($gy, $gm, $gd);
    }

    public function gregorian_to_jalali($gy, $gm, $gd)
    {
        $offsets = array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);
        $gy2 = $gm > 2 ? $gy + 1 : $gy;
        $days = 355666 + (365 * $gy) + (int) (($gy2 + 3) / 4)
            - (int) (($gy2 + 99) / 100) + (int) (($gy2 + 399) / 400)
            + $gd + $offsets[$gm - 1];
        $jy = -1595 + (33 * (int) ($days / 12053));
        $days %= 12053;
        $jy += 4 * (int) ($days / 1461);
        $days %= 1461;
        if ($days > 365) {
            $jy += (int) (($days - 1) / 365);
            $days = ($days - 1) % 365;
        }
        if ($days < 186) {
            return array($jy, 1 + (int) ($days / 31), 1 + ($days % 31));
        }
        return array($jy, 7 + (int) (($days - 186) / 30), 1 + (($days - 186) % 30));
    }
}
