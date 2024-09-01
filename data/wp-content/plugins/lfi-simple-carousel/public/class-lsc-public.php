<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('LSC_Public')) {

    /**
     * The public-facing functionality of the plugin.
     *
     * Defines the plugin name, version, and two examples hooks for how to
     * enqueue the public-facing stylesheet and JavaScript.
     *
     * @since 	   1.0.0
     * @package    lfi-simple-carousel
     * @subpackage lfi-simple-carousel/public
     * @author     LFI <contact@lafabriqueinfo.fr>
     * 
     */
    class LSC_Public
    {

        /**
         * The ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $plugin_name    The ID of this plugin.
         */
        private $plugin_name;

        /**
         * The version of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $version    The current version of this plugin.
         */
        private $version;

        /**
         * @var LSC_Loader $loader  
         */
        protected $loader;

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         * @param      string    $plugin_name       The name of the plugin.
         * @param      string    $version    The version of this plugin.
         */
        public function __construct($plugin_name, $version)
        {

            $this->plugin_name = $plugin_name;
            $this->version = $version;

            $this->loader = new LSC_Loader();
            // Shortcode
            $this->loader->add_action(
                'init',
                $this,
                'add_carousel_shortcode'
            );
            $this->loader->run();
        }

        /**
         * Register the stylesheets for the public-facing side of the site.
         *
         * @since    1.0.0
         */
        public function enqueue_styles()
        {

            /**
             * This function is provided for demonstration purposes only.
             *
             * An instance of this class should be passed to the run() function
             * defined in LSC_Loader as all of the hooks are defined
             * in that particular class.
             *
             * The LSC_Loader will then create the relationship
             * between the defined hooks and the functions defined in this
             * class.
             */
            // Bootstrap
            $ver = '5.3.2';
            wp_enqueue_style(
                'bootstrap-css',
                'https://cdn.jsdelivr.net/npm/bootstrap@' . $ver . '/dist/css/bootstrap.min.css',
                array(),
                $ver
            );

            wp_enqueue_style(
                $this->plugin_name,
                plugin_dir_url(__FILE__) . 'css/lsc-public.css',
                array(),
                $this->version,
                'all'
            );
        }

        /**
         * Register the JavaScript for the public-facing side of the site.
         *
         * @since    1.0.0
         */
        public function enqueue_scripts()
        {

            /**
             * This function is provided for demonstration purposes only.
             *
             * An instance of this class should be passed to the run() function
             * defined in LSC_Loader as all of the hooks are defined
             * in that particular class.
             *
             * The LSC_Loader will then create the relationship
             * between the defined hooks and the functions defined in this
             * class.
             */

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
                $this->plugin_name,
                plugin_dir_url(__FILE__) . 'js/lsc-public.js',
                array('jquery'),
                $this->version,
                true
            );
        }

        /**
         * Add Carousel|Slider shortcode
         *
         * Undocumented function long description
         *
         * @param Type $var Description
         * @return type
         * @throws conditon
         **/
        public function add_carousel_shortcode()
        {
            $shortcode_name = LSC_Helper()->get_shortcode_name();
            add_shortcode($shortcode_name, array($this, 'slider_shortcode_callback'));
        }

        /**
         * Render Slider shortcode
         *
         * Undocumented function long description
         *
         * @param array  $atts Description
         * @param string $content Description
         * @param string $shortcode_tag Description
         * @return string
         **/
        public function slider_shortcode_callback($atts, $content, $shortcode_tag)
        {
            $atts = shortcode_atts(array(
                'id'   => 0,
                'slug' => 'lsc-public-display'
            ), $atts);
            $tmplLoader = new LSC_Template_Loader();

            ob_start();
            $tmplLoader->get_template_part($atts['slug'], null, $atts);
            return ob_get_clean();
        }
    }
}
