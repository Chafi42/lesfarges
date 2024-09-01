<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Settings_API_Sub_Menu')) {

    /**
     * Settings_API_Sub_Menu
     *
     *  Ajoute le sous menu au menu
     *
     * @since 1.0.0
     * @package {file_name}
     *
     */

    class Settings_API_Sub_Menu extends Settings_API_Menu
    {

        function __construct($options, $parent)
        {
            parent::__construct($options);
            if ($parent instanceof Settings_API) {
                $this->parent_id = $parent->menu_slug;
            }else{
                $this->parent_id = $parent;
            }
        }
    }
}
