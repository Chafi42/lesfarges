<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('LFI_Nav_Item_Custom_Fields')) {

    /**
     * LFI_Nav_Item_Custom_Fields
     *
     *  Description
     *
     * @since   1.0.0
     * @package lfi-2022
     *
     */
    class LFI_Nav_Item_Custom_Fields
    {
        /**
         * @var LFI_Nav_Item_Custom_Fields
         * @access private
         * @static
         */
        private static $_instance = null;

        /**
         * LFI_Loader
         *
         * @since    1.0.0
         * @access   private
         * @var      LFI_Loader    $loader    LFI_Loader
         */
        private $loader;

        /**
         * Méthode qui crée l'unique instance de la classe
         * si elle n'existe pas encore puis la retourne.
         *
         * 
         * @return LFI_Nav_Item_Custom_Fields
         */
        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new LFI_Nav_Item_Custom_Fields();
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
            $this->add_hooks();
            $this->loader->run();
        }

        /**
         * Add hooks
         *
         **/
        public function add_hooks()
        {
            $actions = array(
                array(
                    'wp_nav_menu_item_custom_fields',
                    $this,
                    'render_nav_item_custom_fields',
                    10,
                    5
                ),
                array(
                    'wp_update_nav_menu_item',
                    $this,
                    'update_nav_menu_item_custom_fields',
                    10,
                    3
                )
            );

            foreach ($actions as $key => $action) {
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
         * Enqueue Script and Styles
         *
         * @return void
         **/
        public function enqueue_scripts()
        {
            wp_enqueue_style(
                'lfi-cmb2-field-type',
                LFI_Helper()->get_uri_path('admin/css/lfi-cmb2-field-type.css')
            );
            wp_enqueue_script(
                'lfi-cmb2-field-type',
                LFI_Helper()->get_uri_path('admin/js/lfi-cmb2-field-type.js'),
                array('jquery', 'jquery-ui-core', 'jquery-ui-autocomplete'),
                '1.0.0',
                true
            );
        }

        /**
         * Allowed types for menu items custom fields
         *
         * @return array
         **/
        private function allowed_types()
        {
            return array(
                'text',
                'checkbox',
                'file',
            );
        }

        /**
         * Get the meta_key name used for database
         *
         * @return string
         **/
        private function input_field_name($field_id = '')
        {
            return 'menu_item_' . $field_id;
        }

        /**
         * Get list of meta_box registered for nav_menu_item object_type
         *
         * @return array Associative Array of field id and their corresponding input name
         **/
        private function nav_item_meta_boxes()
        {
            $nav_item_meta_boxes = array();
            // Get all meta_boxes registered using CMB2
            $metaBoxes = CMB2_Boxes::get_all();
            foreach ($metaBoxes as $cmb_id => $cmb) {
                // Work only on 'nav_menu_item' type
                if (in_array('nav_menu_item', $cmb->prop('object_types'))) {
                    $nav_item_meta_boxes[$cmb_id] = $cmb;
                }
            }
            return $nav_item_meta_boxes;
        }

        /**
         * Nav Menu Items Custom Fields
         *
         * @param string $item_id Menu item ID as a numeric string.
         * @param WP_Post $menu_item Menu item data object.
         * @param int $depth  Depth of menu item. Used for padding.
         * @param stdClass|null $args An object of menu item arguments.
         * @param int $current_object_id Nav menu ID.
         * 
         * @return void
         * @throws conditon
         **/
        public function render_nav_item_custom_fields($item_id, $menu_item, $depth, $args, $current_object_id)
        {
            $this->enqueue_scripts();
            foreach ($this->nav_item_meta_boxes() as $cmb_id => $cmb) {
                // Get orignal args
                $args = $cmb->meta_box;
                // Set new id for the temporary box, based on nav-item id.
                $args['id'] = $args['id'] . '-' . $item_id;
                foreach ($args['fields'] as $key => $field) {
                    // Limit the type to be added
                    if (!in_array($field['type'], $this->allowed_types())) {
                        unset($args['fields'][$key]);
                        continue;
                    }
                    // Set the id, for the file type
                    // Js script will setup name correctly
                    if ($field['type'] == 'file') {
                        $args['fields'][$key]['id'] = $this->input_field_name($field['id']);
                        // The rest, ex: text type
                    } else {
                        $args['fields'][$key]['id'] = $this->input_field_name($field['id']) . '[' . $item_id . ']';
                    }

                    // Set values from database
                    $db_val = $this->menu_item_cf_value($item_id, $field['id']);
                    if (!empty($db_val)) {
                        $args['fields'][$key]['attributes'] = array(
                            'value' => $this->menu_item_cf_value($item_id, $field['id']),
                        );
                        // Add 'checked' args for checkbox
                        if ($field['type'] == 'checkbox' && $db_val == 'on') {
                            $args['fields'][$key]['attributes']['checked'] = 'checked';
                        }
                    }
                }
                // Create temporary meta box
                $itemCMB = new CMB2($args);
                // Render the meta box and custom field
                $itemCMB->render_form_open();
                foreach ($itemCMB->prop('fields') as $field_args) {
                    // $field_args = $this->set_field_attributes($item_id, $field_args);
                    $itemCMB->render_field($field_args);
                }
                $itemCMB->render_form_close();
                // Delete temporary meta box to avoid creating doublons
                CMB2_Boxes::remove($args['id']);
            }
        }

        /**
         * Get value from database
         *
         * @param int    $menu_item_id
         * @param string $field_id
         * @return mixed
         **/
        public function menu_item_cf_value($menu_item_id = 0, $field_id = '')
        {
            $db_val = get_post_meta($menu_item_id, $this->input_field_name($field_id), true);
            return $db_val;
        }

        /**
         * Update Menu item custom fields
         *
         *
         * @param int   $menu_id         ID of the updated menu.
         * @param int   $menu_item_db_id ID of the updated menu item.
         * @param array $args            An array of arguments used to update a menu item.
         * @return type
         * @throws conditon
         **/
        public function update_nav_menu_item_custom_fields($menu_id, $menu_item_db_id, $args)
        {
            $meta_boxes = $this->nav_item_meta_boxes();
            foreach ($meta_boxes as $cmb_id => $cmb) {
                // Loop through all fields
                foreach ($cmb->meta_box['fields'] as $key => $field) {
                    // Get the meta_key
                    $name = $this->input_field_name($field['id']);
                    $posted_data = isset($_POST[$name][$menu_item_db_id]) ? $_POST[$name][$menu_item_db_id] : false;
                    // Sanitization
                    // File type
                    switch ($field['type']) {
                        case 'file':
                            $posted_data = sanitize_url($posted_data);
                            break;
                        
                        default:
                            $posted_data = sanitize_text_field($posted_data);
                            break;
                    }
                    update_post_meta($menu_item_db_id, $name, $posted_data);
                }
            }
        }
    }
}
