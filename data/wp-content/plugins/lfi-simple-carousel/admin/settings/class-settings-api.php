<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('LSC_Settings_API')) {

    /**
     * LSC_Settings_API
     *
     *  La classe abstraite des paramètres
     *
     * @since 1.0.0
     * @package lfi-simple-carousel
     *
     */

    abstract class LSC_Settings_API
    {
        /**
         * ID of the settings
         * @var string
         */
        public $menu_slug = '';

        /**
         * Menu Hook
         *
         * @since    1.0.0
         * @access   public
         * @var      string    $menu_hook    Menu Hook
         */
        public $menu_hook;

        /**
         * Tabs for the settings page
         * @var array
         */
        public $tabs = array(
            'general' => 'General'
        );

        /**
         * @var string $activeTab l'onglet en cours d'affichage/sauvegarde 
         */
        var $activeTab = 'general';

        /**
         * Settings/Options/Custom values from database
         * @var array
         */
        public $dbValues = array();

        /**
         * Array of fields for the general tab
         * array(
         *     'tab_slug' => array(
         *         'field_name' => array(),
         *         ),
         *     )
         * @var array
         */
        public $fields = array();

        /**
         * Data gotten from POST
         * @var array
         */
        public $posted_data = array();

        /**
         * Menu options
         * @var array
         */
        public $menu_options = array();

        /**
         * Get the values from the database
         * @return void
         */
        public function get_values_from_db()
        {
            if (empty($this->dbValues)) {
                // Get settings from option
                $options = $this->get_fields_by_save_method();
                foreach ($options as $slug => $field_options) {
                    $dbName = $this->database_name($field_options);
                    $default = $field_options['default'];
                    $this->dbValues[$slug] = get_option($dbName, $default);
                }

                // Get setting from theme mod
                $theme_mod = $this->get_fields_by_save_method('theme');
                foreach ($theme_mod as $slug => $field_options) {
                    $dbName = $this->database_name($field_options);
                    $default = $field_options['default'];
                    $this->dbValues[$slug] = get_theme_mod($dbName, $default);
                }

                // Get settings with custom filter
                $custom = $this->get_fields_by_save_method('custom');
                foreach ($custom as $slug => $field_options) {
                    /**
                     * Retrieve setting data with custom function
                     * 
                     * @since 1.0.0
                     * 
                     * @param string $default Field value (default)
                     * @param LSC_Settings_API $menu Current menu class
                     * @param string $field_options
                     * 
                     */
                    $data = apply_filters(
                        'lfi_extras_settings_get_custom',
                        $field_options['default'],
                        $this,
                        $field_options
                    );
                    if ($data) $this->dbValues[$slug] = $data;
                }
            }
        }

        /**
         * Retrieve all fields with their options
         *
         * @since 1.0.0
         * @package lfi-simple-carousel
         * 
         * @return array
         **/
        private function get_all_fields()
        {
            $allFields = array();
            foreach ($this->fields as $tab_slug => $tab_fields) {
                foreach ($tab_fields as $field_slug => $field_options) {
                    if ($field_options['type'] === 'group') {
                        foreach ($field_options['fields'] as $key => $grpField) {
                            $allFields[] = $grpField;
                        }
                    } else {
                        $allFields[] = $field_options;
                    }
                }
            }
            return $allFields;
        }

        /**
         * Retrieve fields by save_method
         *
         * @since 1.0.0
         * @package lfi-simple-carousel
         * 
         * @param string $method Save method
         * @return array
         **/
        private function get_fields_by_save_method($method = 'option')
        {
            $allFields = $this->get_all_fields();
            $options = array();
            $theme_mod = array();
            $custom = array();
            foreach ($allFields as $key => $field_options) {
                $name = $field_options['name'];
                switch ($field_options['save_method']) {
                    case 'option':
                        $options[$name] = $field_options;
                        break;

                    case 'theme':
                        $theme_mod[$name] = $field_options;
                        break;

                    default:
                        $custom[$name] = $field_options;
                        break;
                }
            }

            switch ($method) {
                case 'option':
                    return $options;
                    break;

                case 'theme':
                    return $theme_mod;
                    break;

                case 'custom':
                    return $custom;
                    break;
            }
        }

        /**
         * Save settings from POST
         * @return [type] [description]
         */
        public function save_settings()
        {
            $this->posted_data = $_POST;
            $postedTab = isset($this->posted_data['tab']) ? $this->posted_data['tab'] : 'general';

            if (empty($this->dbValues)) {
                $this->get_values_from_db();
            }

            $option = $this->get_fields_by_save_method();
            foreach ($option as $slug => $field_options) {
                if (!isset($this->posted_data[$slug])) continue;
                $dbName = $this->database_name($field_options);
                $callback = $this->get_callback($field_options);
                $field_value = call_user_func($callback, $slug);
                update_option($dbName, $field_value);
                $this->dbValues[$slug] = $field_value;
            }

            $theme = $this->get_fields_by_save_method('theme');
            foreach ($theme as $slug => $field_options) {
                if (!isset($this->posted_data[$slug])) continue;
                $dbName = $this->database_name($field_options);
                $callback = $this->get_callback($field_options);
                $field_value = call_user_func($callback, $slug);
                set_theme_mod($dbName, $field_value);
                $this->dbValues[$slug] = $field_value;
            }

            $custom = $this->get_fields_by_save_method('custom');
            foreach ($custom as $slug => $field_options) {
                if (!isset($this->posted_data[$slug])) continue;
                /**
                 * Action use for custom saving fields
                 * 
                 * @since 1.0.0
                 * 
                 * @param string $slug Field name (slug)
                 * @param array $field_options
                 * @param LSC_Settings_API $menu Current menu object
                 * 
                 */
                do_action('lfi_save_custom_field', $slug, $field_options, $this);
            }
        }

        /**
         * Return validation callback name
         *
         * @since 1.0.0
         * @package lfi-simple-carousel
         * 
         * @param array $field_param 
         * @return string
         **/
        private function get_callback($field_param = array())
        {
            $callback = isset($field_param['validate']) && $field_param['validate']
                ? $field_param['validate'] : null;
            if ($callback) return $callback;

            return array($this, 'validate_' . $field_param['type']);
        }

        /**
         * Make database name
         *
         * @param array $field_options
         * @return string
         **/
        private function database_name($field_options = array())
        {
            if (empty($field_options)) return false;

            $field_slug = $field_options['name'];
            $format = isset($field_options['db_format']) ? $field_options['db_format'] : '%1$s-%2$s';

            /**
             * Database name format
             *
             * @since 1.0.0
             * @package lfi-simple-carousel
             *
             * @param string $format String format to be used for database, default '%1$s-%2$s'
             * @param mixed $menu_options Menu options, 'slug' is used as first argument for sprintf function.
             * @param mixed $field_slug field 'slug' used as second argument for the sprintf function.
             */
            $database_format = apply_filters('lfi_extras_settings_db_name_format', $format, $this->menu_options, $field_slug);
            return esc_sql(sprintf($database_format, $this->menu_slug, $field_slug));
        }

        /**
         * Gets and option from the settings API, using defaults if necessary to prevent undefined notices.
         *
         * @param  string $key
         * @param  string $groupName
         * @param  mixed  $empty_value
         * @return mixed  The value specified for the option or a default value for the option.
         */
        public function get_option($key)
        {
            if (empty($this->dbValues)) {
                $this->get_values_from_db();
            }
            return isset($this->dbValues[$key]) ? $this->dbValues[$key] : false;
        }

        public function get_posted_data($field)
        {
            $posted_data = isset($this->posted_data[$field])
                ? $this->posted_data[$field]
                : '';

            return $posted_data;
        }

        /**
         * Get theme domain
         *
         * @return string
         **/
        private function theme_domain()
        {
            $helper = LSC_Helper::getInstance();
            return $helper->get_plugin_domain();
        }

        /**
         * Make numeric value an Integer or a Float
         *
         * @param mixed $num Number
         * @return int|float
         **/
        private function to_number($num)
        {
            $int = (int) $num;
            $float = (float) $num;
            return $int === $float ? $int : $float;
        }

        /**
         * Detect if it is numeric or string then return
         * validated/sanitized value.
         *
         * @param mixed $var
         * @return mixed
         **/
        private function string_or_numeric($var)
        {
            if (is_numeric($var)) return $this->to_number($var);
            return sanitize_text_field($var);
        }

        /**
         * Validate group of fields
         * @param  string $grpName name of the group
         * @param  array $fields fields of the group
         * @return string
         */
        public function validate_group($grpName, $fields)
        {
            $grpVal = array();
            foreach ($fields as $key => $field) {
                $fieldDefault = $field['default'];
                $fieldName = $field['name'];
                $fieldType = ($field['type'] === 'custom') ? $field['validate'] : $field['type'];
                $validate = 'validate_' . $fieldType;
                $grpVal[$fieldName] = $this->{$validate}($fieldName, $grpName);
            }
            return $grpVal;
        }

        /**
         * Validate text field
         * @param  string $key name of the field
         * @return string
         */
        public function validate_text($key)
        {
            $posted_data = $this->get_posted_data($key);
            return $this->string_or_numeric($posted_data);
        }

        /**
         * Validate textarea field
         * @param  string $key name of the field
         * @return string
         */
        public function validate_textarea($key)
        {
            $posted_data = $this->get_posted_data($key);

            $textarea = wp_kses(
                trim(stripslashes($posted_data)),
                array_merge(
                    array(
                        'iframe' => array(
                            'src' => true,
                            'style' => true,
                            'id' => true,
                            'class' => true
                        )
                    ),
                    wp_kses_allowed_html('post')
                )
            );

            return $textarea;
        }

        /**
         * Validate WPEditor field
         * @param  string $key name of the field
         * @return string
         */
        public function validate_wpeditor($key)
        {
            $posted_data = $this->get_posted_data($key);
            $text = wp_kses(
                trim(
                    stripslashes($posted_data)
                ),
                array_merge(
                    array(
                        'iframe' => array(
                            'src' => true,
                            'style' => true,
                            'id' => true,
                            'class' => true
                        )
                    ),
                    wp_kses_allowed_html('post')
                )
            );

            return $text;
        }

        /**
         * Validate select field
         * @param  string $key name of the field
         * @return string
         */
        public function validate_select($key)
        {
            $posted_data = $this->get_posted_data($key);
            return $this->string_or_numeric($posted_data);
        }

        /**
         * Validate radio
         * @param  string $key name of the field
         * @return string
         */
        public function validate_radio($key)
        {
            $posted_data = $this->get_posted_data($key);
            return $this->string_or_numeric($posted_data);
        }

        /**
         * Validate checkbox field
         * @param  string $key name of the field
         * @return string
         */
        public function validate_checkbox($key)
        {
            return $this->to_number($this->get_posted_data($key));
        }

        /**
         * Validate color field
         * @param  string $key name of the field
         * @return string
         */
        public function validate_color($key)
        {
            $posted_data = $this->get_posted_data($key);

            if (is_array($posted_data)) {
                $colors = array();
                foreach ($posted_data as $colorHex => $colorName) {
                    $colorName = isset($colorName) ? $colorName : $colorHex;
                    $colors[$colorHex] = sanitize_title($colorName);
                }
                return $colors;
            }
        }

        /**
         * Validate media field
         * @param  string $key name of the field
         * @return string
         */
        public function validate_media($key)
        {
            return absint(
                $this->get_posted_data($key)
            );
        }

        /**
         * Validate icon field
         * @param  string $key name of the field
         * @return string
         */
        public function validate_icon($key)
        {
            $posted_data = $this->get_posted_data($key);
            $icon = wp_kses_post(
                trim(
                    stripslashes($posted_data)
                )
            );
            return $icon;
        }

        /**
         * Validate range field
         * @param  string $key name of the field
         * @return string
         */
        public function validate_range($key)
        {
            $posted_data = $this->get_posted_data($key);
            $int = (int) $posted_data;
            $float = (float) $posted_data;
            return $int === $float ? $int : $float;
        }

        /**
         * Validate number field
         * @param  string $key name of the field
         * @return string
         */
        public function validate_number($key)
        {
            $posted_data = $this->get_posted_data($key);
            $int = (int) $posted_data;
            $float = (float) $posted_data;
            return $int === $float ? $int : $float;
        }

        /**
         * les paramètres par défaut en général
         *
         * @return array
         **/
        public function default_args()
        {
            $defaults = array(
                'name'        => '',
                'type'        => 'text',
                'title'       => '',
                'default'     => null,
                'placeholder' => '',
                'options'     => array(),
                'desc'        => '',
                'view'        => array(),
                'save_method' => 'option',
                'validate'    => null
            );
            return $defaults;
        }

        /**
         * les paramètres par défaut pour les textarea
         *
         * @return array
         **/
        public function default_textarea_args()
        {

            $defaults = array_merge(
                $this->default_args(),
                array(
                    'type' => 'textarea',
                    'rows' => 5,
                    'cols' => 50,
                )
            );
            return $defaults;
        }

        /**
         * les paramètres par défaut pour les number
         *
         * @return array
         **/
        public function default_number_args()
        {
            $defaults = array_merge(
                $this->default_args(),
                array(
                    'type'    => 'number',
                    'default' => 0,
                    'min'     => 0,
                    'max'     => 100,
                    'step'    => 1,
                )
            );
            return $defaults;
        }

        /**
         * les paramètres par défaut pour les range
         *
         * @return array
         **/
        public function default_range_args()
        {
            $defaults = array_merge(
                $this->default_args(),
                array(
                    'type'    => 'range',
                    'default' => 0,
                    'min'     => 0,
                    'max'     => 100,
                    'step'    => 1,
                )
            );
            return $defaults;
        }

        /**
         * les paramètres par défaut pour les group
         *
         * @return array
         **/
        public function default_group_args()
        {
            $defaults = array_merge(
                $this->default_args(),
                array(
                    'type'   => 'group',
                    'fields' => array(),
                )
            );
            return $defaults;
        }

        /**
         * les paramètres par défaut pour les color
         *
         * @return array
         **/
        public function default_color_args()
        {

            $defaults = array_merge(
                $this->default_args(),
                array(
                    'type'    => 'color',
                    'default' => '#b4d455',
                    'input'   => true,
                    'multi'   => true,
                )
            );
            return $defaults;
        }

        /**
         * les paramètres par défaut pour les select
         *
         * @return array
         **/
        public function default_select_args()
        {
            $defaults = array_merge(
                $this->default_args(),
                array(
                    'type'    => 'select',
                    'default' => 'Choisir...',
                    'options' => array(
                        0 => 'default'
                    )
                )
            );
            return $defaults;
        }

        /**
         * les paramètres par défaut pour les media
         *
         * @return array
         **/
        public function default_media_args()
        {
            $defaults = array_merge(
                $this->default_args(),
                array(
                    'type'    => 'media',
                    'default' => 0,
                    'add_image' => __('Set custom image', $this->theme_domain()),
                    'remove_image' => __('Remove this image', $this->theme_domain()),
                )
            );
            return $defaults;
        }

        /**
         * les paramètres par défaut pour les custom
         *
         * @return array
         **/
        public function default_custom_args()
        {
            $defaults = array_merge(
                $this->default_args(),
                array(
                    'type'     => 'custom',
                    'view'     => array(
                        'html' => 'field-text',
                    ),
                    'validate' => array($this, 'validate_text'),
                )
            );
            return $defaults;
        }

        /**
         * List of allowed field types
         *
         * @return array $allowed_field_types
         **/
        public function allowed_field_types()
        {
            return $allowed_field_types = array(
                'text',
                'textarea',
                'number',
                'range',
                'select',
                'radio',
                'checkbox',
                'wpeditor',
                'color',
                'icon',
                'media',
                'group',
                'custom',
            );
        }

        /**
         * Merge default args
         *
         * @param array $args
         * @return array
         **/
        public function merge_default_args($args)
        {
            $type = isset($args['type']) ? $args['type'] : 'custom';
            $default_args_fun = 'default_' . $type . '_args';
            if (method_exists($this, $default_args_fun)) {
                $defaults = $this->{'default_' . $type . '_args'}();
            } else {
                $defaults = $this->default_args();
            }

            return $array = array_merge($defaults, $args);
        }

        /**
         * Adding fields
         * @param array $array options for the field to add
         * @param string $tab tab for which the field is
         */
        public function add_field($array, $tab = 'general')
        {
            // Détection du champ/field autorisé
            $type = isset($array['type']) ? $array['type'] : 'custom';
            $allowed_field_types = $this->allowed_field_types();
            // If a type is set that is now allowed, don't add the field
            if (isset($type) && $type != '' && !in_array($type, $allowed_field_types)) {
                trigger_error('Type : ' . $type . ' not allowed');
                return;
            }

            // Arguments combiné avec leur défaut
            // Travail supplémentaire si group
            if ($type === 'group') {
                $array['name'] = $this->make_group_name();
                foreach ($array['fields'] as $key => $field) {
                    $array['fields'][$key] = $this->merge_default_args($field);
                }
            } else {
                $array = $this->merge_default_args($array);
            }

            foreach ($this->fields as $tabs) {
                if ($type === 'group') {
                    foreach ($array['fields'] as $key => $field) {
                        if (isset($tabs[$field['name']])) {
                            trigger_error('There is already a field with name ' . $field['name']);
                            return;
                        }
                    }
                } else {
                    if (isset($tabs[$array['name']])) {
                        trigger_error('There is already a field with name ' . $array['name']);
                        return;
                    }
                }
            }

            // If there are options set, then use the first option as a default value
            if (
                !empty($array['options'])
                && $array['default'] == ''
                && $array['type'] !== 'group'
            ) {
                $array_keys = array_keys($array['options']);
                $array['default'] = $array_keys[0];
            }

            if (!isset($this->fields[$tab])) {
                $this->fields[$tab] = array();
            }

            $this->fields[$tab][$array['name']] = $array;
        }

        /**
         * Make name for group
         *
         * @return string group name
         **/
        private function make_group_name()
        {
            $cpt = 0;
            foreach ($this->fields as $tab_slug => $fields) {
                foreach ($fields as $slug => $otpions) {
                    if (strpos($slug, 'group') !== false) {
                        $cpt++;
                    }
                }
            }
            return 'group-' . $cpt;
        }

        /**
         * Adding tab
         * @param array $array options
         */
        public function add_tab($array)
        {

            $defaults = array(
                'slug' => '',
                'title' => ''
            );

            $array = array_merge($defaults, $array);

            if ($array['slug'] == '' || $array['title'] == '') {
                return;
            }

            $this->tabs[$array['slug']] = $array['title'];
        }

        /**
         * Require template view
         *
         * @param string $file Nom du fichier à inclure
         * @return void
         * 
         **/
        public function get_template_view($file = null, $folder = '')
        {
            if (empty($file)) {
                return false;
            }

            $helper = LSC_Helper::getInstance();
            $absPath = $helper->get_abs_path();
            $templatesDir = $absPath . 'admin/settings/html/';

            $pathInfo = pathinfo($file);
            if (!isset($pathInfo['extension'])) {
                $file = $file . '.php';
            }

            // Custom folder for templates
            if (!empty($template_folder)) {
                require $template_folder . $file;
            }else{
                require $templatesDir . $file;
            }

        }

        /**
         * Rendering fields
         * @param  string $tab slug of tab
         * @return void
         */
        public function render_rows($tab)
        {
            if (!isset($this->fields[$tab])) {

                echo '<p>' . __('There are no settings on these page.', 'textdomain') . '</p>';
                return;
            }

            foreach ($this->fields[$tab] as $key => $field) {

                set_query_var(
                    'settingsMenu',
                    array(
                        'menu' => $this,
                        'field' => $field,
                    )
                );

                $this->get_template_view('settings-row');
            }
        }

        /**
         * Render fields
         *
         * @param array $field field type
         * @param mixed $group group name or false
         * @return void
         **/
        public function render_fields($field = array(), $group = false)
        {
            $value = $this->get_option($field['name'])
                ? $this->get_option($field['name'])
                : $field['default'];

            set_query_var(
                'value',
                $value
            );

            // Name utilisé pour les input doit être en tableau pour les groups
            // $field['name'] = $group ? $group . '[' . $field['name'] . ']' : $field['name'];
            set_query_var(
                'field',
                $field
            );

            if (isset($field['view']) && is_array($field['view']) && !empty($field['view'])) {
                foreach ($field['view'] as $folder => $file) {
                    $this->get_template_view($file, $folder);
                    break;
                }
            } else {
                $this->get_template_view('field-' . $field['type']);
            }
        }
    }
}
