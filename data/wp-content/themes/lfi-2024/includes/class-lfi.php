<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('LFI')) {

    /**
     * LFI
     *
     *  Master class
     *
     * @since   1.0.0
     * @package lfi-2024
     *
     */
    class LFI
    {
        /**
         * @var LFI
         * @access private
         * @static
         */
        private static $_instance = null;

        /**
         * @var Object $helper LFI_Helper class 
         */
        protected $helper;

        /**
         * The loader that's responsible for maintaining and registering all hooks that power
         * the plugin.
         *
         * @var LFI_Loader $loader Maintains and registers all hooks for the plugin.
         */
        protected $loader;

        /**
         * @var LFI_Admin $LFI_Admin  
         */
        protected $LFI_Admin;

        /**
         * Méthode qui crée l'unique instance de la classe
         * si elle n'existe pas encore puis la retourne.
         *
         * @param array $args Description
         * @return LFI
         * @since 1.0.0
         * @access public
         */
        public static function getInstance($args = array())
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new LFI($args = array());
            }
            return self::$_instance;
        }

        /**
         * Constructor privé pour ne pas se lancer
         * si n'est pas instancié avec ::getInstance
         *
         * @param array $args Description
         * @since 1.0.0
         * @access private
         */
        private function __construct($args = array())
        {
            $this->load_dependencies();
            $this->loader = new LFI_Loader();
            $this->set_locale();
            $this->define_admin_hooks();
            $this->define_front_hooks();
            
            $this->run();
        }

        /**
         * Charge les dépendances pour ce thème
         *
         * @since 1.0.0
         * @access private
         **/
        private function load_dependencies()
        {
            LFI_Helper()->require(array(
                'abs' => array(
                    'includes/class-lfi-loader.php',
                    'includes/class-lfi-i18n.php',
                    'admin/class-lfi-admin.php',
                    'public/class-lfi-public.php',
                )
            ));
        }

        /**
         * Set locale
         *
         * @since 1.0.0
         * @access private
         **/
        private function set_locale()
        {
            $lfi_i18n = new LFI_i18n();
            $this->loader->add_action(
                'after_setup_theme',
                $lfi_i18n,
                'load_theme_textdomain'
            );
        }

        /**
         * Register all of the hooks related to the admin area functionality
         * of the theme.
         *
         * @since 1.0.0
         * @package lfi-2024
         * @access private
         */
        private function define_admin_hooks()
        {
            $lfi_admin = LFI_Admin::getInstance();

            $adminActions = array(
                array(
                    'admin_enqueue_scripts',
                    $lfi_admin,
                    'enqueue_styles'
                ),
                array(
                    'admin_enqueue_scripts',
                    $lfi_admin,
                    'enqueue_scripts'
                ),
                array(
                    'after_setup_theme',
                    $lfi_admin,
                    'add_theme_support'
                ),
                array(
                    'after_setup_theme',
                    $lfi_admin,
                    'register_nav_menus'
                ),
            );

            $adminFilters = array(
                array(
                    'pw_google_api_key',
                    $lfi_admin,
                    'cmb2_maps_api_key'
                ),
            );

            foreach ($adminActions as $key => $action) {
                $this->loader->add_action($action[0], $action[1], $action[2]);
            }

            foreach ($adminFilters as $key => $filter) {
                if (!is_array($filter)) continue;
                $action[3] = isset($action[3]) ? $action[3] : 10;
                $action[4] = isset($action[4]) ? $action[4] : 1;
                $this->loader->add_filter(
                    $action[0],
                    $action[1],
                    $action[2],
                    $action[3],
                    $action[4]
                );
            }
        }

        /**
         * Register all hooks related to the frontend/visitors area
         *
         * @since 1.0.0
         * @package lfi-2024
         * @access private
         **/
        public function define_front_hooks()
        {
            $lfi_public = LFI_Public::getInstance();

            $publicActions = array(
                array(
                    'wp_enqueue_scripts',
                    $lfi_public,
                    'enqueue_styles'
                ),
                array(
                    'wp_enqueue_scripts',
                    $lfi_public,
                    'enqueue_scripts',
                ),
            );

            $publicFilters = array();

            foreach ($publicActions as $key => $action) {
                if (!is_array($action)) continue;
                $this->loader->add_action($action[0], $action[1], $action[2]);
            }

            foreach ($publicFilters as $key => $filter) {
                if (!is_array($filter)) continue;
                $action[3] = isset($action[3]) ? $action[3] : 10;
                $action[4] = isset($action[4]) ? $action[4] : 1;
                $this->loader->add_filter(
                    $action[0],
                    $action[1],
                    $action[2],
                    $action[3],
                    $action[4]
                );
            }
        }

        /**
         * Retrieve loader class
         *
         * @return LFI_Loader
         **/
        public static function get_loader()
        {
            return self::$loader;
        }

        /**
         * Run the loader to execute all of the hooks with WordPress.
         *
         * @since    1.0.0
         */
        public function run()
        {
            $this->loader->run();
        }
    }
}
