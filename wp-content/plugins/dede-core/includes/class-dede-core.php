<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('DeDe_Core')) {
    class DeDe_Core
    {
        /**
         * @var DeDe_Core|null
         */
        private static $instance = null;

        /**
         * @var DeDe_Core_Dependencies
         */
        private $dependencies;

        /**
         * @var DeDe_Core_Logger
         */
        private $logger;

        /**
         * @var bool
         */
        private $booted = false;

        /**
         * @return DeDe_Core
         */
        public static function instance()
        {
            if (null === self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        private function __construct()
        {
            $this->dependencies = new DeDe_Core_Dependencies();
            $this->logger = new DeDe_Core_Logger();
        }

        private function __clone()
        {
        }

        public function __wakeup()
        {
            throw new Exception('DeDe_Core cannot be unserialized.');
        }

        public function boot()
        {
            if ($this->booted) {
                return;
            }

            $this->booted = true;

            do_action('dede_core_loaded', $this);
        }

        /**
         * @return DeDe_Core_Dependencies
         */
        public function dependencies()
        {
            return $this->dependencies;
        }

        /**
         * @return DeDe_Core_Logger
         */
        public function logger()
        {
            return $this->logger;
        }

        /**
         * @return bool
         */
        public function is_booted()
        {
            return $this->booted;
        }
    }
}
