<?php
/**
 * Plugin Name: DeDe Store Features
 * Description: قابلیت‌های مشتری و فروشگاه DeDe شامل نوع حساب، پروفایل مشتری و کنترل اطلاعات Checkout.
 * Version: 1.0.0
 * Author: DeDe
 * Text Domain: dede-store-features
 */

if (!defined('ABSPATH')) {
    exit;
}

define('DEDE_STORE_FEATURES_VERSION', '1.0.0');
define('DEDE_STORE_FEATURES_FILE', __FILE__);
define('DEDE_STORE_FEATURES_PATH', plugin_dir_path(__FILE__));
define('DEDE_STORE_FEATURES_URL', plugin_dir_url(__FILE__));

require_once DEDE_STORE_FEATURES_PATH . 'includes/class-dede-store-features-account-type.php';
require_once DEDE_STORE_FEATURES_PATH . 'includes/trait-dede-store-features-validation.php';
require_once DEDE_STORE_FEATURES_PATH . 'includes/trait-dede-store-features-location.php';
require_once DEDE_STORE_FEATURES_PATH . 'includes/class-dede-store-features-profile.php';
require_once DEDE_STORE_FEATURES_PATH . 'includes/class-dede-store-features.php';

function dede_store_features()
{
    return DeDe_Store_Features::instance();
}

function dede_store_features_render_customer_profile($context = 'account')
{
    dede_store_features()->profile()->render($context);
}

function dede_store_features_render_account_type_selector()
{
    dede_store_features()->account_type()->render();
}

function dede_store_features_is_profile_complete($user_id = 0)
{
    return dede_store_features()->profile()->is_complete($user_id);
}

function dede_store_features_prepare_account_type_selection($user_id)
{
    return dede_store_features()->account_type()->prepare_selection($user_id);
}

function dede_store_features_handle_legacy_account_type_request()
{
    dede_store_features()->account_type()->handle_legacy_request();
}

register_activation_hook(__FILE__, array('DeDe_Store_Features', 'activate'));
add_action('plugins_loaded', 'dede_store_features');
