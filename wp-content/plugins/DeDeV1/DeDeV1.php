<?php
/**
 * Plugin Name: پلاگین توسعه سایت DeDe ورژن 1
 * Plugin URI: https://wobika.com
 * Description: نسخه اول توسعه پلاگین DeDe . ورود اطلاعات از اکسل برای ووکامرس ، شخصی سازی ایمیل ارسل فاکتور ووکامرس ، خروجی گرفتن از تمام محصولات ووکامرس به صورت  PDF
 * Version: 1.0.0
 * Author: erfun.mnjd form WobikaTeam
 * Author URI: https://wobika.com
 * WC requires at least: 6.0.0
 * WC tested up to: 8.0
 */

use dede_dev_run_plugin\dede_dev_run_plugin;


if (!defined('plugin_path')) {
    define("plugin_path", plugin_dir_path(__FILE__));
}
if (!defined('plugin_url')) {
    define("plugin_url", plugin_dir_url(__FILE__));
}
if (!defined('basename')) {
    define("basename", "plugins/" . plugin_basename(__DIR__));
}

$dede_v1_autoload = __DIR__ . '/vendor/autoload.php';

if (file_exists($dede_v1_autoload)) {
    require_once $dede_v1_autoload;
}

if (class_exists(dede_dev_run_plugin::class)) {
    (new dede_dev_run_plugin())->run();
}
