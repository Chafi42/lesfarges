<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('LSC_Settings')) {
    /**
     * LSC_Settings
     *
     *  Settings class
     *
     * @since      1.0.0
     * @package    lfi-simple-carousel
     * @subpackage lfi-simple-carousel/admin
     *
     */
    class LSC_Settings
    {
        /**
         * @var LSC_Settings
         * @access private
         * @static
         */
        private static $_instance = null;

        /**
         * @var LSC_Helper $helper
         */
        protected $helper;

        /**
         * Hook loader
         *
         * @var LSC_Loader $loader
         */
        protected $loader;

        /**
         * @var array $settings 
         */
        var $settings;

        /**
         * @var array $subSettings 
         */
        var $subSettings;

        /**
         * @var array $menus Tableau des menu à ajouter 
         */
        var $menus = array();

        /**
         * Méthode qui crée l'unique instance de la classe
         * si elle n'existe pas encore puis la retourne.
         *
         * @return LSC_Settings
         */
        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new LSC_Settings();
            }
            return self::$_instance;
        }

        /**
         * Constructor privé pour ne pas se lancer
         * si n'est pas instancié avec ::getInstance
         *
         */
        private function __construct()
        {
            $this->helper = LSC_Helper::getInstance();
            $this->load_dependencies();
        }

        /**
         * Charge les dépendances pour LSC_Settings
         *
         * @since 1.0.0
         * @access private
         **/
        private function load_dependencies()
        {
            $adminPath = 'admin/settings/';
            $dependencies = array(
                'abs' => array(
                    $adminPath . 'class-settings-api.php',
                    $adminPath . 'class-settings-api-menu.php',
                    $adminPath . 'class-settings-api-menu-tab.php',
                    $adminPath . 'class-settings-api-sub-menu.php',
                    $adminPath . 'class-settings-api-sub-menu-cpt.php',
                ),
            );

            $this->helper->require($dependencies);
        }

        /**
         * Ajoute les pages de menu en admin
         *
         * @since  1.0.0
         * @param  array $settings
         * @return false|array {
         *      Array of Menu and Sub Menu Classes
         *          @type LSC_Settings_API_Menu 'menu'
         *          @type false|LSC_Settings_API_Sub_Menu 'sub-menu' 
         * }
         **/
        public function add_menu($setting = array())
        {
            /**
             * Add menu to admin page
             *
             * @since 1.0.0
             *
             * @param array $settings An associative array of settings.
             *        'menu-key' => array(
             *            'slug'     => 'menu-slug',
             *            'title'    => 'Menu title',
             *            'desc'     => 'Description menu',
             *            'icon'     => 'dashicons-admin-settings',
             *            'position' => 99,
             *            'fields'   => array(
             *                array(
             *                ),
             *                ...
             *            ),
             *            'tabs'     => array(
             *                array(
             *                    'slug'   => 'tab-slug',
             *                    'title'  => 'Tab title',
             *                    'fields' => array(
             *                        array(
             *                        ),
             *                        ...
             *                    )
             *                ),
             *                ...
             *            ),
             *            'sub-menus' => array(
             *                array(
             *                    'slug'   => 'test-sub',
             *                    'title'  => 'Test sub-menu',
             *                    'desc'   => 'Description sub-menu',
             *                    'fields' => array(
             *                        array(
             *                        ),
             *                        ...
             *                    ),
             *                    'tabs'   => array(
             *                        array(
             *                            'slug'   => 'sub-menu-tab',
             *                            'title'  => 'Sub menu tab',
             *                            'fields' => array(
             *                                array(
             *                                ),
             *                                ...
             *                            )
             *                        ),
             *                        ...
             *                    )
             *                ),
             *            )
             *        )
             */


            if (!isset($setting['menu_slug'])) return false;

            // Create parent menu
            $menu = $this->create_setting($setting);

            // Add fields to "general" tab
            if (isset($setting['fields'])) {
                $this->create_setting_fields($menu, $setting['fields']);
            }

            // Create additional tabs
            if (isset($setting['tabs'])) {
                foreach ($setting['tabs'] as $options) {
                    $menuTab = $this->create_setting_tab($menu, $options);
                    // Create fields for this tab
                    if (isset($options['fields'])) {
                        $this->create_setting_fields($menuTab, $options['fields']);
                    }
                }
            }

            // Create sub-menu for this parent menu
            $subMenu = false;
            if (isset($setting['sub-menus'])) {
                foreach ($setting['sub-menus'] as $options) {
                    $subMenu = $this->create_setting_sub_menu($menu, $options);
                    // Add fields to "general" tab
                    if (isset($options['fields'])) {
                        $this->create_setting_fields($subMenu, $options['fields']);
                    }
                    // Create additional tabs
                    if (isset($options['tabs'])) {
                        foreach ($options['tabs'] as $tabOptions) {
                            $subMenuTab = $this->create_setting_tab($subMenu, $tabOptions);
                            // Create fields for this tab
                            if (isset($tabOptions['fields'])) {
                                $this->create_setting_fields($subMenuTab, $tabOptions['fields']);
                            }
                        }
                    }
                }
            }

            return array(
                $menu,
                $subMenu
            );
        }

        /**
         * Add sub menu page to existing menu
         *
         * @since 1.0.0
         * @param array
         * @return LSC_Settings_API_Sub_Menu
         * 
         **/
        public function add_sub_menu($sub_settings = array())
        {
            /**
             * Add Sub-menu to existing admin menu
             *
             * @since 1.0.0
             *
             * @param array $subSettings Associative array of sub menus.
             *            'sub-menu-key' => array(
             *                'parent_slug' => 'themes.php',
             *                'menu_slug'   => 'sub-menu-slug',
             *                'menu_title'  => 'Sub Menu Title',
             *                'desc'        => 'Sub Menu Description',
             *                'fields'      => array(
             *                    array(
             *                    ),
             *                    ...
             *                ),
             *                'tabs'        => array(
             *                    array(
             *                        'slug'   => 'sub-menu-tab',
             *                        'title'  => 'Sub menu tab',
             *                        'fields' => array(
             *                            array(
             *                            ),
             *                            ...
             *                        )
             *                    ),
             *                    ...
             *                ),
             *            ),
             */


            if (!isset($sub_settings['parent_slug']) || !isset($sub_settings['menu_slug'])) return false;
            $sub = $this->create_setting_sub_menu($sub_settings['parent_slug'], $sub_settings);
            // Add fields to "general" tab
            if (isset($sub_settings['fields'])) {
                $this->create_setting_fields($sub, $sub_settings['fields']);
            }
            // Create additional tabs
            if (isset($sub_settings['tabs'])) {
                foreach ($sub_settings['tabs'] as $tabOptions) {
                    $tab = $this->create_setting_tab($sub, $tabOptions);
                    // Create fields for this tab
                    if (isset($tabOptions['fields'])) {
                        $this->create_setting_fields($tab, $tabOptions['fields']);
                    }
                }
            }

            return $sub;
        }

        /**
         * Settings page
         *
         * @param array $options Options to create menu
         * @return false|LSC_Settings_API
         **/
        private function create_setting($options = array())
        {
            if (empty($options['menu_slug'])) return false;
            return new LSC_Settings_API_Menu($options);
        }

        /**
         * Settings fields
         *
         * @param LSC_Settings_API $menu
         * @param array $fields
         * @return false|LSC_Settings_API
         **/
        private function create_setting_fields($menu = '', $fields = array())
        {
            if (
                !$menu instanceof LSC_Settings_API
                && !$menu instanceof LSC_Settings_API_Menu_Tab
            ) return false;

            foreach ($fields as $field) {
                $menu->add_field($field);
            }
            return $menu;
        }

        /**
         * Settings tabs
         *
         * @param LSC_Settings_API $parent
         * @param array $options
         * @return false|LSC_Settings_API
         **/
        private function create_setting_tab($parent = '', $options = array())
        {
            if (!$parent instanceof LSC_Settings_API) {
                return false;
            }
            return new LSC_Settings_API_Menu_Tab($options, $parent);
        }

        /**
         * Settings sub menu
         *
         * @param string|LSC_Settings_API $parent Parent slug pour les menus existant/built'in
         * @param array $options
         * @return LSC_Settings_API_Sub_Menu
         **/
        private function create_setting_sub_menu($parent = '', $options = array())
        {
            $default = new LSC_Settings_API_Menu();
            $options = wp_parse_args($options, $default);
            return new LSC_Settings_API_Sub_Menu($options, $parent);
        }
    }
}
