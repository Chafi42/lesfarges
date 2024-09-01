<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Dismiss_Notice_API')) {


    /**
     * WP Dismiss Notice.
     *
     * @package wp-dismiss-notice
     * @see https://github.com/w3guy/persist-admin-notices-dismissal
     */

    /**
     * Class Dismiss_Notice_API
     */
    class Dismiss_Notice_API
    {
        /**
         * @var LSC_Loader $loader 
         */
        var $loader;

        /**
         * @var Dismiss_Notice_API
         * @access private
         * @static
         */
        private static $_instance = null;

        /**
         * Méthode qui crée l'unique instance de la classe
         * si elle n'existe pas encore puis la retourne.
         *
         * 
         * @return Dismiss_Notice_API
         */
        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new Dismiss_Notice_API();
            }
            return self::$_instance;
        }

        /**
         * Constructor privé pour ne pas se lancer
         * si n'est pas instancié avec ::getInstance
         * 
         *
         */
        private function __construct()
        {
            $this->loader = new LSC_Loader();
        }

        /**
         * Init hooks.
         */
        public function init()
        {
            $this->loader->add_action(
                'admin_enqueue_scripts',
                $this,
                'load_script',
            );

            $this->loader->add_action(
                'wp_ajax_dismiss_admin_notice',
                $this,
                'dismiss_admin_notice'
            );

            // Run all registered hooks
            $this->loader->run();
        }

        /**
         * Enqueue javascript and variables.
         */
        public function load_script()
        {
            if (is_customize_preview()) {
                return;
            }

            wp_enqueue_script(
                'dismiss-notice',
                LSC_Helper()->get_uri_path('admin/assets/js/dismiss-notice.js'),
                array('jquery', 'common'),
                false,
                true
            );

            wp_localize_script(
                'dismissible-notices',
                'wp_dismiss_notice',
                [
                    'nonce'   => wp_create_nonce('wp-dismiss-notice'),
                    'ajaxurl' => admin_url('admin-ajax.php'),
                ]
            );
        }

        /**
         * Handles Ajax request to persist notices dismissal.
         * Uses check_ajax_referer to verify nonce.
         */
        public function dismiss_admin_notice()
        {
            $option_name        = isset($_POST['option_name']) ? sanitize_text_field(wp_unslash($_POST['option_name'])) : false;
            $dismissible_length = isset($_POST['dismissible_length']) ? sanitize_text_field(wp_unslash($_POST['dismissible_length'])) : 14;

            if ('forever' !== $dismissible_length) {
                // If $dismissible_length is not an integer default to 14.
                $dismissible_length = (0 === absint($dismissible_length)) ? 14 : $dismissible_length;
                $dismissible_length = strtotime(absint($dismissible_length) . ' days');
            }

            check_ajax_referer('wp-dismiss-notice', 'nonce');
            $this->set_admin_notice_cache($option_name, $dismissible_length);
            wp_die();
        }

        /**
         * Is admin notice active?
         *
         * @param string $arg data-dismissible content of notice.
         *
         * @return bool
         */
        public function is_admin_notice_active($arg)
        {
            $array = explode('-', $arg);
            array_pop($array);
            $option_name = implode('-', $array);
            $db_record   = self::get_admin_notice_cache($option_name);

            if ('forever' === $db_record) {
                return false;
            } elseif (absint($db_record) >= time()) {
                return false;
            } else {
                return true;
            }
        }

        /**
         * Returns admin notice cached timeout.
         *
         * @access public
         *
         * @param string|bool $id admin notice name or false.
         *
         * @return array|bool The timeout. False if expired.
         */
        public function get_admin_notice_cache($id = false)
        {
            if (!$id) {
                return false;
            }
            $cache_key = 'wpdn-' . md5($id);
            $timeout   = get_site_option($cache_key);
            $timeout   = 'forever' === $timeout ? time() + 60 : $timeout;

            if (empty($timeout) || time() > $timeout) {
                return false;
            }

            return $timeout;
        }

        /**
         * Sets admin notice timeout in site option.
         *
         * @access public
         *
         * @param string      $id       Data Identifier.
         * @param string|bool $timeout  Timeout for admin notice.
         *
         * @return bool
         */
        public function set_admin_notice_cache($id, $timeout)
        {
            $cache_key = 'wpdn-' . md5($id);
            update_site_option($cache_key, $timeout);

            return true;
        }
    }
}
