<?php
/**
 * Plugin Name: DeDe Core
 * Description: Safe shared foundation for DeDe services and dependency checks.
 * Version: 0.1.0
 * Author: DeDe
 * Text Domain: dede-core
 */

if (!defined('ABSPATH')) {
    exit;
}

if (defined('DEDE_CORE_VERSION')) {
    return;
}

define('DEDE_CORE_VERSION', '0.1.0');
define('DEDE_CORE_FILE', __FILE__);
define('DEDE_CORE_DIR', plugin_dir_path(__FILE__));
define('DEDE_CORE_URL', plugin_dir_url(__FILE__));

if (!function_exists('dede_core_require_file')) {
    function dede_core_require_file($relative_path)
    {
        $file = DEDE_CORE_DIR . ltrim($relative_path, '/\\');

        if (!file_exists($file)) {
            return false;
        }

        require_once $file;

        return true;
    }
}

if (!dede_core_require_file('includes/class-dede-core-dependencies.php')) {
    return;
}

if (!dede_core_require_file('includes/class-dede-core.php')) {
    return;
}

if (!function_exists('dede_core_bootstrap')) {
    /**
     * Delay plugin bootstrap until WordPress has loaded active plugins.
     */
    function dede_core_bootstrap()
    {
        if (!class_exists('DeDe_Core', false)) {
            return;
        }

        DeDe_Core::instance()->boot();
    }
}

add_action('plugins_loaded', 'dede_core_bootstrap', 5);
