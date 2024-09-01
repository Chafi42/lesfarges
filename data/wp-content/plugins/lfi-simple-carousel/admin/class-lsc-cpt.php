<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('LSC_CPT')) {

    /**
     * LSC_CPT
     *
     *  Custom Post types class
     *
     * @since      1.0.0
     * @package    {file-name}
     * @subpackage {file-name}/admin
     *
     */
    class LSC_CPT
    {
        /**
         * @var LSC_CPT
         * @access private
         * @static
         */
        private static $_instance = null;

        /**
         * @var LSC_Helper $helper
         */
        protected $helper;

        /**
         * Hook loader
         *
         * @var LSC_Loader $loader
         */
        protected $loader;

        /**
         * @var array $post_types  
         */
        var $post_types;

        /**
         * @var array $taxonomies  
         */
        var $taxonomies;

        /**
         * Name for slider CPT
         *
         * @since    1.0.0
         * @access   protected
         * @var      string    $name    Name for slider CPT
         */
        protected $name;
        
        /**
         * Slug for slider CPT
         *
         * @since    1.0.0
         * @access   protected
         * @var      string    $slug    Slug for slider CPT
         */
        protected $slug;
        
        /**
         * Méthode qui crée l'unique instance de la classe
         * si elle n'existe pas encore puis la retourne.
         *
         * 
         * @return LSC_CPT
         */
        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new LSC_CPT();
            }
            return self::$_instance;
        }

        /**
         * Constructor
         *
         **/
        private function __construct()
        {
            $this->name = LSC_CPT_NAME;
            $this->slug = LSC_CPT_SLUG;

            $this->helper = LSC_Helper::getInstance();
            $this->loader = new LSC_Loader();
            $this->load_dependencies();

            $this->set_post_type();

            $this->add_hooks();
            $this->run();
        }

        /**
         * Les fichiers requis
         *
         **/
        public function load_dependencies()
        {
            $path = 'admin/posttypes/';
            $dependencies = array(
                'abs' => array(
                    $path . 'class-cpt-api.php',
                    $path . 'class-cpt-api-columns.php',
                    $path . 'class-cpt-api-taxonomy.php',
                )
            );

            $this->helper->require($dependencies);
        }

        /**
         * Default Post types and Taxonomies
         *
         * @since 1.0.0
         * @param string $posttype
         * @package lfi-simple-carousel
         * 
         * @return type
         **/
        public function set_post_type()
        {
            $post_types = array(
                array(
                    'names' => array(
                        'name'     => $this->name,
                        'slug'     => $this->slug,
                        'singular' => 'Carousel',
                        'plural'   => 'Carousels',
                    ),
                    'options' => array(
                        'supports' => array('title'),
                        'has_archive' => true,
                        'menu_icon'   => 'dashicons-format-gallery',
                        'show_in_rest' => false,
                    ),
                )
            );
            $this->add_post_types($post_types);
        }

        /**
         * Ajoute un Post Type
         *
         * @since 1.0.0
         * @package lfi-simple-carousel
         * @param array $post_types
         * @return false|LSC_CPT_API
         * 
         **/
        private function add_post_types($post_types = array())
        {
            if (empty($post_types || !is_array($post_types))) return false;

            foreach ($post_types as $post_type => $args) {

                $options = (isset($args['options'])) ? $args['options'] : array();
                $labels = (isset($args['labels'])) ? $args['labels'] : array();

                if (isset($args['names']) && is_int($post_type)) {
                    $cpt = new LSC_CPT_API($args['names'], $options, $labels);
                } else {
                    $cpt = new LSC_CPT_API($post_type, $options, $labels);
                }

                if (isset($args['taxonomy'])) {
                    foreach ($args['taxonomy'] as $tax) {
                        $cpt->taxonomy($tax);
                    }
                }

                // Columns
                if (isset($args['columns']) && is_array($args['columns'])) {
                    foreach ($args['columns'] as $key => $column) {
                        if (!isset($column['slug']) || empty($column['slug'])) continue;
                        $slug = $column['slug'];
                        $label = isset($column['label']) ? $column['label'] : $slug;
                        $cpt->columns()->add(array($slug => $label));

                        $sortable = isset($column['sortable']) ? $column['sortable'] : true;
                        if ($sortable) {
                            $cpt->columns()->sortable(array($slug => array($slug, $sortable)));
                        }
                        $callback = isset($column['callback']) ? $column['callback'] : false;
                        if ($callback) {
                            $cpt->columns()->populate($slug, $callback);
                        }
                        $order = isset($column['order']) ? $column['order'] : false;
                        if ($order) {
                            $cpt->columns()->order(array($slug => $order));
                        }
                    }
                }

                $cpt->register();
            }
        }

        /**
         * Ajoute une taxonomie
         * 
         * @since      1.0.0
         * @package    lfi-simple-carousel
         * @subpackage lfi-simple-carousel/admin
         * 
         * @param array $taxonomies
         * @return false|LSC_CPT_API_Taxonomy
         * 
         **/
        private function add_taxonomies($taxonomies = array())
        {
            if (empty($taxonomies) || !is_array($taxonomies)) return false;

            foreach ($taxonomies as $key => $args) {

                if (!isset($args['names'])) {
                    continue;
                }

                $options = (isset($args['options'])) ? $args['options'] : array();
                $labels = (isset($args['labels'])) ? $args['labels'] : array();

                $cptTaxo = new LSC_CPT_API_Taxonomy($args['names'], $options, $labels);

                if (isset($args['posttype'])) {
                    $cptTaxo->posttype($args['posttype']);
                    if (isset($args['filters'])) {
                        $postType = new LSC_CPT_API($args['posttype']);
                        $postType->filters($args['filters']);
                        $postType->register();
                    }
                }

                if (isset($args['terms'])) {
                    $cptTaxo->terms($args['terms']);
                }

                $cptTaxo->register();
                return $cptTaxo;
            }
        }

        /**
         * Register all of the hooks related to post-type area functionality
         * of the plugin.
         *
         * @since      1.0.0
         * @package    lfi-simple-carousel
         * @subpackage lfi-simple-carousel/admin
         * 
         * @return void
         */
        public function add_hooks()
        {

            // Filters whether an already registered post-type 
            // is able to be edited in the block editor (gutenberg).
            $this->loader->add_action(
                'use_block_editor_for_post_type',
                $this,
                'post_types_block_editor'
            );

            // Remove Page support for already registered post-type
            // Ideal for removing "editor"
            $this->loader->add_action(
                'init',
                $this,
                'post_type_supports'
            );
        }

        /**
         * Disable Gutenberg Editor
         *
         * @since      1.0.0
         * @package    lfi-simple-carousel
         * @subpackage lfi-simple-carousel/admin
         * 
         * @param bool   $use_block_editor Whether the post type can be edited or not. Default true.
         * @param string $post_type The post type being checked.
         * @return bool
         * 
         **/
        public function post_types_block_editor($use_block_editor)
        {
            // Get the post ID on edit post with filter_input super global inspection.
            $current_post_id = filter_input(INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT);
            // Get the post ID on update post with filter_input super global inspection.
            $update_post_id = filter_input(INPUT_POST, 'post_ID', FILTER_SANITIZE_NUMBER_INT);

            // Check to see if the post ID is set, else return.
            if (isset($current_post_id)) {
                $post_id = absint($current_post_id);
            } else if (isset($update_post_id)) {
                $post_id = absint($update_post_id);
            }

            // Don't do anything unless there is a post_id.
            if (isset($post_id)) {

                $remove = apply_filters('theme_remove_page_gutenberg', array());

                if (isset($remove['templates'])) {
                    foreach ($remove['templates'] as $template) {
                        if (get_page_template_slug($post_id) === $template) {
                            return false;
                        }
                    }
                }

                if (
                    isset($remove['front_page'])
                    && $remove['front_page']
                    && get_option('page_on_front') == $post_id
                ) {
                    return false;
                }

                if (isset($remove['post_types'])) {
                    foreach ($remove['post_types'] as $post_type) {
                        if (get_post_type($post_id) === $post_type)
                            return false;
                    }
                }
            }

            return $use_block_editor;
        }

        /**
         * Remove support for certain post-types
         *
         * @since      1.0.0
         * @package    lfi-simple-carousel
         * @subpackage lfi-simple-carousel/admin
         *
         * @param Type $var Description
         * @return type
         * @throws conditon
         **/
        public static function post_type_supports()
        {
            // If not in the admin, return.
            if (!is_admin()) {
                return;
            }

            // Get the post ID on edit post with filter_input super global inspection.
            $current_post_id = filter_input(INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT);
            // Get the post ID on update post with filter_input super global inspection.
            $update_post_id = filter_input(INPUT_POST, 'post_ID', FILTER_SANITIZE_NUMBER_INT);

            // Check to see if the post ID is set, else return.
            if (isset($current_post_id)) {
                $post_id = absint($current_post_id);
            } else if (isset($update_post_id)) {
                $post_id = absint($update_post_id);
            } else {
                return;
            }
            // Don't do anything unless there is a post_id.
            if (isset($post_id)) {

                $remove = apply_filters('theme_remove_page_supports', array());

                if (isset($remove['templates']) && is_array($remove['templates'])) {
                    foreach ($remove['templates'] as $template => $supports) {
                        if (get_page_template_slug($post_id) === $template) {
                            if (is_array($supports)) {
                                foreach ($supports as $support) {
                                    remove_post_type_support('page', $support);
                                }
                            }
                        }
                    }
                }

                if (
                    isset($remove['front_page'])
                    && is_array($remove['front_page'])
                    && get_option('page_on_front') == $post_id
                ) {
                    foreach ($remove['front_page'] as $support) {
                        remove_post_type_support('page', $support);
                    }
                }

                if (isset($remove['post_type']) && is_array($remove['post_type'])) {
                    foreach ($remove['post_type'] as $post_type => $features) {
                        if (!is_array($features)) {
                            remove_post_type_support($post_type, $features);
                        } else {
                            foreach ($features as $feature) {
                                remove_post_type_support($post_type, $features);
                            }
                        }
                    }
                }
            }
        }


        /**
         * Run the loader to execute all of the hooks with WordPress.
         *
         * @since       1.0.0
         * @package     lfi-simple-carousel
         * @subpackage  lfi-simple-carousel/admin
         */
        public function run()
        {
            $this->loader->run();
        }
    }
}
