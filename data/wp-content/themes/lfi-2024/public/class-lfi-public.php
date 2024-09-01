<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('LFI_Public')) {

    /**
     * LFI_Public
     *
     *  Description
     *
     * @since   1.0.0
     * @package lfi-2024
     *
     */
    class LFI_Public
    {
        /**
         * @var LFI_Public
         * @access private
         * @static
         */
        private static $_instance = null;

        /**
         * @var LFI_Loader $loader  
         */
        protected $loader;

        /**
         * @var LFI_Helper $helper  
         */
        protected $helper;


        /**
         * Méthode qui crée l'unique instance de la classe
         * si elle n'existe pas encore puis la retourne.
         *
         * 
         * @return LFI_Public
         */
        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new LFI_Public();
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
            $this->loader = new LFI_Loader();
            $this->helper = LFI_Helper();
            $this->helper->require(array(
                'abs' => array(
                    'public/class-lfi-walker-nav-menu.php',
                    'public/class-lfi-public-functions.php',
                )
            ));
        }

        /**
         * Enqueue frontend styles
         *
         * @since 1.0.0
         * @package lfi-2024
         **/
        public function enqueue_styles()
        {
            // Bootstrap
            $ver = '5.3.2';
            wp_enqueue_style(
                'bootstrap-css',
                'https://cdn.jsdelivr.net/npm/bootstrap@' . $ver . '/dist/css/bootstrap.min.css',
                array(),
                $ver
            );

            // Font Awesome
            wp_enqueue_style(
                'font-awesome',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css',
                array(),
                '6.3.0'
            );

            // Loading
            wp_enqueue_style(
                $this->helper->name() . '-loading',
                $this->helper->get_uri_path('public/css/loading.css'),
                array(),
                '1.0.0'
            );

            // Extras Bootstrap classes
            wp_enqueue_style(
                $this->helper->name() . '-bootstrap-extras',
                $this->helper->get_uri_path('public/css/bootstrap-extras.css'),
                array(),
                $this->helper->version()
            );

            // Bootstrap burger-menu classes
            wp_enqueue_style(
                $this->helper->name() . '-burger-menu',
                $this->helper->get_uri_path('public/css/burger-menu.css'),
                array(),
                $this->helper->version()
            );

            // Nav menu classes
            wp_enqueue_style(
                $this->helper->name() . '-nav-menu',
                $this->helper->get_uri_path('public/css/nav-menu.css'),
                array(),
                $this->helper->version()
            );

            $handle = $this->helper->name() . '-stylesheet';
            wp_enqueue_style(
                $handle,
                $this->helper->get_uri_path('style.css'),
                array('dashicons'),
                $this->helper->version()
            );

            // Theme settings colors
            // Using Gutenberg classes
            wp_add_inline_style(
                $handle,
                LFI_Helper()->color_style()
            );
            
            // Loading - inline from settings
            $settings = LFI_Settings::getInstance();
            wp_add_inline_style(
                $this->helper->name() . '-stylesheet',
                $this->helper->loader_style()
            );
            
            // Font - inline from settings
            wp_add_inline_style(
                $this->helper->name() . '-stylesheet',
                $this->helper->font_style()
            );
            
            // Heading - inline from settings
            wp_add_inline_style(
                $this->helper->name() . '-stylesheet',
                $this->helper->heading_style()
            );

            // Menu - inline from settings
            wp_add_inline_style(
                $this->helper->name() . '-stylesheet',
                $this->helper->menu_style()
            );
        }

        /**
         * Enqueue frontend scripts
         *
         * @since 1.0.0
         * @package lfi-2024
         **/
        public function enqueue_scripts()
        {
            // Bootstrap
            $ver = '5.3.2';
            wp_enqueue_script(
                'bootstrap-js',
                'https://cdn.jsdelivr.net/npm/bootstrap@' . $ver . '/dist/js/bootstrap.bundle.min.js',
                array('jquery'),
                $ver,
                true
            );

            wp_enqueue_script(
                'lfi-js',
                $this->helper->get_uri_path('public/js/lfi.js'),
                array('jquery', 'jquery-effects-core'),
                '1.0.0',
                true
            );

            // wp_enqueue_script(
            //     'functions-js',
            //     $this->helper->get_uri_path('public/js/functions.js'),
            //     array('jquery'),
            //     '1.0.0',
            //     true
            // );

            // wp_enqueue_script(
            //     'loading-js',
            //     $this->helper->get_uri_path('public/js/loading.js'),
            //     array('jquery'),
            //     '1.0.0',
            //     true
            // );
        }
    }
}
