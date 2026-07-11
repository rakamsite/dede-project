<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('DeDe_Core_Logger')) {
    class DeDe_Core_Logger
    {
        /**
         * @var string[]
         */
        private $allowed_levels = array('debug', 'info', 'warning', 'error');

        /**
         * @var string[]
         */
        private $sensitive_keys = array(
            'password',
            'pass',
            'token',
            'access_token',
            'authorization',
            'secret',
            'api_key',
            'otp',
            'code',
        );

        public function debug($message, $context = array())
        {
            return $this->log('debug', $message, $context);
        }

        public function info($message, $context = array())
        {
            return $this->log('info', $message, $context);
        }

        public function warning($message, $context = array())
        {
            return $this->log('warning', $message, $context);
        }

        public function error($message, $context = array())
        {
            return $this->log('error', $message, $context);
        }

        public function log($level, $message, $context = array())
        {
            if (!$this->is_enabled()) {
                return false;
            }

            $normalized_level = $this->normalize_level($level);
            $formatted_message = $this->format_message($message);
            $log_entry = sprintf(
                '[DeDe Core][%s] %s',
                strtoupper($normalized_level),
                $formatted_message
            );

            $formatted_context = $this->format_context($context);
            if ('' !== $formatted_context) {
                $log_entry .= ' ' . $formatted_context;
            }

            error_log($log_entry);

            return true;
        }

        /**
         * @return bool
         */
        private function is_enabled()
        {
            if (defined('DEDE_CORE_LOGGING')) {
                $enabled = (bool) DEDE_CORE_LOGGING;
            } else {
                $enabled = false;

                if (defined('WP_DEBUG') && WP_DEBUG) {
                    $enabled = true;
                }

                if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
                    $enabled = true;
                }
            }

            if (function_exists('apply_filters')) {
                $enabled = apply_filters('dede_core_logging_enabled', $enabled, $this);
            }

            return (bool) $enabled;
        }

        /**
         * @param mixed $level
         * @return string
         */
        private function normalize_level($level)
        {
            if (!is_string($level)) {
                return 'info';
            }

            $level = strtolower(trim($level));

            if (!in_array($level, $this->allowed_levels, true)) {
                return 'info';
            }

            return $level;
        }

        /**
         * @param mixed $message
         * @return string
         */
        private function format_message($message)
        {
            if (is_string($message)) {
                return $message;
            }

            if (is_scalar($message) || null === $message) {
                return (string) $message;
            }

            return $this->safe_json_encode($message);
        }

        /**
         * @param mixed $context
         * @return string
         */
        private function format_context($context)
        {
            if (empty($context)) {
                return '';
            }

            $sanitized_context = $this->sanitize_context($context, 0);
            $encoded_context = $this->safe_json_encode($sanitized_context);

            if ('' === $encoded_context) {
                return '[context_encoding_failed]';
            }

            return $encoded_context;
        }

        /**
         * @param mixed $value
         * @param int   $depth
         * @return mixed
         */
        private function sanitize_context($value, $depth)
        {
            if ($depth >= 8) {
                return '[MAX_DEPTH_REACHED]';
            }

            if (is_array($value)) {
                $sanitized = array();

                foreach ($value as $key => $item) {
                    if ($this->is_sensitive_key($key)) {
                        $sanitized[$key] = '[REDACTED]';
                        continue;
                    }

                    $sanitized[$key] = $this->sanitize_context($item, $depth + 1);
                }

                return $sanitized;
            }

            if (is_object($value)) {
                return sprintf('[object:%s]', get_class($value));
            }

            if (is_resource($value)) {
                return '[resource]';
            }

            return $value;
        }

        /**
         * @param mixed $key
         * @return bool
         */
        private function is_sensitive_key($key)
        {
            if (!is_string($key) && !is_int($key)) {
                return false;
            }

            return in_array(strtolower((string) $key), $this->sensitive_keys, true);
        }

        /**
         * @param mixed $value
         * @return string
         */
        private function safe_json_encode($value)
        {
            $flags = 0;

            if (defined('JSON_UNESCAPED_UNICODE')) {
                $flags |= JSON_UNESCAPED_UNICODE;
            }

            if (defined('JSON_UNESCAPED_SLASHES')) {
                $flags |= JSON_UNESCAPED_SLASHES;
            }

            if (function_exists('wp_json_encode')) {
                $encoded = wp_json_encode($value, $flags);
            } else {
                $encoded = json_encode($value, $flags);
            }

            if (false === $encoded || null === $encoded) {
                return '';
            }

            return $encoded;
        }
    }
}
