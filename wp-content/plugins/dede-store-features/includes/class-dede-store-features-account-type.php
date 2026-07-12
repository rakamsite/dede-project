<?php

if (!defined('ABSPATH')) {
    exit;
}

class DeDe_Store_Features_Account_Type
{
    const TOKEN_COOKIE = 'dede_account_type_token';
    const TOKEN_PREFIX = 'dede_store_account_type_';

    private $allowed_types = array('personal', 'store', 'company');

    public function __construct()
    {
        add_action('wp_ajax_dede_store_select_account_type', array($this, 'ajax_select'));
        add_action('wp_ajax_nopriv_dede_store_select_account_type', array($this, 'ajax_select'));
        add_action('wp_ajax_dede_store_change_account_type', array($this, 'ajax_change'));
        add_action('wp_ajax_Login_register_ajax', array($this, 'intercept_legacy_request'), 1);
        add_action('wp_ajax_nopriv_Login_register_ajax', array($this, 'intercept_legacy_request'), 1);
        add_action('user_register', array($this, 'prepare_legacy_registration'), 20);
    }

    public function prepare_legacy_registration($user_id)
    {
        if (!wp_doing_ajax() || empty($_POST['verifySmsPass'])) {
            return;
        }

        $this->prepare_selection($user_id);
    }

    public function intercept_legacy_request()
    {
        if (!empty($_POST['updateUserRol'])) {
            $this->complete_selection(true);
        }
    }

    public function prepare_selection($user_id)
    {
        $user_id = absint($user_id);
        $user = get_userdata($user_id);

        if (!$user) {
            return false;
        }

        $mobile = $this->normalize_mobile($user->user_login);
        if ($mobile) {
            wp_update_user(array(
                'ID' => $user_id,
                'nickname' => $mobile,
            ));
            update_user_meta($user_id, 'custom_phone_number', $mobile);
            update_user_meta($user_id, 'nickname', $mobile);
        }

        $token = wp_generate_password(48, false, false);
        set_transient(self::TOKEN_PREFIX . $token, $user_id, 15 * MINUTE_IN_SECONDS);
        update_user_meta($user_id, '_dede_account_type_pending_', '1');
        delete_user_meta($user_id, 'customer_type');

        $this->set_token_cookie($token, time() + (15 * MINUTE_IN_SECONDS));

        return true;
    }

    public function is_pending($user_id)
    {
        return '1' === (string) get_user_meta(absint($user_id), '_dede_account_type_pending_', true);
    }

    public function render($mode = 'create', $current_type = '')
    {
        $mode = 'change' === $mode ? 'change' : 'create';
        $current_type = in_array($current_type, $this->allowed_types, true) ? $current_type : '';
        $options = array(
            'personal' => array(
                'title' => 'شخص',
                'description' => 'خرید و دریافت فاکتور به نام شخص',
                'icon' => 'person',
            ),
            'store' => array(
                'title' => 'فروشگاه',
                'description' => 'خرید برای فروشگاه یا واحد صنفی',
                'icon' => 'store',
            ),
            'company' => array(
                'title' => 'شرکت',
                'description' => 'خرید و دریافت فاکتور به نام شرکت',
                'icon' => 'company',
            ),
        );

        include DEDE_STORE_FEATURES_PATH . 'templates/account-type-selector.php';
    }

    public function ajax_select()
    {
        if (is_user_logged_in()) {
            check_ajax_referer('dede_store_account_type', 'nonce');
        } elseif (!$this->resolve_token_user_id()) {
            wp_send_json_error(array(
                'message' => 'اعتبار مرحله ثبت‌نام منقضی شده است. با همان شماره و رمز وارد حساب خود شوید.',
            ), 403);
        }

        $this->complete_selection(false);
    }

    public function ajax_change()
    {
        check_ajax_referer('dede_store_account_type', 'nonce');
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'ابتدا وارد حساب کاربری شوید.'), 401);
        }
        if ('POST' !== ($_SERVER['REQUEST_METHOD'] ?? '')) {
            wp_send_json_error(array('message' => 'درخواست نامعتبر است.'), 405);
        }

        $type = sanitize_key(wp_unslash($_POST['select_type'] ?? ''));
        if (!in_array($type, $this->allowed_types, true)) {
            wp_send_json_error(array('message' => 'نوع حساب انتخاب‌شده معتبر نیست.'), 422);
        }

        $user_id = get_current_user_id();
        $current_type = $this->get_current_type($user_id);
        if (!$current_type) {
            wp_send_json_error(array('message' => 'نوع حساب فعلی معتبر نیست.'), 422);
        }

        $redirect = wp_validate_redirect(
            esc_url_raw(wp_unslash($_POST['redirect_url'] ?? '')),
            function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account')
        );

        if ($current_type === $type) {
            wp_send_json_success(array(
                'message' => 'نوع حساب تغییری نکرد.',
                'redirect' => $redirect,
            ));
        }

        $updated = wp_update_user(array(
            'ID' => $user_id,
            'role' => $type,
        ));
        if (is_wp_error($updated)) {
            wp_send_json_error(array('message' => 'تغییر نوع حساب انجام نشد.'), 500);
        }

        $this->clear_incompatible_profile_data($user_id, $type);
        update_user_meta($user_id, 'customer_type', $type);
        delete_user_meta($user_id, '_dede_account_type_pending_');

        do_action('dede_store_features_account_type_changed', $user_id, $current_type, $type);

        wp_send_json_success(array(
            'message' => 'نوع حساب تغییر کرد. اطلاعات ضروری نوع جدید را تکمیل کنید.',
            'redirect' => $redirect,
        ));
    }

    public function handle_legacy_request()
    {
        $this->complete_selection(true);
    }

    private function complete_selection($legacy_request)
    {
        if ('POST' !== ($_SERVER['REQUEST_METHOD'] ?? '')) {
            wp_send_json_error(array('message' => 'درخواست نامعتبر است.'), 405);
        }

        $type = sanitize_key(wp_unslash($_POST['select_type'] ?? ''));
        if (!in_array($type, $this->allowed_types, true)) {
            wp_send_json_error(array('message' => 'مشکل در انتخاب نوع حساب کاربری.'), 422);
        }

        $user_id = $this->resolve_pending_user_id();
        if (!$user_id) {
            wp_send_json_error(array(
                'message' => 'اعتبار مرحله ثبت‌نام منقضی شده است. با همان شماره و رمز وارد حساب خود شوید.',
            ), 403);
        }

        $user = get_userdata($user_id);
        if (!$user) {
            wp_send_json_error(array('message' => 'حساب کاربری پیدا نشد.'), 404);
        }

        $mobile = $this->normalize_mobile($user->user_login);
        if (!$mobile) {
            $mobile = $this->normalize_mobile(wp_unslash($_COOKIE['register_User_username'] ?? ''));
        }

        $update = wp_update_user(array(
            'ID' => $user_id,
            'role' => $type,
            'nickname' => $mobile ?: $user->user_login,
        ));

        if (is_wp_error($update)) {
            wp_send_json_error(array('message' => 'مشکل در انتخاب نوع حساب کاربری.'), 500);
        }

        update_user_meta($user_id, 'customer_type', $type);
        if ($mobile) {
            update_user_meta($user_id, 'custom_phone_number', $mobile);
            update_user_meta($user_id, 'nickname', $mobile);
        }
        delete_user_meta($user_id, '_dede_account_type_pending_');

        $this->clear_pending_token();
        $this->clear_registration_cookies();
        wp_clear_auth_cookie();
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true, is_ssl());

        do_action('dede_store_features_account_type_selected', $user_id, $type);

        wp_send_json_success(array(
            'message' => 'نوع حساب کاربری با موفقیت ثبت شد.',
            'redirect' => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account'),
            'legacy' => (bool) $legacy_request,
        ));
    }

    private function get_current_type($user_id)
    {
        $type = sanitize_key((string) get_user_meta($user_id, 'customer_type', true));
        if (in_array($type, $this->allowed_types, true)) {
            return $type;
        }

        $user = get_userdata($user_id);
        foreach ($user ? (array) $user->roles : array() as $role) {
            if (in_array($role, $this->allowed_types, true)) {
                return $role;
            }
        }

        return '';
    }

    private function clear_incompatible_profile_data($user_id, $new_type)
    {
        delete_user_meta($user_id, '_dede_profile_complete_');
        delete_user_meta($user_id, '_dede_profile_completed_at_');

        $clear = array();
        if ('company' === $new_type) {
            $clear = array('_dede_national_code_', '_dede_shop_name_');
        } elseif ('store' === $new_type) {
            $clear = array('_dede_national_id_', '_dede_Economic_Code_', 'billing_company', 'shipping_company');
        } else {
            $clear = array('_dede_national_id_', '_dede_shop_name_', '_dede_Economic_Code_', 'billing_company', 'shipping_company');
        }

        foreach ($clear as $key) {
            update_user_meta($user_id, $key, '');
        }
    }

    private function resolve_token_user_id()
    {
        $token = sanitize_text_field(wp_unslash($_COOKIE[self::TOKEN_COOKIE] ?? ''));
        if (!$token) {
            return 0;
        }

        return absint(get_transient(self::TOKEN_PREFIX . $token));
    }

    private function resolve_pending_user_id()
    {
        $user_id = $this->resolve_token_user_id();
        if ($user_id) {
            return $user_id;
        }

        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            if ('1' === (string) get_user_meta($user_id, '_dede_account_type_pending_', true)) {
                return $user_id;
            }
        }

        return 0;
    }

    private function clear_pending_token()
    {
        $token = sanitize_text_field(wp_unslash($_COOKIE[self::TOKEN_COOKIE] ?? ''));
        if ($token) {
            delete_transient(self::TOKEN_PREFIX . $token);
        }
        $this->set_token_cookie('', time() - HOUR_IN_SECONDS);
    }

    private function clear_registration_cookies()
    {
        $default_path = defined('COOKIEPATH') && COOKIEPATH ? COOKIEPATH : '/';
        $domain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '';
        $paths = array_unique(array($default_path, '/', '/wp-admin', '/wp-admin/'));

        foreach (array('register_User_password', 'register_User_username', 'User_ID_dede', 'TempPassSend') as $name) {
            foreach ($paths as $path) {
                setcookie($name, '', time() - HOUR_IN_SECONDS, $path, $domain, is_ssl(), true);
            }
        }
    }

    private function set_token_cookie($value, $expires)
    {
        $path = defined('COOKIEPATH') && COOKIEPATH ? COOKIEPATH : '/';
        $domain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '';

        if (PHP_VERSION_ID >= 70300) {
            setcookie(self::TOKEN_COOKIE, $value, array(
                'expires' => $expires,
                'path' => $path,
                'domain' => $domain,
                'secure' => is_ssl(),
                'httponly' => true,
                'samesite' => 'Lax',
            ));
        } else {
            setcookie(self::TOKEN_COOKIE, $value, $expires, $path . '; samesite=Lax', $domain, is_ssl(), true);
        }
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

    private function normalize_mobile($value)
    {
        $digits = preg_replace('/\D+/', '', $this->normalize_digits($value));
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
}
