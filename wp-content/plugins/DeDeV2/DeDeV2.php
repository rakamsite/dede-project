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

if (!defined('dede_v2_path')) {
    define("dede_v2_path", plugin_dir_path(__FILE__));
}
if (!defined('dede_v2_url')) {
    define("dede_v2_url", plugin_dir_url(__FILE__));
}
if (!defined('dede_v2_base')) {
    define("dede_v2_base", "plugins/" . plugin_basename(__DIR__));
}

$dede_v2_autoload = __DIR__ . '/vendor/autoload.php';

if (file_exists($dede_v2_autoload)) {
    require_once $dede_v2_autoload;
}

if (class_exists('dede_v2_run_plugin')) {
    (new dede_v2_run_plugin())->run();
}

register_activation_hook(__FILE__, function () {
    $abandoned_cart_file = __DIR__ . '/src/classes/abandoned_cart/dede_v2_abandoned_cart.php';
    $stock_quantity_file = __DIR__ . '/src/classes/stock_quantity_handler/dede_v2_stock_quantity.php';

    if (!class_exists(dede_v2_abandoned_cart::class) && file_exists($abandoned_cart_file)) {
        require_once $abandoned_cart_file;
    }
    if (!class_exists(dede_v2_stock_quantity::class) && file_exists($stock_quantity_file)) {
        require_once $stock_quantity_file;
    }

    if (!class_exists(dede_v2_abandoned_cart::class) || !class_exists(dede_v2_stock_quantity::class)) {
        return;
    }

    $abandoned_cart = new dede_v2_abandoned_cart();
    $abandoned_cart->create_woocommerce_abandoned_cart_database();

    $stock_quantity = new dede_v2_stock_quantity();
    $stock_quantity->create_woocommerce_quantity_database();
});
