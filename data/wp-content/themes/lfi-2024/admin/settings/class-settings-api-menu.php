<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Settings_API_Menu')) {

    /**
     * Settings_API_Menu
     *
     *  Aide à la création de page de paramètre pour WP
     *
     * @since 1.0.0
     * @package {file_name}
     *
     */

    class Settings_API_Menu extends Settings_API
    {

        /**
         * Default options
         * @var array
         */
        public $defaultOptions = array(
            'menu_slug'   => '', // Unique Name of the menu item, see sanitize_key() (required)
            'parent_slug' => null, // id of parent, if blank, then this is a top level menu
            'menu_title'  => '', // Menu title text to be used for the menu. (required)
            'page_title'  => '', // The text to be displayed in the title tags of the page when the menu is selected
            'capability'  => 'manage_options', // User role
            'icon'        => 'dashicons-admin-generic', // Menu icon for top level menus only http://melchoyce.github.io/dashicons/
            'position'    => null, // Menu position. Can be used for both top and sub level menus
            'desc'        => '', // Description displayed below the title
            'function'    => 'create_menu_page',
        );

        /**
         * Gets populated on submenus, contains slug of parent menu
         * @var null
         */
        public $parent_id = null;


        public function __construct($options = array())
        {

            $this->menu_options = array_merge($this->defaultOptions, $options);

            // If menu_slug empty do nothing - Helps to get default options
            if ($this->menu_options['menu_slug'] == '') {
                return;
            }

            $this->menu_slug = $this->menu_options['menu_slug'];

            $this->prepopulate();

            add_action('admin_menu', array($this, 'add_page'), 99);

            add_action('Settings_API_Menu_page_save_' . $this->menu_slug, array($this, 'save_settings'));
        }

        /**
         * Enqueue script for menu_page
         *
         **/
        public function load_page_scripts()
        {
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 10, 1);
            do_action('load_page_scripts', $this->menu_slug);
        }

        /**
         * Enqueue admin scripts
         * Lancer uniquement lorsque la page est crée
         *
         * @param string $hook_suffix
         **/
        public function admin_enqueue_scripts($hook_suffix)
        {
            $helper = LFI_Helper();

            $styles = array(
                0 => array('wp-color-picker'),
                1 => array(
                    'select2',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css',
                ),
                2 => array(
                    'bootstrap-css',
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
                    array(),
                    '5.3.2'
                ),
                3 => array(
                    'font-awesome',
                    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css'
                ),
                4 => array(
                    'pickr-nano',
                    'https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/nano.min.css'
                ),
                5 => array(
                    'iconpicker',
                    $helper->get_uri_path('admin/css/fontawesome-iconpicker.min.css'),
                ),
                6 => array(
                    'settings-color',
                    $helper->get_uri_path('admin/css/settings-color.css'),
                    array('dashicons')
                ),
                7 => array(
                    'custom-select2',
                    $helper->get_uri_path('admin/css/select2.css'),
                    array('dashicons')
                )
            );

            $scripts = array(
                0 => array(
                    'wp-color-picker-alpha',
                    $helper->get_uri_path('admin/js/wp-color-picker-alpha.min.js'),
                    array('wp-color-picker'),
                    '2.1.3',
                    true
                ),
                1 => array(
                    'select-2',
                    'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js',
                    array('jquery'),
                    '4.1.0',
                    true,
                ),
                2 => array(
                    'popper',
                    'https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js',
                    array('jquery'),
                    '1.16.0',
                    true
                ),
                3 => array(
                    'bootstrap-js',
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
                    array('jquery'),
                    '5.3.2',
                    true
                ),
                4 => array(
                    'pickr',
                    'https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js',
                    array(),
                    false,
                    true
                ),
                5 => array(
                    'iconpicker',
                    $helper->get_uri_path('admin/js/fontawesome-iconpicker.min.js'),
                    array('jquery'),
                    false,
                    true
                ),
                6 => array(
                    'settings-color',
                    $helper->get_uri_path('admin/js/settings-color.js'),
                    array('jquery'),
                    false,
                    true
                ),
                7 => array(
                    'media-upload',
                    $helper->get_uri_path('admin/js/media-upload.js'),
                    array('jquery'),
                    false,
                    true
                ),
                8 => array(
                    'settings-custom',
                    $helper->get_uri_path('admin/js/settings-custom.js'),
                    array('jquery'),
                    false,
                    true
                ),
                9 => array(
                    'settings-general',
                    $helper->get_uri_path('admin/js/settings-general.js'),
                    // $jsPath . '/settings-general.js',
                    array('jquery'),
                    '1.0.0',
                    true
                )
            );

            wp_enqueue_media();

            // Loop to enqueue all styles
            foreach ($styles as $key => $style) {
                if (empty($style[0]) || empty($style[1])) {
                    continue;
                }
                $handle = $style[0];
                $src = $style[1];
                $deps = !empty($style[2]) ? $style[2] : array();
                $ver = !empty($style[3]) ? $style[3] : false;
                $media = !empty($style[4]) ? $style[4] : 'all';
                wp_enqueue_style(
                    $handle,
                    $src,
                    $deps,
                    $ver,
                    $media,
                );
            }

            // Loop to enqueue all scripts
            foreach ($scripts as $key => $script) {
                if (empty($style[0]) || empty($style[1])) {
                    continue;
                }
                $handle = $script[0];
                $src = $script[1];
                $deps = !empty($script[2]) ? $script[2] : array();
                $ver = !empty($script[3]) ? $script[3] : false;
                $in_footer = !empty($script[4]) ? $script[4] : true;
                wp_enqueue_script(
                    $handle,
                    $src,
                    $deps,
                    $ver,
                    $in_footer,
                );
            }
        }

        /**
         * Populate some of required options
         * @return void
         */
        public function prepopulate()
        {

            if ($this->menu_options['menu_title'] == '') {
                $this->menu_options['menu_title'] = ucfirst($this->menu_options['menu_slug']);
            }

            if ($this->menu_options['page_title'] == '') {
                $this->menu_options['page_title'] = $this->menu_options['menu_title'];
            }
        }

        /**
         * Add the menu page using WordPress API
         * @return [type] [description]
         */
        public function add_page()
        {
            $functionToUse = $this->menu_options['function'];
            $functionToUse = ($functionToUse == 'create_menu_page') ? array($this, 'create_menu_page') : $functionToUse;

            if ($this->parent_id != null) {
                $this->menu_hook = add_submenu_page(
                    $this->parent_id,
                    $this->menu_options['page_title'],
                    $this->menu_options['menu_title'],
                    $this->menu_options['capability'],
                    $this->menu_options['menu_slug'],
                    $functionToUse
                );
                add_action('load-' . $this->menu_hook, array($this, 'load_page_scripts'));
            } else {
                $this->menu_hook = add_menu_page(
                    $this->menu_options['page_title'],
                    $this->menu_options['menu_title'],
                    $this->menu_options['capability'],
                    $this->menu_options['menu_slug'],
                    $functionToUse,
                    $this->menu_options['icon'],
                    $this->menu_options['position']
                );
                add_action('load-' . $this->menu_hook, array($this, 'load_page_scripts'));
            }
        }

        /**
         * Create the menu page
         * @return void
         */
        public function create_menu_page()
        {
            $this->save_if_submit();

            $tab = 'general';

            if (isset($_GET['tab'])) {
                $tab = $_GET['tab'];
            }

            if (empty($this->dbValues)) {
                $this->get_values_from_db();
            }

            set_query_var(
                'templateVar',
                array(
                    'WPMenu' => $this,
                    'tab'    => $tab
                )
            );

            $this->get_template_view('settings-page');
        }

        /**
         * Render the registered tabs
         * @param  string $active_tab the viewed tab
         * @return void
         */
        public function render_tabs($active_tab = 'general')
        {
            $this->activeTab = $active_tab;

            if (count($this->tabs) > 1) {

                echo '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">';

                foreach ($this->tabs as $key => $value) {

                    echo '<a href="' . admin_url('admin.php?page=' . $this->menu_options['menu_slug'] . '&tab=' . $key) . '" class="nav-tab ' . (($key == $active_tab) ? 'nav-tab-active' : '') . ' ">' . $value . '</a>';
                }

                echo '</h2>';
                echo '<br/>';
            }
        }

        /**
         * Save if the button for this menu is submitted
         * @return void
         */
        protected function save_if_submit()
        {
            if (isset($_POST[$this->menu_slug . '_save'])) {
                do_action('Settings_API_Menu_page_save_' . $this->menu_slug);
            }
        }
    }
}
