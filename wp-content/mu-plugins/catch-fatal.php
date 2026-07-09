<?php
/**
 * Plugin Name: Local Fatal Catcher
 */

register_shutdown_function(function () {
    $error = error_get_last();

    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        $log = "=== FATAL ERROR ===\n";
        $log .= date('Y-m-d H:i:s') . "\n";
        $log .= print_r($error, true) . "\n\n";

        file_put_contents(WP_CONTENT_DIR . '/fatal-local.log', $log, FILE_APPEND);
    }
});