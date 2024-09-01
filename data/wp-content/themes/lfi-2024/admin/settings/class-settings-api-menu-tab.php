<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Settings_API_Menu_Tab')) {

    /**
     * Settings_API_Menu_Tab
     *
     *  Ajoute les onglets dans la page de paramètres
     *
     * @since 1.0.0
     * @package {file_name}
     *
     */

    class Settings_API_Menu_Tab
    {

        public $slug;

        public $title;

        public $menu;

        function __construct($options, Settings_API_Menu $menu)
        {
            $this->slug = $options['slug'];
            $this->title = $options['title'];
            $this->menu = $menu;
            $this->menu->add_tab($options);
        }

        /**
         * Add field to this tab
         * @param [type] $array [description]
         */
        public function add_field($array)
        {
            $this->menu->add_field($array, $this->slug);
        }
    }
}
