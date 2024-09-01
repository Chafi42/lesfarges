<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('LFI_Helper')) {

    /**
     * LFI_Helper
     *
     *  Class d'aide à l'utilisation du thème
     *
     * @since   1.0.0
     * @package lfi-2024
     *
     */
    class LFI_Helper
    {
        /**
         * @var string $version Theme version
         */
        protected $version = '1.0.0';

        /**
         * @var string $domain Theme text-domain
         */
        protected $domain = 'lfi-2024';

        /**
         * @var string $name  
         */
        protected $name = 'lfi-2024';

        /**
         * @var string $status  
         */
        protected $status = 'dev';

        /**
         * @var LFI_Helper
         * @access private
         * @static
         */
        private static $_instance = null;

        /**
         * Méthode qui crée l'unique instance de la classe
         * si elle n'existe pas encore puis la retourne.
         *
         * @param array $args Tableau associatif pour variables de la classe (ex: 'version' ou 'domain )
         * @return LFI_Helper
         */
        public static function getInstance($args = array())
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new LFI_Helper($args);
            }
            return self::$_instance;
        }

        /**
         * Contructor pour la class LFI_Helper, 
         * ne peux pas être utiliser directement, 
         * il faut passer par ::getInstance.
         */
        private function __construct($args = array())
        {
            $defaults = array(
                'version' => '1.0.0',
                'domain' => 'lfi-2024',
                'name' => 'lfi-2024',
                'status' => 'dev'
            );
            $args = wp_parse_args($args, $defaults);
            foreach ($args as $var => $value) {
                $this->{$var} = $value;
            }
        }

        /**
         * Get a random number
         *
         * Useful to have a new css or js file every time and avoid caching
         *
         * @return string
         **/
        public function get_random($min = 1, $max = 99999999999)
        {
            return rand($min, $max);
        }

        /**
         * Get Theme name
         *
         * @since 1.0.0
         * @package lfi-2024
         * @return string Theme name
         **/
        public function name()
        {
            if (wp_get_theme()->get('Name')) {
                $name = sanitize_title(wp_get_theme()->get('Name'));
                if ($name !== $this->name) {
                    $this->name = $name;
                }
            }

            return $this->name;
        }

        /**
         * Get a Theme version
         *
         * @return string Theme version number
         **/
        public function version()
        {
            if (wp_get_theme()->get('Version')) {
                $version = wp_get_theme()->get('Version');
                if ($version !== $this->version) {
                    $this->version = $version;
                }
            }

            return $this->version;
        }

        /**
         * Get a Theme domain
         *
         * @return string Theme domain
         **/
        public function domain()
        {
            if (wp_get_theme()->get('TextDomain')) {
                $domain = wp_get_theme()->get('TextDomain');
                if ($domain !== $this->domain) {
                    $this->domain = $domain;
                }
            }

            return $this->domain;
        }

        /**
         * Get theme path URI or ABS
         *
         * @param string $file  file path from folder (ex: class-lfi.php)
         * @param string $type  Absolute (abs) or Relative (uri)
         * @param bool   $child Is child theme or not
         * @return string
         **/
        private function get_path($file = '', $type = 'abs', $child = false)
        {
            if ($child) {
                $abs_path = trailingslashit(get_stylesheet_directory());
                $uri_path = trailingslashit(get_stylesheet_directory_uri());
            } else {
                $abs_path = trailingslashit(get_template_directory());
                $uri_path = trailingslashit(get_template_directory_uri());
            }

            if ($this->status == "dev" && $type == "uri") {
                $file = $file . '?' . $this->get_random();
            }

            switch ($type) {
                case 'uri':
                    return $uri_path . $file;
                    break;

                default:
                    return $abs_path . $file;
                    break;
            }
        }

        /**
         * Get the plugin absolute path + the file path given
         *
         * @since      1.0.0
         * @package    lfi-2024
         * @subpackage lfi-2024/includes
         * 
         * @param string $file  path from the plugin dir
         * @param bool   $child is it child theme
         * @return string
         * 
         **/
        public function get_abs_path($file = '', $child = false)
        {
            return $this->get_path($file, 'abs', $child);
        }

        /**
         * Get the plugin relative path + the file path given
         * 
         * @since      1.0.0
         * @package    lfi-2024
         * @subpackage lfi-2024/includes
         *
         * @param string $file path from the plugin dir
         * @param bool   $child is it child theme
         * @return string
         * 
         **/
        public function get_uri_path($file = '', $child = false)
        {
            return $this->get_path($file, 'uri', $child);
        }

        /**
         * Assets Path URI
         *
         * @param string $type Type d'asset à récupérer (CSS, JS, IMG)
         * @param string $filename file path relative to folder type
         * @return string file path uri
         **/
        public function asset_uri($filename = 'style.css', $type = 'css')
        {
            $folder = trailingslashit('assets/' . $type);
            return $this->get_path($filename, $folder, 'uri');
        }

        /**
         * Require files wrapper
         * 
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies 
         *      $dependencies = [
         *          'type' = [
         *              'file path from ',
         *              'file',
         *              'file',
         *              ...
         *          ]
         *      ]  
         * @param bool $child is for child theme
         * @param bool $once  require_once or require
         * @return void
         * 
         **/
        public function require($dependencies = array(), $child = false, $once = true)
        {
            foreach ($dependencies as $type => $files) {
                $type = is_numeric($type) ? 'abs' : $type;
                if (!is_array($files)) continue;
                foreach ($files as $filename) {
                    if ($once) {
                        require_once $this->get_path($filename, $type, $child);
                    } else {
                        require $this->get_path($filename, $type, $child);
                    }
                }
            }
        }

        /**
         * Get theme mod helper
         *
         * @param string $setting setting name
         * @param string $sub_setting setting field name
         * @param string $default default return
         * @return mixed
         * @since 1.0.0
         * @access public
         **/
        public function get_settings($setting = '', $sub_setting = '', $default = '')
        {
            if (empty($setting)) return array();
            $data = get_theme_mod($setting, array());
            if (empty($sub_setting)) return $data;
            return isset($data[$sub_setting]) ? $data[$sub_setting] : $default;
        }

        /**
         * Get color from theme settings
         *
         * @param string $setting 
         * @param string $sub_setting 
         * @param string $color_type 
         * @since 1.0.0
         * @access public
         **/
        public function get_color_setting($setting = '', $sub_setting = '', $color_type = 'hex')
        {
            $result = array();
            $data = $this->get_settings($setting, $sub_setting, array());
            if (!empty($data)) {
                foreach ($data as $hex => $name) {
                    if ($color_type == 'hex') {
                        $result[] = $hex;
                    } else {
                        $name = isset($name) ? $name : $hex;
                        $result[] = array(
                            'name' => ucfirst($name),
                            'slug' => sanitize_title($name),
                            'color' => $hex,
                        );
                    }
                }
            }
            return $result;
        }

        /**
         * Return some default image if file doesn't exist
         *
         * @param string $src
         * @return string
         **/
        public function get_default_image($src = '', $size = '1920x1080', $type = '')
        {
            $default = 'https://via.placeholder.com/' . $size;
            if ($type == 'svg') {
                $default = 'https://placeholder.pics/svg/' . $size;
            }
            $path = trim(parse_url($src, PHP_URL_PATH), '/');
            $absSrc = trailingslashit(ABSPATH) . $path;
            if (empty($src) || !file_exists($absSrc)) return $default;
            return $src;
        }

        /**
         * Définition des noms d'options
         *
         * Utilisé par défaut par le thème
         * @param string $tab
         * @return array
         **/
        public function settings_options_name($tab = '')
        {
            // Array of input names => database_name
            $settings = array(
                'general' => array(
                    'public-v2'   => 'recaptcha_public_v2',
                    'secret-v2'   => 'recaptcha_secret_v2',
                    'public-v3'   => 'recaptcha_public_v3',
                    'secret-v3'   => 'recaptcha_secret_v3',
                    'maps-api'    => 'maps_api_key',
                    'colors'      => 'colors',
                    'logo'        => 'logo',
                    'site-title'  => 'site_title',
                    'tagline'     => 'tagline',
                ),
                'loader'  => array(
                    'background'   => 'bg_color',
                    'border'       => 'border_color',
                    'border-top'   => 'border_top_color',
                    'width'        => 'width',
                    'height'       => 'height',
                    'border-width' => 'border_width'
                ),
                'font'    => array(
                    'small'       => 'sm',
                    'medium'      => 'md',
                    'large'       => 'lg',
                    'x-large'     => 'xl',
                ),
                'heading' => array(
                    'h1'          => 'h1',
                    'h2'          => 'h2',
                    'h3'          => 'h3',
                    'h4'          => 'h4',
                    'h5'          => 'h5',
                    'h6'          => 'h6',
                ),
                'menu' => array(
                    'color'        => 'color',
                    'active-color' => 'active_color',
                    'font-size'    => 'font_size',
                )
            );

            if (in_array($tab, array_keys($settings))) {
                return $settings[$tab];
            } else {
                return $settings;
            }
        }

        /**
         * Retreive single option database name
         *
         * @param string $option Name to retreive
         * @return false|string
         **/
        public function single_db_name($tab = 'general', $setting = '')
        {
            $options = $this->settings_options_name($tab);
            $prefix = 'theme_';

            if (empty($setting)) return false;

            foreach ($options as $id => $name) {
                if ($id !== $setting) continue;
                return $prefix . $tab . '_' . $name;
            }
        }

        /**
         * Loader styles, from settings saved into option database.
         * return inline css.
         *
         * @return string
         **/
        public function loader_style()
        {
            $loader_options = $this->settings_options_name('loader');
            $loader_values = array();
            foreach ($loader_options as $id => $name) {
                $value = get_option($this->single_db_name('loader', $id), '#fff');
                if (in_array($id, array('background', 'border', 'border-top'))) {
                    $value = is_array($value) ? key($value) : $value;
                    $loader_values[$id] = $value;
                } else {
                    $loader_values[$id] = $value;
                }
            }

            $inlineCss = '';
            if (!empty($loader_values['background'])) {
                $inlineCss .= '.loader-body, .lightboxOverlay{';
                $inlineCss .= 'background-color:' . $loader_values['background'];
                $inlineCss .= '}';
                $inlineCss .= '.lightbox .lb-image{';
                $inlineCss .= 'border-color:' . $loader_values['background'];
                $inlineCss .= '}';
            }
            $inlineCss .= '.loader, .lb-cancel{';
            $inlineCss .= !empty($loader_values['width']) ? 'width:' . $loader_values['width'] . 'px;' : '';
            $inlineCss .= !empty($loader_values['height']) ? 'height:' . $loader_values['height'] . 'px;' : '';
            $inlineCss .= !empty($loader_values['border-width']) ? 'border-width:' . $loader_values['border-width'] . 'px;' : '';
            $inlineCss .= !empty($loader_values['border']) ? 'border-color:' . $loader_values['border'] . ';' : '';
            $inlineCss .= !empty($loader_values['border-top']) ? 'border-top-color:' . $loader_values['border-top'] . ';' : '';
            $inlineCss .= '}';

            return $inlineCss;
        }

        /**
         * Add font style 
         *
         * @since 1.0.0
         * 
         * @return string 
         **/
        public function font_style($root = "body", $important = false)
        {
            $font_styles = $this->settings_options_name('font');
            $output = $root . "{";
            foreach ($font_styles as $id => $name) {
                $value = get_option($this->single_db_name('font', $id));
                $output .= "--wp--preset--font-size--" . $id . ": " . $value . "rem";
                $output .= $important ? " !important;" : ";";
            }
            $output .= "}";

            return $output;
        }
        
        /**
         * Add heading style 
         *
         * @since 1.0.0
         * 
         * @return string 
         **/
        public function heading_style($root = false, $important = false)
        {
            $heading_styles = $this->settings_options_name('heading');
            $output = "";
            foreach ($heading_styles as $id => $name) {
                $value = get_option($this->single_db_name('heading', $id));
                $tag = $root ? $root . ' ' . $id : $id;
                $output .= $tag . ", ";
                $class = $root ? $root . ' .' . $id : '.' . $id;
                $output .= $class . "{";
                    $output .= "font-size: " . $value . "rem";
                    $output .= $important ? " !important" : "";
                    $output .= ";}";
                }

            return $output;
        }

        /**
         * Add heading style 
         *
         * @since 1.0.0
         * 
         * @return string 
         **/
        public function menu_style($important = false)
        {
            $heading_styles = $this->settings_options_name('menu');
            $output = "";
            foreach ($heading_styles as $id => $name) {
                $value = get_option($this->single_db_name('menu', $id));
                $output .= ".menu-item,";
                $output .= ".menu-item .nav-link,";
                $output .= ".menu-item .dropdown-item,";
                $output .= ".menu-item .nav-link.show{";
                if ($id == "font-size") {
                    $output .= "font-size: " . $value . "rem";
                    $output .= $important ? " !important;" : ";";
                } 
                if ($id == "color") {
                    $output .= "color: " . $value;
                    $output .= $important ? " !important;" : ";";
                } 
                $output .= "}";
                if($id == "active-color"){
                    $output .= ".menu-item.current-menu-item .nav-link.active,";
                    $output .= ".dropdown-item.active, .dropdown-item:active{";
                    $output .= "color: " . $value;
                    $output .= $important ? " !important;" : ";";
                    $output .= "}";
                }
            }

            return $output;
        }

        /**
         * Return an array of theme colors
         *
         * @param bool $hex return only hexadecimal if false array(hex => color_name)
         * @return array
         **/
        public function theme_colors($hex = true)
        {
            $options = $this->settings_options_name('general');
            /**
             * Allows to add|push by code, theme colors.
             *
             * @since 1.0.0
             *
             * @param array $colors array of colors color_hex => color_name
             */
            $defaultColors = apply_filters('lfi_theme_default_colors', array());

            $colors = wp_parse_args(
                get_option(
                    $this->single_db_name('general', 'colors'),
                    array()
                ),
                $defaultColors
            );

            if (!is_array($colors)) return false;

            $output = array();
            foreach ($colors as $colorHex => $colorName) {
                if ($hex) {
                    $output[] = $colorHex;
                } else {
                    $colorName = isset($colorName) ? $colorName : $colorHex;
                    $output[] = array(
                        'name' => ucfirst($colorName),
                        'slug' => sanitize_title($colorName),
                        'color' => $colorHex,
                    );
                }
            }
            return $output;
        }

        /**
         * Inline style for gutenberg editor
         * Retreive all colors from theme settings and create classes to match Gutenberg style.
         *
         * @since 1.0.0
         * 
         * @return string
         **/
        public function color_style()
        {
            $colors = $this->theme_colors(false);
            $inlineCss = '';
            foreach ($colors as $key => $value) {
                $inlineCss .= '.has-' . $value['slug'] . '-background-color {';
                $inlineCss .= 'background-color:' . $value['color'] . '!important';
                $inlineCss .= '}';
                $inlineCss .= '.has-' . $value['slug'] . '-color {';
                $inlineCss .= 'color:' . $value['color'] . '!important;';
                $inlineCss .= 'border-color:' . $value['color'] . '!important;';
                $inlineCss .= '}';
                $inlineCss .= '.has-' . $value['slug'] . '-border-top-color {';
                $inlineCss .= 'border-top-color:' . $value['color'] . '!important;';
                $inlineCss .= '}';
                $inlineCss .= '.has-' . $value['slug'] . '-border-bottom-color {';
                $inlineCss .= 'border-bottom-color:' . $value['color'] . '!important;';
                $inlineCss .= '}';
                $inlineCss .= '.hover-' . $value['slug'] . '-color:hover {';
                $inlineCss .= 'color:' . $value['color'] . '!important;';
                $inlineCss .= '}';
                $inlineCss .= '.placeholder-' . $value['slug'] . '::-webkit-input-placeholder {';
                $inlineCss .= 'color:' . $value['color'] . '!important;';
                $inlineCss .= '}';
                $inlineCss .= '.placeholder-' . $value['slug'] . '::-ms-input-placeholder {';
                $inlineCss .= 'color:' . $value['color'] . '!important;';
                $inlineCss .= '}';
                $inlineCss .= '.placeholder-' . $value['slug'] . '::placeholder {';
                $inlineCss .= 'color:' . $value['color'] . '!important;';
                $inlineCss .= '}';
            }

            // $inlineCss .= $this->font_style(".editor-styles-wrapper", true);
            // $inlineCss .= $this->heading_style(true);

            return $inlineCss;
        }

        /**
         * Retreive theme colors and create an array for Gutenberg palette
         *
         * @since 1.0.0
         *
         * @return array
         **/
        public function gutenberg_editor_palette()
        {
            $colors = $this->theme_colors(false);
            return $colors;
        }

        /**
         * Is setting enabled
         * Test whether the settings is been checked or not 
         *
         * @param string $tab
         * @param string $setting
         * @return bool
         **/
        public function is_setting_enabled($tab = 'general', $setting = '')
        {
            return get_option($this->single_db_name($tab, $setting), true);
        }
    }
}
