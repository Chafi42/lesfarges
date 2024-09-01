<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('Bootstrap_Walker_Nav_Menu')) {

    /**
     * Bootstrap_Walker_Nav_Menu
     *
     *  Description
     *
     * @since   1.0.0
     * @package lfi-2022
     *
     */
    class Bootstrap_Walker_Nav_Menu extends Walker_Nav_Menu
    {
        private $current_item;
        private $dropdown_menu_alignment_values = [
            'dropdown-menu-start',
            'dropdown-menu-end',
            'dropdown-menu-sm-start',
            'dropdown-menu-sm-end',
            'dropdown-menu-md-start',
            'dropdown-menu-md-end',
            'dropdown-menu-lg-start',
            'dropdown-menu-lg-end',
            'dropdown-menu-xl-start',
            'dropdown-menu-xl-end',
            'dropdown-menu-xxl-start',
            'dropdown-menu-xxl-end'
        ];

        /**
         * Starts the list before the elements are added.
         *
         * @param string $output  Required Used to append additional content (passed by reference).
         * @param int $depth  Required Depth of menu item. Used for padding.
         * @param stdClass $args  Optional An object of wp_nav_menu() arguments.
         **/
        public function start_lvl(&$output, $depth = 0, $args = null)
        {
            $dropdown_menu_class[] = '';
            foreach ($this->current_item->classes as $class) {
                if (in_array($class, $this->dropdown_menu_alignment_values)) {
                    $dropdown_menu_class[] = $class;
                }
            }
            $indent = str_repeat("\t", $depth);
            $submenu = ($depth > 0) ? ' sub-menu' : '';
            $output .= "\n$indent<ul class=\"dropdown-menu$submenu " . esc_attr(implode(" ", $dropdown_menu_class)) . " depth-$depth\">\n";
        }

        /**
         * Starts the element output.
         *
         * @since 3.0.0
         * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
         * @since 5.9.0 Renamed `$item` to `$data_object` and `$id` to `$current_object_id`
         *              to match parent class for PHP 8 named parameter support.
         *
         * @see Walker::start_el()
         *
         * @param string   $output            Used to append additional content (passed by reference).
         * @param WP_Post  $data_object       Menu item data object.
         * @param int      $depth             Depth of menu item. Used for padding.
         * @param stdClass $args              An object of wp_nav_menu() arguments.
         * @param int      $current_object_id Optional. ID of the current menu item. Default 0.
         */
        public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_object_id = 0)
        {
            $this->current_item = $data_object;
            $split_dropdown = isset($args->split_dropdown) ? $args->split_dropdown : false;

            $indent = ($depth) ? str_repeat("\t", $depth) : '';

            $li_attributes = '';
            $class_names = $value = '';

            $classes = empty($data_object->classes) ? array() : (array) $data_object->classes;
            $classes[] = ($args->walker->has_children) ? 'dropdown' : '';
            $classes[] = 'nav-item';
            $classes[] = 'nav-item-' . $data_object->ID;
            if ($depth && $args->walker->has_children) {
                $classes[] = 'dropdown-menu dropdown-menu-end';
            }

            if (isset($args->item_class) && $depth == 0) {
                $class_names = join(' ', $classes);
                $class_names .= ' ' . $args->item_class;
            } else {
                $class_names =  join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $data_object, $args));
            }
            if ($depth > 0) {
                $class_names .= ' nav-item-children';
            }
            $class_names = ' class="' . esc_attr($class_names) . '"';

            $id = apply_filters('nav_menu_item_id', 'menu-item-' . $data_object->ID, $data_object, $args);
            $id = strlen($id) ? ' id="' . esc_attr($id) . '"' : '';

            $output .= $indent . '<li ' . $id . $value . $class_names . $li_attributes . '>';

            // Title attributes
            if (!get_post_meta($data_object->ID, 'menu_item_disable_title', true)) {
                $attrTitle = !empty($data_object->attr_title) ? $data_object->attr_title : $data_object->title;
            } else {
                $attrTitle = '';
            }


            $queriedObj = get_queried_object();
            $queriedObj = is_object($queriedObj) ? $queriedObj : new stdClass();
            switch (get_class($queriedObj)) {
                case 'WP_Term':
                    $post_type = $queriedObj->slug;
                    break;

                case 'WP_Post_Type':
                    $post_type = $queriedObj->name;
                    break;

                case 'WP_Post':
                    $post_type = $queriedObj->post_type;
                    break;

                case 'WP_User':
                case 'null':
                case 'stdClass':
                    $post_type = '';
                    break;
            }

            $active_class = '';
            if (
                in_array('current-menu-item', $classes, true)
                || in_array(
                    'current-menu-parent',
                    $classes,
                    true
                )
                || $data_object->current_item_ancestor
            ) {
                $active_class = ' active';
            }
            // Is Item Post type Archive page
            if (
                $data_object->type == 'post_type_archive'
                && $data_object->object == $post_type
            ) {
                $active_class = ' active';
            }
            // Is Item Page for Posts
            if (
                get_option('page_for_posts') == $data_object->object_id
                && $post_type == 'post'
            ) {
                $active_class = ' active';
            }

            // <a> tag attributes
            $atts = array(
                'title'  => esc_attr($attrTitle),
                'target' => !empty($data_object->target) ? $data_object->target : '',
                'rel'    => !empty($data_object->xfn) ? $data_object->xfn : '',
                'href'   => !empty($data_object->url) ? esc_attr($data_object->url) : '',
                'class'  => ($depth > 0) ? 'dropdown-item' : 'nav-link',
            );
            // $atts['class'] .= $args->walker->has_children ? ' dropdown-toggle' : '';

            if ($args->walker->has_children && !$split_dropdown) {
                $atts['class'] .= ' dropdown-toggle';
                $atts['data-bs-toggle'] = 'dropdown';
                $atts['aria-haspopup'] = 'true';
                $atts['aria-expanded'] = 'false';
            }
            if ($args->walker->has_children && $split_dropdown) {
                $atts['class'] .= ' d-inline-block';
            }
            $atts['class'] .= $active_class;
            // Loop all attributes
            $attributes = '';
            $atts = apply_filters('nav_menu_item_attributes', $atts, $data_object);
            foreach ($atts as $attr_name => $attr_val) {
                $attributes .= ' ' . $attr_name . '="' . $attr_val . '"';
            }

            $data_object_output = $args->before;
            $data_object_output .= '<a' . $attributes . '>';
            // Title
            $title_output = apply_filters('the_title', $data_object->title, $data_object->ID);
            $data_object_output .= $args->link_before;
            $data_object_output .= apply_filters('nav_menu_item_title_output', $title_output, $data_object);
            $data_object_output .= $args->link_after;
            $data_object_output .= apply_filters('nav_menu_item_before_closing', '', $data_object);
            $data_object_output .= '</a>';
            if ($args->walker->has_children && $split_dropdown) {
                $dp_class = 'dropdown-toggle dropdown-toggle-split d-inline-block ';
                $dp_class .= isset($args->dropdown_class) ? $args->dropdown_class : 'ms-2 px-3';
                $data_object_output .= '<span class="' . $dp_class . '" data-bs-toggle="dropdown"></span>';
            }
            $data_object_output .= $args->after;

            $output .= apply_filters('walker_nav_menu_start_el', $data_object_output, $data_object, $depth, $args);
        }
    }
}
