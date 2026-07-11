<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('DeDe_Core_Dependencies')) {
    class DeDe_Core_Dependencies
    {
        /**
         * @return bool
         */
        public function is_woocommerce_active()
        {
            return $this->has_class('WooCommerce');
        }

        /**
         * @return bool
         */
        public function is_cmb2_available()
        {
            if ($this->has_function('new_cmb2_box')) {
                return true;
            }

            return $this->has_class('CMB2_Bootstrap_290')
                || $this->has_class('CMB2_Boxes')
                || $this->has_class('CMB2');
        }

        /**
         * @param string $class_name
         * @return bool
         */
        public function has_class($class_name)
        {
            if (!is_string($class_name) || '' === $class_name) {
                return false;
            }

            return class_exists($class_name, false);
        }

        /**
         * @param string $function_name
         * @return bool
         */
        public function has_function($function_name)
        {
            if (!is_string($function_name) || '' === $function_name) {
                return false;
            }

            return function_exists($function_name);
        }
    }
}
