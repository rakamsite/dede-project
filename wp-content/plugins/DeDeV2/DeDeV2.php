<?php
/**
 * Plugin Name: پلاگین توسعه سایت DeDe ورژن 2
 * Plugin URI: https://wobika.com
 * Description: سبد خرید رها شده ، ارسال پیامک ووکامرس ، استوزی ،ارسال پیام موجودی برای کاربران ثبت شده، هوک تبدیل تاریخ میلادی
 * Version: 1.0.2
 * Author: erfun.mnjd form WobikaTeam
 * Author URI: https://wobika.com
 * WC requires at least: 6.0.0
 * WC tested up to: 8.0
 */

use classes\abandoned_cart\dede_v2_abandoned_cart;
use classes\stock_quantity_handler\dede_v2_stock_quantity;

define( "dede_v2_path", plugin_dir_path( __FILE__ ) );
define( "dede_v2_url", plugin_dir_url( __FILE__ ) );
define( "dede_v2_base", "plugins/" . plugin_basename( __DIR__ ) );

require_once __DIR__ . '/vendor/autoload.php';
(new dede_v2_run_plugin())->run();
register_activation_hook(__FILE__, function () {
    if (!class_exists('dede_v2_abandoned_cart')) {
        require_once __DIR__ . '/src/classes/abandoned_cart/dede_v2_abandoned_cart.php';
    }
    if (!class_exists('dede_v2_stock_quantity')) {
        require_once __DIR__ . '/src/classes/stock_quantity_handler/dede_v2_stock_quantity.php';
    }
    $abandoned_cart = new dede_v2_abandoned_cart();
    $abandoned_cart->create_woocommerce_abandoned_cart_database();

    $stock_quantity = new dede_v2_stock_quantity();
    $stock_quantity->create_woocommerce_quantity_database();
});