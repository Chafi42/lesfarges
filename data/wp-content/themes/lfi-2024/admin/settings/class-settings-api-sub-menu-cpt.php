<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Settings_API_Sub_Menu_CPT')) {

    /**
     * Settings_API_Sub_Menu_CPT
     *
     *  Ajoute le sous menu au menu du Post type
     *
     * @since 1.0.0
     * @package {file_name}
     *
     */

    class Settings_API_Sub_Menu_CPT extends Settings_API_Menu
    {

        function __construct($options, $parent_slug = '')
        {
            if (empty($parent_slug)) return false;

            parent::__construct($options);

            $this->parent_id = $parent_slug;
        }

        /**
         * Get values from the database
         * @return void
         */
        public function get_values_from_db()
        {
            if (empty($this->settings)) {
                foreach ($this->fields as $tab => $fields) {
                    foreach ($fields as $name => $value) {
                        $this->settings[$name] = get_option($name);
                    }
                }
            }
        }

        /**
         * Save settings from POST
         * @return [type] [description]
         */
        public function save_settings()
        {
            $this->posted_data = $_POST;

            if (empty($this->settings)) {
                $this->get_values_from_db();
            }

            foreach ($this->fields as $tab => $tab_data) {
                // Form POST on tab
                if (isset($this->posted_data['tab']) && $tab === $this->posted_data['tab']) {
                    foreach ($tab_data as $name => $field) {
                        $type = $field['type'];
                        if ($type === 'group') {
                            $validated_val = $this->{'validate_' . $type}($name, $field['fields']);
                        } elseif ($type === 'custom') {
                            $validated_val = $this->{'validate_' . $field['validate']}($name, $empty_val = null);
                        } else {
                            $validated_val = $this->{'validate_' . $type}($name, $empty_val = null);
                        }
                        update_option($field['name'], $validated_val);
                    }
                }
            }
        }
    }
}
