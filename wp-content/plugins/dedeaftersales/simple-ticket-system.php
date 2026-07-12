<?php
/*
Plugin Name: Simple Ticket System
Description: A simple ticket system for WooCommerce with user ticket submission, admin management, and email notifications.
Version: 1.3.1
Author: sajadtorabi
Text Domain: simple-ticket
*/

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('STS_PLUGIN_FILE')) {
    define('STS_PLUGIN_FILE', __FILE__);
}

define('STS_PLUGIN_DIR', plugin_dir_path(STS_PLUGIN_FILE));

/**
 * Check whether the current front-end page contains one of this plugin's shortcodes.
 * This prevents after-sales assets from leaking into unrelated site pages.
 *
 * @param string[] $shortcodes
 * @return bool
 */
function sts_page_has_shortcode(array $shortcodes) {
    if (is_admin()) {
        return false;
    }

    global $post;
    if (!($post instanceof WP_Post) || !is_string($post->post_content)) {
        return false;
    }

    foreach ($shortcodes as $shortcode) {
        if (has_shortcode($post->post_content, $shortcode)) {
            return true;
        }
    }

    return false;
}

/**
 * Load plugin textdomains.
 */
function sts_load_textdomain() {
    load_plugin_textdomain('simple-ticket', false, dirname(plugin_basename(STS_PLUGIN_FILE)) . '/languages');
}
add_action('plugins_loaded', 'sts_load_textdomain');

require_once STS_PLUGIN_DIR . 'includes/tickets.php';
require_once STS_PLUGIN_DIR . 'includes/warranty.php';
require_once STS_PLUGIN_DIR . 'includes/warranty-part.php';
