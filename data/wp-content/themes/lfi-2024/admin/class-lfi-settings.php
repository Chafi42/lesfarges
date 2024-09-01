<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('LFI_Settings')) {
    /**
     * LFI_Settings
     *
     *  Settings class
     *
     * @since      1.0.0
     * @package    lfi-2024
     * @subpackage lfi-2024/admin
     *
     */
    class LFI_Settings
    {
        /**
         * @var LFI_Settings
         * @access private
         * @static
         */
        private static $_instance = null;

        /**
         * @var LFI_Helper $helper
         */
        protected $helper;

        /**
         * Hook loader
         *
         * @var LFI_Loader $loader
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
         * @return LFI_Settings
         */
        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new LFI_Settings();
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
            $this->helper = LFI_Helper();
            $this->load_dependencies();
            // Add defaults menus
            $this->add_defaults_settings();
        }

        /**
         * Charge les dépendances pour LFI_Settings
         *
         * @since 1.0.0
         * @access private
         **/
        private function load_dependencies()
        {
            $settingsPath = 'admin/settings/';
            $dependencies = array(
                'abs' => array(
                    $settingsPath . 'class-settings-api.php',
                    $settingsPath . 'class-settings-api-menu.php',
                    $settingsPath . 'class-settings-api-menu-tab.php',
                    $settingsPath . 'class-settings-api-sub-menu.php',
                    $settingsPath . 'class-settings-api-sub-menu-cpt.php',
                ),
            );

            $this->helper->require($dependencies);
        }

        /**
         * Just a wrapper for the Helper class, single_db_name method.
         *
         * @param string $tab Tab on which the setting is.
         * @param string $setting The setting name to retreive.
         * @return false|string
         **/
        public function single_db_name($tab = 'general', $setting = '')
        {
            return $this->helper->single_db_name($tab, $setting);
        }

        /**
         * Add defaults menus
         *
         **/
        public function add_defaults_settings()
        {
            $colorSwatches = $this->helper->theme_colors('hex');
            $settingsMenu = array(
                'parent_slug' => 'themes.php',
                'menu_slug' => 'theme-settings',
                'menu_title' => __('Theme Settings', $this->helper->domain()),
                'desc' => __('Settings for LFI themes', $this->helper->domain()),
                'icon' => 'dashicons-admin-settings',
                'position' => 99,
                'fields' => array(
                    array(
                        'name'      => 'theme-colors',
                        'title'     => __('Couleur du thème'),
                        'type'      => 'color',
                        'default'   => array('#fff' => 'blanc'),
                        'multi'     => true,
                        'input'     => true,
                        // 'db_format' => '%2$s',
                        'option_name' => $this->single_db_name('general', 'colors'),
                        'swatches' => $colorSwatches,
                    ),
                    array(
                        'name'        => 'header-site-title',
                        'title'       => 'Afficher Titre Site',
                        'type'        => 'checkbox',
                        'default'     => true,
                        'option_name' => $this->single_db_name('general', 'site-title'),
                    ),
                    array(
                        'name'        => 'header-tagline',
                        'title'       => 'Afficher Slogan',
                        'type'        => 'checkbox',
                        'default'     => true,
                        'option_name' => $this->single_db_name('general', 'tagline'),
                    ),
                    array(
                        'name'        => 'header-logo',
                        'title'       => 'Afficher logo',
                        'type'        => 'checkbox',
                        'default'     => true,
                        'option_name' => $this->single_db_name('general', 'logo'),
                    )
                ),
                'tabs' => array(
                    // LOADER
                    array(
                        'slug' => 'loader',
                        'title' => 'Loader',
                        'fields' => array(
                            array(
                                'name' => 'loader-color',
                                'title' => 'Couleurs',
                                'type' => 'group',
                                'fields' => array(
                                    array(
                                        'name' => 'bg-color',
                                        'title' => 'Couleur arrière plan',
                                        'type' => 'color',
                                        'default' => array('#fff' => 'blanc'),
                                        'multi' => false,
                                        'input' => false,
                                        'db_format' => 'theme-loader-%2$s',
                                        'option_name' => $this->single_db_name('loader', 'background'),
                                        'swatches' => $colorSwatches,
                                    ),
                                    array(
                                        'name' => 'border-color',
                                        'title' => 'Couleur trait cercle',
                                        'type' => 'color',
                                        'default' => array('#fff' => 'blanc'),
                                        'multi' => false,
                                        'input' => false,
                                        'db_format' => 'theme-loader-%2$s',
                                        'option_name' => $this->single_db_name('loader', 'border'),
                                        'swatches' => $colorSwatches,
                                    ),
                                    array(
                                        'name' => 'border-top-color',
                                        'title' => 'Couleur trait mobile',
                                        'type' => 'color',
                                        'default' => array('#fff' => 'blanc'),
                                        'multi' => false,
                                        'input' => false,
                                        'db_format' => 'theme-loader-%2$s',
                                        'option_name' => $this->single_db_name('loader', 'border-top'),
                                        'swatches' => $colorSwatches,
                                    ),
                                )
                            ),
                            array(
                                'name' => 'loader-size',
                                'title' => 'Paramètres taille',
                                'type' => 'group',
                                'fields' => array(
                                    array(
                                        'name' => 'width',
                                        'title' => 'Largeur',
                                        'type' => 'number',
                                        'default' => 60,
                                        'min' => 1,
                                        'max' => 100,
                                        'db_format' => 'theme-loader-%2$s',
                                        'option_name' => $this->single_db_name('loader', 'width')
                                    ),
                                    array(
                                        'name' => 'height',
                                        'title' => 'Hauteur',
                                        'type' => 'number',
                                        'default' => 60,
                                        'min' => 1,
                                        'max' => 100,
                                        'db_format' => 'theme-loader-%2$s',
                                        'option_name' => $this->single_db_name('loader', 'height')
                                    ),
                                    array(
                                        'name' => 'border-width',
                                        'title' => 'Largeur trait',
                                        'type' => 'number',
                                        'default' => 5,
                                        'min' => 1,
                                        'max' => 100,
                                        'db_format' => 'theme-loader-%2$s',
                                        'option_name' => $this->single_db_name('loader', 'border-width')
                                    ),
                                )
                            ),
                        )
                    ),
                    // Fonts
                    array(
                        'slug'   => 'font',
                        'title'  => 'Fonts',
                        'fields' => array(
                            array(
                                'name'   => 'font-class',
                                'title'  => 'Classes',
                                'type'   => 'group',
                                'fields' => array(
                                    // S
                                    array(
                                        'name' => 'small',
                                        'title' => 'Small (rem)',
                                        'type' => 'number',
                                        'default' => 0.8,
                                        'min' => 0,
                                        'max' => 100,
                                        'step' => 0.1,
                                        // 'db_format' => 'theme-loader-%2$s',
                                        'option_name' => $this->single_db_name('font', 'small')
                                    ),
                                    // M
                                    array(
                                        'name' => 'medium',
                                        'title' => 'Medium (rem)',
                                        'type' => 'number',
                                        'default' => 1.3,
                                        'min' => 0,
                                        'max' => 100,
                                        'step' => 0.1,
                                        // 'db_format' => 'theme-loader-%2$s',
                                        'option_name' => $this->single_db_name('font', 'medium')
                                    ),
                                    // L
                                    array(
                                        'name' => 'large',
                                        'title' => 'Large (rem)',
                                        'type' => 'number',
                                        'default' => 2,
                                        'min' => 0,
                                        'max' => 100,
                                        'step' => 0.1,
                                        // 'db_format' => 'theme-loader-%2$s',
                                        'option_name' => $this->single_db_name('font', 'large')
                                    ),
                                    // XL
                                    array(
                                        'name' => 'x-large',
                                        'title' => 'XL (rem)',
                                        'type' => 'number',
                                        'default' => 4,
                                        'min' => 0,
                                        'max' => 100,
                                        'step' => 0.1,
                                        // 'db_format' => 'theme-loader-%2$s',
                                        'option_name' => $this->single_db_name('font', 'x-large')
                                    ),
                                )
                            ),
                            array(
                                'name'   => 'font-heading',
                                'title'  => 'Headings',
                                'type'   => 'group',
                                'fields' => array(
                                    // h1
                                    array(
                                        'name'        => 'h1',
                                        'title'       => 'Titre H1 (rem)',
                                        'type'        => 'number',
                                        'default'     => 2.5,
                                        'min'         => 0,
                                        'max'         => 100,
                                        'step'        => 0.01,
                                        'option_name' => $this->single_db_name('heading', 'h1')
                                    ),
                                    // h2
                                    array(
                                        'name'        => 'h2',
                                        'title'       => 'Titre H2 (rem)',
                                        'type'        => 'number',
                                        'default'     => 2,
                                        'min'         => 0,
                                        'max'         => 100,
                                        'step'        => 0.01,
                                        'option_name' => $this->single_db_name('heading', 'h2')
                                    ),
                                    // h3
                                    array(
                                        'name'        => 'h3',
                                        'title'       => 'Titre H3 (rem)',
                                        'type'        => 'number',
                                        'default'     => 1.75,
                                        'min'         => 0,
                                        'max'         => 100,
                                        'step'        => 0.01,
                                        'option_name' => $this->single_db_name('heading', 'h3')
                                    ),
                                    // h4
                                    array(
                                        'name'        => 'h4',
                                        'title'       => 'Titre H4 (rem)',
                                        'type'        => 'number',
                                        'default'     => 1.5,
                                        'min'         => 0,
                                        'max'         => 100,
                                        'step'        => 0.01,
                                        'option_name' => $this->single_db_name('heading', 'h4')
                                    ),
                                    // h5
                                    array(
                                        'name'        => 'h5',
                                        'title'       => 'Titre H5 (rem)',
                                        'type'        => 'number',
                                        'default'     => 1.25,
                                        'min'         => 0,
                                        'max'         => 100,
                                        'step'        => 0.01,
                                        'option_name' => $this->single_db_name('heading', 'h5')
                                    ),
                                    // j6
                                    array(
                                        'name'        => 'h6',
                                        'title'       => 'Titre H6 (rem)',
                                        'type'        => 'number',
                                        'default'     => 1,
                                        'min'         => 0,
                                        'max'         => 100,
                                        'step'        => 0.01,
                                        'option_name' => $this->single_db_name('heading', 'h6')
                                    ),
                                )
                            )
                        )
                    ),
                    // Menus
                    array(
                        'slug'   => 'menu',
                        'title'  => 'Menus',
                        'fields' => array(
                            array(
                                'name'      => 'color',
                                'title'     => 'Couleur menu',
                                'type'      => 'color',
                                'default'   => array('#fff' => 'blanc'),
                                'multi'     => false,
                                'input'     => false,
                                // 'db_format' => '%2$s',
                                'option_name' => $this->single_db_name('menu', 'color'),
                                'swatches' => $colorSwatches,
                            ),
                            array(
                                'name'      => 'active-color',
                                'title'     => 'Couleur menu actif',
                                'type'      => 'color',
                                'default'   => array('#fff' => 'blanc'),
                                'multi'     => false,
                                'input'     => false,
                                // 'db_format' => '%2$s',
                                'option_name' => $this->single_db_name('menu', 'active-color'),
                                'swatches' => $colorSwatches,
                            ),
                            array(
                                'name'        => 'font-size',
                                'title'       => 'Taille texte',
                                'type'        => 'number',
                                'default'     => 1,
                                'min'         => 0,
                                'max'         => 100,
                                'step'        => 0.01,
                                'option_name' => $this->single_db_name('menu', 'font-size')
                            ),
                        )
                        ),
                    // Menus
                    array(
                        'slug'   => 'images',
                        'title'  => 'Images',
                        'fields' => array(
                            array(
                                'name'      => 'size',
                                'title'     => 'Taille image',
                                'type'      => 'custom',
                                'view'      => array('custom/image-size')
                            ),
                        )
                    )
                ),
            );


            $this->add_sub_menu($settingsMenu);
        }

        /**
         * Ajoute les pages de menu en admin
         *
         * @since  1.0.0
         * @param  array $settings
         * @return false|array {
         *      Array of Menu and Sub Menu Classes
         *          @type Settings_API_Menu 'menu'
         *          @type false|Settings_API_Sub_Menu 'sub-menu' 
         * }
         **/
        public static function add_menu($setting = array())
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
            $menu = self::create_setting($setting);

            // Add fields to "general" tab
            if (isset($setting['fields'])) {
                self::create_setting_fields($menu, $setting['fields']);
            }

            // Create additional tabs
            if (isset($setting['tabs'])) {
                foreach ($setting['tabs'] as $options) {
                    $menuTab = self::create_setting_tab($menu, $options);
                    // Create fields for this tab
                    if (isset($options['fields'])) {
                        self::create_setting_fields($menuTab, $options['fields']);
                    }
                }
            }

            // Create sub-menu for this parent menu
            $subMenu = false;
            if (isset($setting['sub-menus'])) {
                foreach ($setting['sub-menus'] as $options) {
                    $subMenu = self::create_setting_sub_menu($menu, $options);
                    // Add fields to "general" tab
                    if (isset($options['fields'])) {
                        self::create_setting_fields($subMenu, $options['fields']);
                    }
                    // Create additional tabs
                    if (isset($options['tabs'])) {
                        foreach ($options['tabs'] as $tabOptions) {
                            $subMenuTab = self::create_setting_tab($subMenu, $tabOptions);
                            // Create fields for this tab
                            if (isset($tabOptions['fields'])) {
                                self::create_setting_fields($subMenuTab, $tabOptions['fields']);
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
         * @return Settings_API_Sub_Menu
         * 
         **/
        public static function add_sub_menu($sub_settings = array())
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
            $sub = self::create_setting_sub_menu($sub_settings['parent_slug'], $sub_settings);
            // Add fields to "general" tab
            if (isset($sub_settings['fields'])) {
                self::create_setting_fields($sub, $sub_settings['fields']);
            }
            // Create additional tabs
            if (isset($sub_settings['tabs'])) {
                foreach ($sub_settings['tabs'] as $tabOptions) {
                    $tab = self::create_setting_tab($sub, $tabOptions);
                    // Create fields for this tab
                    if (isset($tabOptions['fields'])) {
                        self::create_setting_fields($tab, $tabOptions['fields']);
                    }
                }
            }

            return $sub;
        }

        /**
         * Settings page
         *
         * @param array $options Options to create menu
         * @return false|Settings_API
         **/
        private static function create_setting($options = array())
        {
            if (empty($options['menu_slug'])) return false;
            return new Settings_API_Menu($options);
        }

        /**
         * Settings fields
         *
         * @param Settings_API $menu
         * @param array $fields
         * @return false|Settings_API
         **/
        private static function create_setting_fields($menu = '', $fields = array())
        {
            if (
                !$menu instanceof Settings_API
                && !$menu instanceof Settings_API_Menu_Tab
            ) return false;

            foreach ($fields as $field) {
                $menu->add_field($field);
            }
            return $menu;
        }

        /**
         * Settings tabs
         *
         * @param Settings_API $parent
         * @param array $options
         * @return false|Settings_API
         **/
        private static function create_setting_tab($parent = '', $options = array())
        {
            if (!$parent instanceof Settings_API) {
                return false;
            }
            return new Settings_API_Menu_Tab($options, $parent);
        }

        /**
         * Settings sub menu
         *
         * @param string|Settings_API $parent Parent slug pour les menus existant/built'in
         * @param array $options
         * @return Settings_API_Sub_Menu
         **/
        private static function create_setting_sub_menu($parent = '', $options = array())
        {
            $default = new Settings_API_Menu();
            $options = wp_parse_args($options, $default);
            return new Settings_API_Sub_Menu($options, $parent);
        }
    }
}
