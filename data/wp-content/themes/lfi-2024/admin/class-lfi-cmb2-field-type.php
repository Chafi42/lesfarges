<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('LFI_CMB2_Field_Type')) {

    /**
     * LFI_CMB2_Field_Type
     *
     *  Description
     *
     * @since   1.0.0
     * @package lfi-2022
     *
     */
    class LFI_CMB2_Field_Type
    {
        /**
         * @var LFI_CMB2_Field_Type
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
         * CMB2_Field object
         *
         * @var CMB2_Field
         */
        protected $field;

        /**
         * Nav menu item input name prefix
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $prefix Prefix
         */
        private $prefix;


        /**
         * Méthode qui crée l'unique instance de la classe
         * si elle n'existe pas encore puis la retourne.
         *
         * 
         * @return LFI_CMB2_Field_Type
         */
        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new LFI_CMB2_Field_Type();
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
         * Add hooks to loader
         *
         * @return void
         * @throws conditon
         **/
        public function add_hooks()
        {
            $actions = array(
                array(
                    'cmb2_render_post_link',
                    $this,
                    'render_field_post_link',
                    10,
                    5
                ),
                array(
                    'cmb2_sanitize_post_link',
                    $this,
                    'sanitize_field_post_link',
                    10,
                    5
                ),
                // array(
                //     'admin_enqueue_scripts',
                //     $this,
                //     'enqueue_scripts'
                // ),
                array(
                    'wp_ajax_post_link',
                    $this,
                    'field_post_link_ajax_handler'
                ),
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
         * undocumented function summary
         *
         * Undocumented function long description
         *
         * @param string $hook
         * @return void
         **/
        public function enqueue_scripts()
        {
            // $hooks = array(
            //     'post.php',
            //     'post-new.php',
            //     'page-new.php',
            //     'page.php',
            //     'comment.php',
            //     'edit-tags.php',
            //     'term.php',
            //     'user-new.php',
            //     'profile.php',
            //     'user-edit.php',
            // );
            // // only pre-enqueue our scripts/styles on the proper pages
            // // show_form_for_type will have us covered if we miss something here.
            // if (in_array($hook, $hooks, true)) {
            // }
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
            // wp_localize_script(
            //     'lfi-cmb2-field-type',
            //     'ajax_post_link',
            //     array(
            //         'posts_list' => $this->get_objects(),
            //         'ajax_url' => admin_url('admin-ajax.php'),
            //     )
            // );
        }

        /**
         * Ajax handler for Field type post_link
         *
         * @return json
         **/
        public function field_post_link_ajax_handler()
        {
            $post_id = intval($_POST['post_id']);
            wp_send_json(get_permalink($post_id));
        }

        /**
         * Retreive Objects based on post_types option
         *
         * @return array
         **/
        private function get_objects()
        {
            $post_types = $this->field->options('post_types');
            if (!is_array($post_types)) {
                $post_types = array('post', 'page');
            }
            $list = array();
            foreach ($post_types as $key => $post_type) {
                if ($this->field->options('archives')) {
                    $post_type_obj = get_post_type_object($post_type);
                    $list[] = array(
                        'label' => $post_type_obj->labels->name . ' - Page "racine/archive"',
                        'value' => get_post_type_archive_link($post_type),
                    );
                }
                $query = new WP_Query(array(
                    'post_type' => $post_type,
                    'posts_per_page' => -1,
                    'fields' => 'ids'
                ));
                if ($query->have_posts()) { // If there are any custom public post types.
                    foreach ($query->posts as $key => $post_id) {
                        $list[] = array(
                            'label' => get_the_title($post_id),
                            'value' => get_permalink($post_id),
                        );
                    }
                }
                // Taxonomies
                $taxonomies = get_object_taxonomies($post_type, 'objects');
                foreach ($taxonomies as $key => $taxonomy) {
                    $terms = get_terms($taxonomy->name, array(
                        'hide_empty' => true,
                    ));
                    foreach ($terms as $key => $term) {
                        $list[] = array(
                            'label' => $term->name . ' - ' . $taxonomy->label,
                            'value' => get_term_link($term)
                        );
                    }
                }
            }
            return $list;
        }

        /**
         * Render Field post_link
         *
         * @param CMB2_Field  $field The passed in `CMB2_Field` object
         * @param mixed       $escaped_value      The value of this field escaped.
         *                                        It defaults to `sanitize_text_field`.
         *                                        If you need the unescaped value, you can access it
         *                                        via `$field->value()`
         * @param int         $object_id          The ID of the current object
         * @param string      $object_type        The type of object you are working with.
         *                                        Most commonly, `post` (this applies to all post-types),
         *                                        but could also be `comment`, `user` or `options-page`.
         * @param CMB2_Types  $field_type_object  This `CMB2_Types` object
         * 
         * @return string
         **/
        public function render_field_post_link($field, $escaped_value, $object_id, $object_type, $field_type_object)
        {
            $this->field = $field;
            $this->enqueue_scripts();

            $fieldArgs = $field->args();
            $name = $fieldArgs['_name'];
            $id = $fieldArgs['id'];
            $desc = $fieldArgs['desc'];
            $label = isset($escaped_value['label']) ? $escaped_value['label'] : '';
            $value = isset($escaped_value['value']) ? $escaped_value['value'] : '';
            $data_source = "data-source='" . wp_json_encode($this->get_objects()) . "'";
            ?>
            <span class="delete dashicons dashicons-trash"></span>
            <input type="text" class="post-link" value="<?= $label ?>" <?= $data_source ?>><span class="post-link-value"><?= $value ?></span>
            <p class="cmb2-metabox-description"><?= $desc ?></p>
            <input type="hidden" class="post-link-label" name="<?= $name ?>[label]" id="<?= $id ?>_label" value="<?= $label ?>">
            <input type="hidden" class="post-link-value" name="<?= $name ?>[value]" id="<?= $id ?>_value" value="<?= $value ?>">
            <?php
        }

        /**
         * Sanitize Field post_link
         *
         * @param bool|mixed $override_value Sanitization/Validation override value to return.
         *                                   Default: null. false to skip it.
         * @param mixed      $value      The value to be saved to this field.
         * @param int        $object_id  The ID of the object where the value will be saved
         * @param array      $field_args The current field's arguments
         * @param object     $sanitizer  This `CMB2_Sanitize` object
         * @return string
         **/
        public function sanitize_field_post_link($override_value, $value, $object_id, $field_args, $sanitizer_object)
        {
        }
       
    }
}
