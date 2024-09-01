<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('LSC_Settings_API_Menu_Tab')) {

    /**
     * LSC_Settings_API_Menu_Tab
     *
     *  Ajoute les onglets dans la page de paramètres
     *
     * @since 1.0.0
     * @package lfi-simple-carousel
     *
     */

    class LSC_Settings_API_Menu_Tab
    {

        public $slug;

        public $title;

        public $menu;

        function __construct($options, LSC_Settings_API_Menu $menu)
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
