<?php

if (!defined('ABSPATH')) {
    exit;
}

final class DeDe_Store_Features
{
    private static $instance;

    private $account_type;

    private $profile;

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function activate()
    {
        $customer = get_role('customer');
        $caps = $customer ? $customer->capabilities : array('read' => true);

        foreach (array(
            'personal' => 'شخص',
            'store' => 'فروشگاه',
            'company' => 'شرکت',
        ) as $role => $label) {
            if (!get_role($role)) {
                add_role($role, $label, $caps);
            }
        }
    }

    private function __construct()
    {
        $this->account_type = new DeDe_Store_Features_Account_Type();
        $this->profile = new DeDe_Store_Features_Profile();

        add_action('init', array($this, 'disable_legacy_profile_endpoint'), 100);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'), 100);
        add_action('wp_footer', array($this, 'render_pending_account_type'), 5);
    }

    public function account_type()
    {
        return $this->account_type;
    }

    public function profile()
    {
        return $this->profile;
    }

    public function disable_legacy_profile_endpoint()
    {
        if (function_exists('user_information_manager_callback')) {
            remove_action('wp_ajax_user_information_manager', 'user_information_manager_callback');
            remove_action('wp_ajax_nopriv_user_information_manager', 'user_information_manager_callback');
        }
    }

    public function render_pending_account_type()
    {
        if (!is_user_logged_in() || !$this->account_type->is_pending(get_current_user_id())) {
            return;
        }

        echo '<div class="dede-account-type-modal" role="dialog" aria-modal="true" aria-label="انتخاب نوع حساب کاربری">';
        $this->account_type->render();
        echo '</div>';
    }

    public function enqueue_assets()
    {
        if (is_admin()) {
            return;
        }

        $css = DEDE_STORE_FEATURES_PATH . 'assets/css/customer-profile.css';
        $js = DEDE_STORE_FEATURES_PATH . 'assets/js/customer-profile.js';

        wp_enqueue_style(
            'dede-store-features-customer-profile',
            DEDE_STORE_FEATURES_URL . 'assets/css/customer-profile.css',
            array(),
            file_exists($css) ? (string) filemtime($css) : DEDE_STORE_FEATURES_VERSION
        );

        wp_enqueue_script(
            'dede-store-features-customer-profile',
            DEDE_STORE_FEATURES_URL . 'assets/js/customer-profile.js',
            array(),
            file_exists($js) ? (string) filemtime($js) : DEDE_STORE_FEATURES_VERSION,
            true
        );

        wp_localize_script('dede-store-features-customer-profile', 'DedeStoreFeatures', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'profileAction' => 'dede_store_save_profile',
            'citiesAction' => 'dede_store_get_cities',
            'accountTypeAction' => 'dede_store_select_account_type',
            'profileNonce' => is_user_logged_in() ? wp_create_nonce('dede_store_profile') : '',
            'accountTypeNonce' => wp_create_nonce('dede_store_account_type'),
            'accountTypeManaged' => true,
            'messages' => array(
                'genericError' => 'در ذخیره اطلاعات مشکلی رخ داد. دوباره تلاش کنید.',
                'required' => 'تکمیل این فیلد الزامی است.',
                'saved' => 'اطلاعات با موفقیت ذخیره شد.',
                'loadingCities' => 'در حال دریافت شهرها…',
            ),
        ));
    }
}
