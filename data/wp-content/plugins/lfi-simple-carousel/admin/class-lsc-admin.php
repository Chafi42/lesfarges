<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('LSC_Admin')) {

	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * Defines the plugin name, version, and two examples hooks for how to
	 * enqueue the admin-specific stylesheet and JavaScript.
	 *
	 * @since 	   1.0.0
	 * @package    lfi-simple-carousel
	 * @subpackage lfi-simple-carousel/admin
	 * @author     LFI <contact@lafabriqueinfo.fr>
	 * 
	 */
	class LSC_Admin {

		/**
		 * Helper class
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      LSC_Helper    $helper    Helper class
		 */
		private $helper;

		/**
         * @var LSC_Loader $loader  
         */
        protected $loader;

		/**
		 * @since  1.0.0
		 * @access private
		 * @static
		 * @var    LSC_Admin
		 */
		private static $_instance = null;

		/**
		 * Méthode qui crée l'unique instance de la classe
		 * si elle n'existe pas encore puis la retourne.
		 *
		 * 
		 * @return LSC_Admin
		 */
		public static function getInstance()
		{
			if (is_null(self::$_instance)) {
				self::$_instance = new LSC_Admin();
			}
			return self::$_instance;
		}

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since      1.0.0
		 * @package    lfi-simple-carousel
		 * @subpackage lfi-simple-carousel/admin
		 */
		public function __construct()
		{
			$this->helper = LSC_Helper::getInstance();
			$this->loader = new LSC_Loader();
			$this->required_files();

			// Add CPT
			LSC_CPT::getInstance();

			// Add meta-boxes to CPT
			$this->add_custom_fields();

			$this->add_hooks();
			$this->run_hooks();
		}

		/**
		 * Get dependencies
		 *
		 **/
		public function required_files()
		{
			$adminPath = 'admin/';
			$required_files = array(
				'abs' => array(
					$adminPath . 'class-lsc-settings.php',
					$adminPath . 'class-lsc-cpt.php',
					$adminPath . 'class-lsc-meta-boxes.php',
					$adminPath . 'dependency/class-dependency-api.php',
					$adminPath . 'dependency/class-dependency-api-skin.php',
					$adminPath . 'dependency/class-dismiss-notice-api.php',
				),
			);

			$this->helper->require($required_files);
		}

		/**
		 * Admin hooks
		 *
		 * @return void
		 **/
		public function add_hooks()
		{
			// Include plugins dependencies
			$this->loader->add_action(
				'init',
				$this,
				'cmb2_dependencies'
			);
		}

		/**
		 * Register the stylesheets for the admin area.
         * 
         * @param string $hook_suffix The current admin page.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles($hook_suffix)
		{
			global $post_type;
			$screen = get_current_screen();
			if(empty($post_type)) $post_type = $screen->post_type;
            // LSC_CPT_NAME Admin post-type
            if($post_type == LSC_CPT_NAME){
				wp_enqueue_style(
                    'bootstrap-css',
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
                    array(),
                    '5.3.2',
                );
				wp_enqueue_style(
					'admin-slider',
					LSC_Helper()->get_uri_path('admin/css/lsc-admin.css'),
				);
				wp_enqueue_script(
                    'bootstrap-js',
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js',
                    array('jquery'),
                    '5.3.2',
                    true
                );
            }
		}

		/**
		 * Register the JavaScript for the admin area.
         * 
         * @param string $hook_suffix The current admin page.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts($hook_suffix)
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

			wp_enqueue_script(
				$this->helper->get_plugin_name() . '-js',
				$this->helper->get_uri_path('admin/js/lsc-admin.js'),
				array('jquery'),
				$this->helper->get_plugin_ver(),
				true
			);
		}

		/**
		 * Plugins dependencies
		 *
		 * Auto Upload/Install other plugins required for this plugin
		 * 
		 * @since      1.0.0
         * @package    lfi-simple-carousel
         * @subpackage lfi-simple-carousel/admin
		 *
		 * @return void
		 **/
		public function cmb2_dependencies()
		{
			// CMB2
			$cmb2 = array(
                array(
                    'name' => 'CMB2',
                    'host' => 'github',
                    'slug' => 'cmb2/init.php',
                    'uri' => 'cmb2/cmb2',
                    'branch' => 'master',
                    'required' => true,
                ),
                array(
                    'name' => 'CMB2 Google Map',
                    'host' => 'github',
                    'slug' => 'cmb2_field_map/cmb-field-map.php',
                    'uri' => 'mustardBees/cmb_field_map',
                    'branch' => 'master',
                    'required' => true,
                )
            );

			// Dependency_API::getInstance('LFI Simple Carousel')->register($cmb2)->run();
		}

        /**
         * Add custom fields using MOZ_Custom_Fields Class
         *
         * @return void
         **/
        public function add_custom_fields()
        {
            LSC_Custom_Fields::getInstance();
        }

		/**
         * Run the loader to execute all of the hooks with WordPress.
         *
         * @since      1.0.0
         * @package    lfi-simple-carousel
         * @subpackage lfi-simple-carousel/admin
         * 
		 * @return void
         **/
        public function run_hooks()
        {
            $this->loader->run();
        }

	}
}