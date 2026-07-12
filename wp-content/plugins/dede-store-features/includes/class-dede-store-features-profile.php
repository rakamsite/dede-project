<?php

if (!defined('ABSPATH')) {
    exit;
}

class DeDe_Store_Features_Profile
{
    use DeDe_Store_Features_Validation;
    use DeDe_Store_Features_Location;

    private $allowed_roles = array('personal', 'store', 'company');

    public function __construct()
    {
        add_action('wp_ajax_dede_store_save_profile', array($this, 'ajax_save'));
        add_action('wp_ajax_dede_store_get_cities', array($this, 'ajax_cities'));
        add_filter('woocommerce_checkout_posted_data', array($this, 'inject_checkout_data'));
        add_action('woocommerce_after_checkout_validation', array($this, 'validate_checkout_profile'), 10, 2);
        add_action('woocommerce_checkout_create_order', array($this, 'copy_profile_to_order'), 10, 2);
    }

    public function render($context = 'account')
    {
        if (!is_user_logged_in()) {
            echo '<div class="woocommerce-info">برای تکمیل اطلاعات ابتدا وارد حساب کاربری شوید.</div>';
            return;
        }

        $context = 'checkout' === $context ? 'checkout' : 'account';
        $user_id = get_current_user_id();
        $role = $this->get_account_type($user_id);
        if (!$role) {
            echo '<div class="woocommerce-error">نوع حساب کاربری شما مشخص نشده است. مرحله انتخاب نوع حساب را کامل کنید.</div>';
            return;
        }

        $profile = $this->get_profile($user_id);
        $states = $this->get_state_records();
        $cities_by_state = $this->get_all_cities_by_state();
        $billing_cities = $cities_by_state[$profile['billing_state']] ?? array();
        $shipping_cities = $cities_by_state[$profile['shipping_state']] ?? array();
        $is_complete = $this->is_complete($user_id);
        $start_step = $is_complete ? 3 : ($this->identity_is_complete($profile, $role) ? 2 : 1);
        $role_labels = array('personal' => 'شخص', 'store' => 'فروشگاه', 'company' => 'شرکت');
        $today = current_datetime();
        $jalali_today = $this->gregorian_to_jalali((int) $today->format('Y'), (int) $today->format('n'), (int) $today->format('j'));
        $max_birth_year = ((int) $jalali_today[0]) - 10;

        include DEDE_STORE_FEATURES_PATH . 'templates/customer-profile.php';
    }

    public function ajax_cities()
    {
        check_ajax_referer('dede_store_profile', 'nonce');
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'ابتدا وارد حساب کاربری شوید.'), 401);
        }
        $cities = $this->get_cities_for_state(sanitize_text_field(wp_unslash($_POST['state'] ?? '')));
        wp_send_json_success(array('cities' => array_map(static function ($city) {
            return array('id' => (string) $city['id'], 'name' => $city['name']);
        }, $cities)));
    }

    public function ajax_save()
    {
        check_ajax_referer('dede_store_profile', 'nonce');
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'ابتدا وارد حساب کاربری شوید.'), 401);
        }

        $user_id = get_current_user_id();
        $role = $this->get_account_type($user_id);
        if (!$role) {
            wp_send_json_error(array('message' => 'نوع حساب کاربری مشخص نیست.'), 422);
        }

        $validation = $this->validate_request(wp_unslash($_POST), $role, $user_id);
        if ($validation['errors']) {
            wp_send_json_error(array(
                'message' => 'لطفاً خطاهای فرم را اصلاح کنید.',
                'errors' => $validation['errors'],
            ), 422);
        }

        $saved = $this->save_profile($user_id, $role, $validation['data']);
        if (is_wp_error($saved)) {
            wp_send_json_error(array('message' => $saved->get_error_message()), 500);
        }
        wp_send_json_success(array(
            'message' => 'اطلاعات با موفقیت ذخیره شد.',
            'complete' => true,
            'profile' => $this->review_payload($user_id),
        ));
    }

    public function is_complete($user_id = 0)
    {
        $user_id = $user_id ? absint($user_id) : get_current_user_id();
        $role = $user_id ? $this->get_account_type($user_id) : '';
        if (!$role) {
            return false;
        }
        return !$this->validate_profile_array($this->get_profile($user_id), $role, $user_id);
    }

    public function inject_checkout_data($data)
    {
        if (!is_user_logged_in() || !$this->is_complete()) {
            return $data;
        }
        $profile = $this->get_profile(get_current_user_id());
        return array_merge($data, array(
            'billing_first_name' => $profile['first_name'],
            'billing_last_name' => $profile['last_name'],
            'billing_company' => $profile['company_name'],
            'billing_country' => 'IR',
            'billing_state' => $profile['billing_state'],
            'billing_city' => $profile['billing_city'],
            'billing_postcode' => $profile['billing_postcode'],
            'billing_phone' => $profile['billing_phone'],
            'billing_address_1' => $profile['billing_address_1'],
            'shipping_first_name' => $profile['first_name'],
            'shipping_last_name' => $profile['last_name'],
            'shipping_company' => $profile['company_name'],
            'shipping_country' => 'IR',
            'shipping_state' => $profile['shipping_state'],
            'shipping_city' => $profile['shipping_city'],
            'shipping_postcode' => $profile['shipping_postcode'],
            'shipping_phone' => $profile['shipping_phone'],
            'shipping_address_1' => $profile['shipping_address_1'],
        ));
    }

    public function validate_checkout_profile($data, $errors)
    {
        if (is_user_logged_in() && !$this->is_complete()) {
            $errors->add('dede_profile_incomplete', 'برای ثبت سفارش، اطلاعات حساب کاربری را کامل و ذخیره کنید.');
        }
    }

    public function copy_profile_to_order($order, $data)
    {
        if (!is_user_logged_in() || !$this->is_complete()) {
            return;
        }
        $profile = $this->get_profile(get_current_user_id());
        $order->set_address(array(
            'first_name' => $profile['first_name'],
            'last_name' => $profile['last_name'],
            'company' => $profile['company_name'],
            'address_1' => $profile['billing_address_1'],
            'city' => $profile['billing_city_name'],
            'state' => $profile['billing_state'],
            'postcode' => $profile['billing_postcode'],
            'country' => 'IR',
            'phone' => $profile['billing_phone'],
            'email' => $profile['email'],
        ), 'billing');
        $order->set_address(array(
            'first_name' => $profile['first_name'],
            'last_name' => $profile['last_name'],
            'company' => $profile['company_name'],
            'address_1' => $profile['shipping_address_1'],
            'city' => $profile['shipping_city_name'],
            'state' => $profile['shipping_state'],
            'postcode' => $profile['shipping_postcode'],
            'country' => 'IR',
            'phone' => $profile['shipping_phone'],
        ), 'shipping');
        $order->update_meta_data('_dede_customer_type', $profile['account_type']);
        $order->update_meta_data('_dede_national_code_', $profile['national_code']);
        $order->update_meta_data('_dede_national_id_', $profile['national_id']);
        $order->update_meta_data('_dede_shop_name_', $profile['store_name']);
    }

    public function get_account_type($user_id)
    {
        if ('1' === (string) get_user_meta($user_id, '_dede_account_type_pending_', true)) {
            return '';
        }
        $type = sanitize_key((string) get_user_meta($user_id, 'customer_type', true));
        if (in_array($type, $this->allowed_roles, true)) {
            return $type;
        }
        $user = get_userdata($user_id);
        foreach ($user ? (array) $user->roles : array() as $role) {
            if (in_array($role, $this->allowed_roles, true)) {
                return $role;
            }
        }
        return '';
    }

    public function get_profile($user_id)
    {
        $user = get_userdata($user_id);
        $birthday = (string) get_user_meta($user_id, '_dede_birthday_', true);
        $parts = preg_split('#[/\-]#', $birthday);
        $stored_gender = (string) get_user_meta($user_id, '_dede_Gender_', true);
        $gender = $this->normalize_gender($stored_gender);
        if ($stored_gender && $gender !== $stored_gender && in_array($gender, array('آقای', 'خانم'), true)) {
            update_user_meta($user_id, '_dede_Gender_', $gender);
        }

        $birthday_timestamp = (string) get_user_meta($user_id, '_dede_birthday_timestamp_', true);
        if ($birthday && ctype_digit($birthday_timestamp) && strlen($birthday_timestamp) <= 10) {
            $birthday_timestamp = (string) (((int) $birthday_timestamp) * 1000);
            update_user_meta($user_id, '_dede_birthday_timestamp_', $birthday_timestamp);
        }

        return array(
            'account_type' => $this->get_account_type($user_id),
            'mobile' => $this->get_verified_mobile($user),
            'first_name' => $user ? (string) $user->first_name : '',
            'last_name' => $user ? (string) $user->last_name : '',
            'email' => $user ? (string) $user->user_email : '',
            'gender' => $gender,
            'national_code' => (string) get_user_meta($user_id, '_dede_national_code_', true),
            'national_id' => (string) get_user_meta($user_id, '_dede_national_id_', true),
            'company_name' => (string) get_user_meta($user_id, 'billing_company', true),
            'store_name' => (string) get_user_meta($user_id, '_dede_shop_name_', true),
            'economic_code' => (string) get_user_meta($user_id, '_dede_Economic_Code_', true),
            'telegram' => '98' === (string) get_user_meta($user_id, '_dede_Telegram_', true) ? '' : (string) get_user_meta($user_id, '_dede_Telegram_', true),
            'birthday' => $birthday,
            'birthday_year' => $parts[0] ?? '',
            'birthday_month' => isset($parts[1]) ? ltrim($parts[1], '0') : '',
            'birthday_day' => isset($parts[2]) ? ltrim($parts[2], '0') : '',
            'billing_state' => (string) get_user_meta($user_id, 'billing_state', true),
            'billing_city' => (string) get_user_meta($user_id, 'billing_city', true),
            'billing_state_name' => (string) get_user_meta($user_id, 'state_custom_billing', true),
            'billing_city_name' => (string) get_user_meta($user_id, 'city_custom_billing', true),
            'billing_postcode' => (string) get_user_meta($user_id, 'billing_postcode', true),
            'billing_phone' => (string) get_user_meta($user_id, 'billing_phone', true),
            'billing_address_1' => (string) get_user_meta($user_id, 'billing_address_1', true),
            'shipping_state' => (string) get_user_meta($user_id, 'shipping_state', true),
            'shipping_city' => (string) get_user_meta($user_id, 'shipping_city', true),
            'shipping_state_name' => (string) get_user_meta($user_id, 'state_custom_shipping', true),
            'shipping_city_name' => (string) get_user_meta($user_id, 'city_custom_shipping', true),
            'shipping_postcode' => (string) get_user_meta($user_id, 'shipping_postcode', true),
            'shipping_phone' => (string) get_user_meta($user_id, 'shipping_phone', true),
            'shipping_address_1' => (string) get_user_meta($user_id, 'shipping_address_1', true),
            'same_as_billing' => $this->addresses_match($user_id),
        );
    }

    private function save_profile($user_id, $role, $data)
    {
        $mobile = $this->get_verified_mobile(get_userdata($user_id));
        $billing_state = $this->state_record($data['billing_state']);
        $shipping_state = $this->state_record($data['shipping_state']);
        $billing_city = get_term($data['billing_city'], 'city_country');
        $shipping_city = get_term($data['shipping_city'], 'city_country');
        if (!$billing_state || !$shipping_state || is_wp_error($billing_city) || is_wp_error($shipping_city)) {
            return new WP_Error('invalid_address', 'اطلاعات استان یا شهر معتبر نیست.');
        }

        do_action('dede_store_features_before_profile_save', $user_id, $role, $data);
        $updated = wp_update_user(array(
            'ID' => $user_id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'nickname' => $mobile,
            'user_email' => $data['email'],
        ));
        if (is_wp_error($updated)) {
            return $updated;
        }

        $meta = array(
            'customer_type' => $role,
            'custom_phone_number' => $mobile,
            '_dede_national_code_' => 'company' === $role ? '' : $data['national_code'],
            '_dede_national_id_' => 'company' === $role ? $data['national_id'] : '',
            '_dede_Gender_' => $data['gender'],
            '_dede_Telegram_' => $data['telegram'],
            '_dede_birthday_' => $data['birthday'],
            '_dede_birthday_timestamp_' => $data['birthday_timestamp'],
            '_dede_shop_name_' => 'store' === $role ? $data['store_name'] : '',
            '_dede_Economic_Code_' => 'company' === $role ? $data['economic_code'] : '',
            'billing_first_name' => $data['first_name'],
            'billing_last_name' => $data['last_name'],
            'billing_company' => 'company' === $role ? $data['company_name'] : '',
            'billing_country' => 'IR',
            'billing_state' => $data['billing_state'],
            'billing_city' => (string) $data['billing_city'],
            'billing_postcode' => $data['billing_postcode'],
            'billing_phone' => $data['billing_phone'],
            'billing_address_1' => $data['billing_address_1'],
            'state_custom_billing' => $billing_state['name'],
            'city_custom_billing' => $billing_city->name,
            'shipping_first_name' => $data['first_name'],
            'shipping_last_name' => $data['last_name'],
            'shipping_company' => 'company' === $role ? $data['company_name'] : '',
            'shipping_country' => 'IR',
            'shipping_state' => $data['shipping_state'],
            'shipping_city' => (string) $data['shipping_city'],
            'shipping_postcode' => $data['shipping_postcode'],
            'shipping_phone' => $data['shipping_phone'],
            'shipping_address_1' => $data['shipping_address_1'],
            'state_custom_shipping' => $shipping_state['name'],
            'city_custom_shipping' => $shipping_city->name,
            'custom_first_name' => $data['first_name'],
            'custom_last_name' => $data['last_name'],
            '_dede_profile_complete_' => '1',
            '_dede_profile_completed_at_' => current_time('mysql'),
        );
        foreach ($meta as $key => $value) {
            update_user_meta($user_id, $key, $value);
        }
        do_action('dede_store_features_after_profile_save', $user_id, $role, $data, $meta);
        return true;
    }

    private function identity_is_complete($profile, $role)
    {
        $subset = $profile;
        foreach (array('billing_state', 'billing_postcode', 'billing_phone', 'billing_address_1', 'shipping_state', 'shipping_postcode', 'shipping_phone', 'shipping_address_1') as $key) {
            $subset[$key] = 'ok';
        }
        $subset['billing_city'] = 1;
        $subset['shipping_city'] = 1;
        foreach (array_keys($this->validate_profile_array($subset, $role, get_current_user_id())) as $field) {
            if (0 !== strpos($field, 'billing_') && 0 !== strpos($field, 'shipping_')) {
                return false;
            }
        }
        return true;
    }

    private function review_payload($user_id)
    {
        $profile = $this->get_profile($user_id);
        $labels = array('personal' => 'شخص', 'store' => 'فروشگاه', 'company' => 'شرکت');
        return array(
            'account_type' => $labels[$profile['account_type']] ?? '',
            'identity' => 'company' === $profile['account_type'] ? $profile['company_name'] : trim($profile['first_name'] . ' ' . $profile['last_name']),
            'billing_address' => trim($profile['billing_state_name'] . '، ' . $profile['billing_city_name'] . '، ' . $profile['billing_address_1'], '، '),
            'shipping_address' => trim($profile['shipping_state_name'] . '، ' . $profile['shipping_city_name'] . '، ' . $profile['shipping_address_1'], '، '),
        );
    }

    private function addresses_match($user_id)
    {
        foreach (array('state', 'city', 'postcode', 'phone', 'address_1') as $key) {
            if ((string) get_user_meta($user_id, 'billing_' . $key, true) !== (string) get_user_meta($user_id, 'shipping_' . $key, true)) {
                return false;
            }
        }
        return true;
    }

    private function get_verified_mobile($user)
    {
        if (!$user) {
            return '';
        }
        foreach (array(
            get_user_meta($user->ID, 'custom_phone_number', true),
            get_user_meta($user->ID, 'nickname', true),
            $user->user_login,
        ) as $candidate) {
            $mobile = $this->normalize_mobile($candidate);
            if ($mobile) {
                return $mobile;
            }
        }
        return '';
    }
}
