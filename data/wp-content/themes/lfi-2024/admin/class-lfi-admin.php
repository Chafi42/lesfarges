<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('LFI_Admin')) {

    /**
     * The admin-specific functionality of the plugin.
     *
     * Defines the plugin name, version, and two examples hooks for how to
     * enqueue the admin-specific stylesheet and JavaScript.
     *
     * @since 	   1.0.0
     * @package    lfi-2024
     * @subpackage lfi-2024/admin
     * @author     LFI <contact@lafabriqueinfo.fr>
     * 
     */
    class LFI_Admin
    {

        /**
         * Helper class
         *
         * @since    1.0.0
         * @access   private
         * @var      LFI_Helper    $helper    Helper class
         */
        private $helper;

        /**
         * @var LFI_Loader $loader  
         */
        protected $loader;

        /**
         * @since  1.0.0
         * @access private
         * @static
         * @var    LFI_Admin
         */
        private static $_instance = null;

        /**
         * Méthode qui crée l'unique instance de la classe
         * si elle n'existe pas encore puis la retourne.
         *
         * 
         * @return LFI_Admin
         */
        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new LFI_Admin();
            }
            return self::$_instance;
        }

        /**
         * Initialize the class and set its properties.
         *
         * @since      1.0.0
         * @package    lfi-2024
         * @subpackage lfi-2024/admin
         */
        public function __construct()
        {
            $this->loader = new LFI_Loader();
            $this->required_files();
            $this->add_hooks();
            $this->run_hooks();

            // Run the LFI CMB2 field type class
            LFI_CMB2_Field_Type::getInstance();

            // Run the nav item custom fields class
            LFI_Nav_Item_Custom_Fields::getInstance();
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
                    $adminPath . 'class-lfi-settings.php',
                    $adminPath . 'class-lfi-cpt.php',
                    $adminPath . 'dependency/class-dependency-api.php',
                    $adminPath . 'dependency/class-dependency-api-skin.php',
                    $adminPath . 'dependency/class-dismiss-notice-api.php',
                    $adminPath . 'class-lfi-cmb2-field-type.php',
                    $adminPath . 'class-lfi-nav-item-custom-fields.php',
                ),
            );

            LFI_Helper()->require($required_files);
            LFI_Settings::getInstance();
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
            // Gutenberg editor hook for styles and scripts
            $this->loader->add_action(
                'enqueue_block_editor_assets',
                $this,
                'gutenberg_editor_assets',
                999
            );
            // Mime Type
            $this->loader->add_filter(
                'upload_mimes',
                $this,
                'upload_mimes',
                10,
                2
            );
            // Select page as "Footer"
            $this->loader->add_action(
                'admin_init',
                $this,
                'footer_select_setting'
            );
            // Select page as "404"
            $this->loader->add_action(
                'admin_init',
                $this,
                'page_404_select_setting'
            );
            $this->loader->add_filter(
                'display_post_states',
                $this,
                'special_page_display_post_states',
                10,
                2
            );
            // Gutenberg Block Style
            $this->loader->add_action(
                'init',
                $this,
                'gutenberg_block_style'
            );
        }

        /**
         * Ajoute des format autorisé pour le téléversement
         *
         * @param array $t
         * @param int|Wp_User|null $user
         * @return array $t
         **/
        public static function upload_mimes($t, $user)
        {
            $t['svg'] = 'image/svg+xml';
            // $t['svgz'] = 'image/svg+xml';
            // $t['doc'] = 'application/msword';

            // Optional. Remove a mime type.
            // unset($t['exe']);

            return $t;
        }

        /**
         * Register the stylesheets for the admin area.
         *
         * @since    1.0.0
         */
        public function enqueue_styles()
        {

            /**
             * This function is provided for demonstration purposes only.
             *
             * An instance of this class should be passed to the run() function
             * defined in LFI_Admin as all of the hooks are defined
             * in that particular class.
             *
             * The LFI_Admin will then create the relationship
             * between the defined hooks and the functions defined in this
             * class.
             */

            wp_enqueue_style(
                LFI_Helper()->name() . '-admin-css',
                LFI_Helper()->get_uri_path('admin/css/lfi-2024-admin.css'),
                array(),
                LFI_Helper()->version(),
                'all'
            );
        }

        /**
         * Enqueue Styles and Scripts ofr the gutenberg editor
         *
         * @return void
         **/
        public function gutenberg_editor_assets()
        {
            $handle = LFI_Helper()->name() . '-gutenberg-css';
            wp_enqueue_style(
                $handle,
                LFI_Helper()->get_uri_path('admin/css/gutenberg-editor.css'),
                array(),
                LFI_Helper()->version(),
                'all'
            );

            // Color
            wp_add_inline_style(
                $handle,
                LFI_Helper()->color_style()
            );
            // Font
            wp_add_inline_style(
                $handle,
                LFI_Helper()->font_style(".editor-styles-wrapper", true)
            );
            // Heading
            wp_add_inline_style(
                $handle,
                LFI_Helper()->heading_style(".edit-post-visual-editor", true)
            );

            wp_enqueue_script(
                $handle . '-gutenberg-js',
                LFI_Helper()->get_uri_path('admin/js/gutenberg-editor.js'),
                array('jquery'),
                LFI_Helper()->version(),
                true
            );
        }

        /**
         * Register the JavaScript for the admin area.
         *
         * @since    1.0.0
         */
        public function enqueue_scripts()
        {

            /**
             * This function is provided for demonstration purposes only.
             *
             * An instance of this class should be passed to the run() function
             * defined in LFI_Admin as all of the hooks are defined
             * in that particular class.
             *
             * The LFI_Admin will then create the relationship
             * between the defined hooks and the functions defined in this
             * class.
             */

            wp_enqueue_script(
                LFI_Helper()->name() . '-js',
                LFI_Helper()->get_uri_path('admin/js/lfi-2024-admin.js'),
                array('jquery'),
                LFI_Helper()->version(),
                false
            );

            wp_enqueue_script(
                'cmb2-extras',
                LFI_Helper()->get_uri_path('admin/js/cmb2-extras.js'),
                array('jquery'),
                LFI_Helper()->version(),
                false
            );
        }

        /**
         * Plugins dependencies
         *
         * Auto Upload/Install other plugins required for this plugin
         * 
         * @since      1.0.0
         * @package    lfi-2024
         * @subpackage lfi-2024/admin
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
                // array(
                // 	'name' => 'CMB2 Attached Posts',
                // 	'host' => 'github',
                // 	'slug' => 'cmb2-attached-posts/cmb2-attached-posts-field.php',
                // 	'uri' => 'cmb2/cmb2-attached-posts',
                // 	'branch' => 'master',
                // 	'required' => true,
                // ),
                // array(
                // 	'name' => 'CMB2 Google Map',
                // 	'host' => 'github',
                // 	'slug' => 'cmb2_field_map/cmb-field-map.php',
                // 	'uri' => 'mustardBees/cmb_field_map',
                // 	'branch' => 'master',
                // 	'required' => true,
                // )
            );

            // Dependency_API::getInstance(__DIR__)->register($cmb2)->run();
        }

        /**
         * Enregistre des menus
         *
         * @since      1.0.0
         * @package    lfi-2024
         * @subpackage lfi-2024/admin
         * 
         * @return void
         **/
        public function register_nav_menus()
        {
            // Minimum nav-menu
            register_nav_menus(
                array(
                    'primary-menu' => esc_html__('Primary', LFI_Helper()->domain()),
                )
            );
        }

        /**
         * Ajouter des supports pour le theme
         *
         * @since   1.0.0
         * @package lfi-2024
         * @subpackage lfi-2024/admin
         * 
         * @return void
         **/
        public function add_theme_support()
        {
            // Theme support
            $supports = array(
                'custom-logo',
                'custom-header',
                'post-thumbnails',
                'responsive-embeds',
                'woocommerce',
                'align-wide',
                'editor-color-palette' => LFI_Helper()->theme_colors(false)
            );
            foreach ($supports as $key => $support) {
                if (!is_int($key)) {
                    add_theme_support($key, $support);
                } else {
                    add_theme_support($support);
                }
            }
        }

        /**
         * Ajoute une selection de page footer dans les réglages généraux
         *
         * @since   1.0.0
         * @package lfi-2024
         * @subpackage lfi-2024/admin
         * 
         * @return void
         **/
        public function footer_select_setting()
        {
            // register a new setting for "reading" page
            register_setting('reading', 'footer_page');

            // register a new section in the "reading" page
            add_settings_section(
                'footer_select_section',
                '',
                '',
                'reading'
            );

            // register a new field in the "footer_select_section" section, inside the "reading" page
            add_settings_field(
                'footer_select_field',
                'Pied de page',
                array($this, 'footer_select_field_callback'),
                'reading',
                'footer_select_section'
            );
        }

        /**
         * Ajoute une selection de page 404 dans les réglages généraux
         *
         * @since   1.0.0
         * @package lfi-2024
         * @subpackage lfi-2024/admin
         * 
         * @return void
         **/
        public function page_404_select_setting()
        {
            // register a new setting for "reading" page
            register_setting('reading', 'page_404');

            // register a new section in the "reading" page
            add_settings_section(
                'page_404_select_section',
                '',
                '',
                'reading'
            );

            // register a new field in the "404_select_section" section, inside the "reading" page
            add_settings_field(
                'page_404_select_field',
                'Page 404',
                array($this, 'page_404_select_field_callback'),
                'reading',
                'page_404_select_section'
            );
        }

        /**
         * Fonction de rendu du paramètre
         *
         * @since   1.0.0
         * @package lfi-2024
         * @subpackage lfi-2024/admin
         * 
         * @return void
         **/
        public function footer_select_field_callback()
        {
            // output the field
            print(wp_dropdown_pages(
                array(
                    'name'              => 'footer_page',
                    'echo'              => 0,
                    'show_option_none'  => __('&mdash; Select &mdash;'),
                    'option_none_value' => '0',
                    'selected'          => get_option('footer_page'),
                )
            )
            );
        }

        /**
         * Fonction de rendu du paramètre
         *
         * @since   1.0.0
         * @package lfi-2024
         * @subpackage lfi-2024/admin
         * 
         * @return void
         **/
        public function page_404_select_field_callback()
        {
            // output the field
            print(wp_dropdown_pages(
                array(
                    'name'              => 'page_404',
                    'echo'              => 0,
                    'show_option_none'  => __('&mdash; Select &mdash;'),
                    'option_none_value' => '0',
                    'selected'          => get_option('page_404'),
                )
            )
            );
        }

        /**
         * Display post state for footer
         *
         * @param array $post_states An array of post display states.
         * @param WP_Post $post The current post object.
         * @return array
         **/
        public function special_page_display_post_states($post_states, $post)
        {
            if ($post->ID == get_option('footer_page')) {
                return array('Footer Page');
            }
            // if ($post->ID == get_option('project_cat_page')) {
            //     return array('Page Catégorie Projet');
            // }
            // if ($post->ID == get_option('search_page')) {
            //     return array('Page Recherche');
            // }
            return $post_states;
        }

        /**
         * Add some style to gutenberg blocks
         *
         * @since 1.0.0
         * 
         * @param type 
         * 
         * @return 
         * @throws conditon
         **/
        public function gutenberg_block_style()
        {
            $styles = apply_filters('gutenberg_block_style', array());
            if (function_exists('register_block_style') && is_array($styles) && !empty($styles)) {
                foreach ($styles as $block_name => $style_properties) {
                    register_block_style(
                        $block_name,
                        $style_properties
                    );
                }
            }
        }

        /**
         * Run the loader to execute all of the hooks with WordPress.
         *
         * @since      1.0.0
         * @package    lfi-2024
         * @subpackage lfi-2024/admin
         * 
         * @return void
         **/
        public function run_hooks()
        {
            $this->loader->run();
        }
    }
}
