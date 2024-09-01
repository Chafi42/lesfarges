<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('LFG_2024')) {

    /**
     * LFG_2024
     *
     *  Description
     *
     * @since   1.0.0
     * @package lesfarges-2024
     * @subpackage includes/lesfarges-2024
     *
     */
    class LFG_2024
    {
        /**
         * @var LFG_2024
         * @access private
         * @static
         */
        private static $_instance = null;

        /**
         * @var LFI_Loader $loader  
         */
        protected $loader;

        /**
         * Méthode qui crée l'unique instance de la classe
         * si elle n'existe pas encore puis la retourne.
         *
         * 
         * @return LFG_2024
         */
        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new LFG_2024();
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
            // Instanciate Loader Class
            $this->loader = new LFI_Loader();

            // include require files
            $this->require_files();

            // Registers action hooks
            $this->register_actions();

            // Registers filter hooks
            $this->register_filters();

            // Use Loader to actually run the actions and filters
            $this->loader->run();
        }

        /**
         * Require files
         *
         **/
        private function require_files()
        {
            // Require files
            LFI_Helper()->require(
                array(
                    'abs' => array(
                        'includes/class-custom-post-types.php',
                        'includes/class-taxonomies.php',
                        'includes/class-custom-fields.php',
                    )
                ),
                true
            );

            // Initiate classes from the required files.
            $this->initiate_theme_classes();
        }

        /**
         * Instanciate theme classes
         *
         * @return void
         * @throws conditon
         **/
        private function initiate_theme_classes()
        {
            // 'includes/class-custom-post-types.php'
            LFG_2024_CPT::getInstance();
            // 'includes/class-taxonomies.php',
            LFG_2024_Tax::getInstance();
            // 'includes/class-custom-fields.php',
            LFG_2024_CFs::getInstance();
        }

        /**
         * Register action hooks with LFI_Loader class
         *
         **/
        private function register_actions()
        {
            $actions = array(
                array(
                    'wp_enqueue_scripts',
                    $this,
                    'enqueue_scripts',
                    10
                ),
                array(
                    'after_setup_theme',
                    $this,
                    'register_nav_menu'
                ),
                array(
                    'init',
                    $this,
                    'lesfarges_block_styles'
                ),
                array(
                    'admin_init',
                    $this,
                    'lfa_settings'
                ),
                array(
                    'enqueue_block_editor_assets',
                    $this,
                    'lfa_block_editor_assets'
                ),
                array(
                    'admin_enqueue_scripts',
                    $this,
                    'lfa_admin_enqueue_scripts',
                ),
                array(
                    'wp-blocks',
                    'wp-dom-ready',
                    'wp-edit-post',
                    'lfa_block_editor_assets',
                ),
            );

            foreach ($actions as $key => $action) {
                if (empty($action[0]) || !isset($action[0])) continue;
                $hook          = $action[0];
                $component     = $action[1];
                $callback      = $action[2];
                $priority      = isset($action[3]) ? $action[3] : 10;
                $accepted_args = isset($action[4]) ? $action[4] : 1;
                $this->loader->add_action(
                    $hook,
                    $component,
                    $callback,
                    $priority,
                    $accepted_args,
                );
            }
        }

        /**
         * Register filter hooks with LFI_Loader class
         *
         **/
        private function register_filters()
        {
            $filters = array(
                array()
            );

            foreach ($filters as $key => $filter) {
                if (empty($filter[0]) || !isset($filter[0])) continue;
                $hook          = $filter[0];
                $component     = $filter[1];
                $callback      = $filter[2];
                $priority      = isset($filter[3]) ? $filter[3] : 10;
                $accepted_args = isset($filter[4]) ? $filter[4] : 1;
                $this->loader->add_filter(
                    $hook,
                    $component,
                    $callback,
                    $priority,
                    $accepted_args,
                );
            }
        }

        /**
         * Add script to admin area
         */
        public function lfa_admin_enqueue_scripts($hook)
        {
           
            if ('options-general.php' === $hook) {
                wp_enqueue_script(
                    'my_custom_script',
                    CHILD_ASSETS . 'js/options-general.js',
                    array('jquery'),
                    '1.0',
                    true
                );
            }
            
        }

        /**
         * Add scripts and styles to public area
         *
         * @param string $hook_suffix The current admin page.
         * 
         * @return void
         **/
        public function enqueue_scripts($hook_suffix)
        {
            wp_enqueue_style(
                'child-theme-style',
                LFI_Helper()->get_uri_path('style.css', true),
            );

            wp_enqueue_script(
                'lesfarges-2024-js',
                LFI_Helper()->get_uri_path('assets/js/lesfarges-2024.js', true),
                array('jquery'),
                '1.0.0',
                true
            );
        }

        /**
         * Add new location for menu
         *
         * @return void
         **/
        public function register_nav_menu()
        {
            register_nav_menus(array(
                'secondary' => 'Mentions légales',
            ));
        }

        /**
         * Register Gutenberg custom block styles
         *
         * @return void
         **/
        public function lesfarges_block_styles()
        {
            $styles = array(
                'core/cover' => array(
                    'name'  => 'contain',
                    'label' => 'Contenir'
                ),
                'core/cover' => array(
                    'name'  => 'cover',
                    'label' => 'Couvrir'
                ),
            );

            foreach ($styles as $block => $style) {
                register_block_style(
                    $block,
                    $style
                );
            }
        }



        /**
         * Register settings fields & settings
         * @return void
         **/

        public function  lfa_settings()
        {
            add_settings_field(
                'lfa_slogan_2',
                'Slogan 2ème ligne.',
                array($this, 'lfa_settings_callback'),
                'general',
                'default'
            );

            register_setting('general', 'lfa_slogan_2');
        }

        /**
         * Settings callback
         *
         * @return string
         **/
        public function lfa_settings_callback()
        {
            $value = get_option('lfa_slogan_2');
            echo '<input name="lfa_slogan_2" type="text" id="lfa_slogan_2" aria-describedby="tagline-description" value="' . $value . '" class="regular-text">';
        }

        /**
         * Register settings fields & settings
         *
         * @return void
         **/
        public function  lfa_block_editor_assets()
        {
            wp_enqueue_script(
                'lfa-block-editor',
                CHILD_ASSETS . 'js/lfa-block-editor.js',
            );
        }
    }
}
