<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('LSC_Settings_API_Sub_Menu')) {

    /**
     * LSC_Settings_API_Sub_Menu
     *
     *  Ajoute le sous menu au menu
     *
     * @since 1.0.0
     * @package lfi-simple-carousel
     *
     */

    class LSC_Settings_API_Sub_Menu extends LSC_Settings_API_Menu
    {

        function __construct($options, $parent)
        {
            parent::__construct($options);
            if ($parent instanceof LSC_Settings_API) {
                $this->parent_id = $parent->menu_slug;
            }else{
                $this->parent_id = $parent;
            }
        }
    }
}
