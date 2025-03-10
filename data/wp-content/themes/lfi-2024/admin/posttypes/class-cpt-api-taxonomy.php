<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('CPT_API_Taxonomy')) {

    /**
     * CPT_API_Taxonomy
     *
     * Create WordPress Taxonomies easily
     *
     * @link    https://github.com/jjgrainger/PostTypes/
     * @author  jjgrainger
     * @link    https://jjgrainger.co.uk
     * @version 2.0
     * @license https://opensource.org/licenses/mit-license.html MIT License
     */
    class CPT_API_Taxonomy
    {
        /**
         * The names passed to the Taxonomy
         * @var mixed
         */
        public $names;

        /**
         * The Taxonomy name
         * @var string
         */
        public $name;

        /**
         * The singular label for the Taxonomy
         * @var string
         */
        public $singular;

        /**
         * The plural label for the Taxonomy
         * @var string
         */
        public $plural;

        /**
         * The Taxonomy slug
         * @var string
         */
        public $slug;

        /**
         * Custom options for the Taxonomy
         * @var array
         */
        public $options;

        /**
         * Custom labels for the Taxonomy
         * @var array
         */
        public $labels;

        /**
         * PostTypes to register the Taxonomy to
         * @var array
         */
        public $posttypes = [];

        /**
         * Terms to register to the Taxonomy
         * @var array
         */
        public $terms = [];

        /**
         * The column manager for the Taxonomy
         * @var mixed
         */
        public $columns;

        /**
         * Create a Taxonomy
         * @param mixed $names The name(s) for the Taxonomy
         */
        public function __construct($names, $options = [], $labels = [])
        {
            $this->names($names);

            $this->options($options);

            $this->labels($labels);
        }

        /**
         * Set the names for the Taxonomy
         * @param  mixed $names The name(s) for the Taxonomy
         * @return $this
         */
        public function names($names)
        {
            if (is_string($names)) {
                $names = ['name' => $names];
            }

            $this->names = $names;

            // create names for the Taxonomy
            $this->createNames();

            return $this;
        }

        /**
         * Set options for the Taxonomy
         * @param  array $options
         * @return $this
         */
        public function options(array $options = [])
        {
            $this->options = $options;

            return $this;
        }

        /**
         * Set the Taxonomy labels
         * @param  array  $labels
         * @return $this
         */
        public function labels(array $labels = [])
        {
            $this->labels = $labels;

            return $this;
        }

        /**
         * Assign a PostType to register the Taxonomy to
         * @param  string $posttype
         * @return $this
         */
        public function posttype($posttype)
        {
            $this->posttypes[] = $posttype;

            return $this;
        }

        /**
         * Assign a PostType to register the Taxonomy to
         * @param  string $posttype
         * @return $this
         */
        public function terms($terms = array())
        {
            $this->terms = $terms;

            return $this;
        }

        /**
         * Recherche si un term est défini comme term par défaut
         *
         * @return mixed $term_name|false
         **/
        public function is_default_term()
        {

            $default = false;
            if (isset($this->terms)) {
                foreach ($this->terms as $key => $term) {
                    if (isset($term['default']) && $term['default']) {
                        $default = $term['args']['slug'];
                        break;
                    } else {
                        $default = false;
                    }
                }
            }

            return $default;
        }

        /**
         * Get the Column Manager for the Taxonomy
         * @return CPT_API_Cols
         */
        public function columns()
        {
            if (!isset($this->columns)) {
                $this->columns = new CPT_API_Cols;
            }

            return $this->columns;
        }

        /**
         * Register the Taxonomy to WordPress
         * @return void
         */
        public function register()
        {
            // register the taxonomy, set priority to 9
            // so taxonomies are registered before PostTypes
            add_action('init', [&$this, 'registerTaxonomy'], 9);

            // assign taxonomy to post type objects
            add_action('init', [&$this, 'registerTaxonomyToObjects']);

            if (isset($this->columns)) {
                // modify the columns for the Taxonomy
                add_filter("manage_edit-{$this->name}_columns", [&$this, 'modifyColumns']);

                // populate the columns for the Taxonomy
                add_filter("manage_{$this->name}_custom_column", [&$this, 'populateColumns'], 10, 3);

                // set custom sortable columns
                add_filter("manage_edit-{$this->name}_sortable_columns", [&$this, 'setSortableColumns']);

                // run action that sorts columns on request
                add_action('parse_term_query', [&$this, 'sortSortableColumns']);
            }

            if (
                isset($this->terms)
                && $this->is_default_term()
            ) {
                // Action à la sauvegrade du post
                add_action(
                    'save_post',
                    array($this, 'default_term'),
                    10,
                    3
                );
            }
        }

        /**
         * Register the Taxonomy to WordPress
         * @return void
         */
        public function registerTaxonomy()
        {
            if (!taxonomy_exists($this->name)) {
                // create options for the Taxonomy
                $options = $this->createOptions();

                // register the Taxonomy with WordPress
                register_taxonomy($this->name, null, $options);
            }

            if (isset($this->terms)) {
                $this->add_taxonomy_terms();
            }
        }

        /**
         * Enrefistre les term pour une certaine taxonomie
         * @param  array $terms
         * @return $this
         **/
        public function add_taxonomy_terms()
        {

            /**
             * Filters array of terms
             *
             * @since 0.0.1
             *
             * @param array $terms list of terms and their arguments
             */
            $terms = apply_filters('lfi_extras_add_taxonomy_terms', $this->terms);

            if (is_array($terms)) {
                foreach ($terms as $key => $term) {

                    if (
                        (!isset($term['name']) || empty($term['name']))
                    ) {
                        continue;
                    }

                    $term['args'] = isset($term['args']) ? $term['args'] : array();

                    if (!term_exists($term['name'], $this->name)) {
                        wp_insert_term(
                            $term['name'], // the term 
                            $this->name, // the taxonomy
                            $term['args']
                        );
                    }
                }
            }
        }

        /**
         * Register the Taxonomy to PostTypes
         * @return void
         */
        public function registerTaxonomyToObjects()
        {
            // register Taxonomy to each of the PostTypes assigned
            if (!empty($this->posttypes)) {
                foreach ($this->posttypes as $posttype) {
                    register_taxonomy_for_object_type($this->name, $posttype);
                }
            }
        }

        /**
         * Create names for the Taxonomy
         * @return void
         */
        public function createNames()
        {
            $required = [
                'name',
                'singular',
                'plural',
                'slug',
            ];

            foreach ($required as $key) {
                // if the name is set, assign it
                if (isset($this->names[$key])) {
                    $this->$key = $this->names[$key];
                    continue;
                }

                // if the key is not set and is singular or plural
                if (in_array($key, ['singular', 'plural'])) {
                    // create a human friendly name
                    $name = ucwords(strtolower(str_replace(['-', '_'], ' ', $this->names['name'])));
                }

                if ($key === 'slug') {
                    // create a slug friendly name
                    $name = strtolower(str_replace([' ', '_'], '-', $this->names['name']));
                }

                // if is plural or slug, append an 's'
                if (in_array($key, ['plural', 'slug'])) {
                    $name .= 's';
                }

                // asign the name to the PostType property
                $this->$key = $name;
            }
        }

        /**
         * Create options for Taxonomy
         * @return array Options to pass to register_taxonomy
         */
        public function createOptions()
        {
            // default options
            $options = [
                'hierarchical' => true,
                'show_admin_column' => true,
                'show_in_rest' => true,
                'rewrite' => [
                    'slug' => $this->slug,
                ],
            ];

            // replace defaults with the options passed
            $options = array_replace_recursive($options, $this->options);

            // create and set labels
            if (!isset($options['labels'])) {
                $options['labels'] = $this->createLabels();
            }

            return $options;
        }

        /**
         * Create labels for the Taxonomy
         * @return array
         */
        public function createLabels()
        {
            // default labels
            $labels = [
                'name' => $this->plural,
                'singular_name' => $this->singular,
                'menu_name' => $this->plural,
                'all_items' => "All {$this->plural}",
                'edit_item' => "Edit {$this->singular}",
                'view_item' => "View {$this->singular}",
                'update_item' => "Update {$this->singular}",
                'add_new_item' => "Add New {$this->singular}",
                'new_item_name' => "New {$this->singular} Name",
                'parent_item' => "Parent {$this->plural}",
                'parent_item_colon' => "Parent {$this->plural}:",
                'search_items' => "Search {$this->plural}",
                'popular_items' => "Popular {$this->plural}",
                'separate_items_with_commas' => "Seperate {$this->plural} with commas",
                'add_or_remove_items' => "Add or remove {$this->plural}",
                'choose_from_most_used' => "Choose from most used {$this->plural}",
                'not_found' => "No {$this->plural} found",
            ];

            return array_replace($labels, $this->labels);
        }

        /**
         * Modify the columns for the Taxonomy
         * @param  array  $columns  The WordPress default columns
         * @return array
         */
        public function modifyColumns($columns)
        {
            $columns = $this->columns->modifyColumns($columns);

            return $columns;
        }

        /**
         * Populate custom columns for the Taxonomy
         * @param  string $content
         * @param  string $column
         * @param  int    $term_id
         */
        public function populateColumns($content, $column, $term_id)
        {
            if (isset($this->columns->populate[$column])) {
                $content = call_user_func_array($this->columns()->populate[$column], [$content, $column, $term_id]);
            }

            return $content;
        }

        /**
         * Make custom columns sortable
         * @param array $columns Default WordPress sortable columns
         */
        public function setSortableColumns($columns)
        {
            if (!empty($this->columns()->sortable)) {
                $columns = array_merge($columns, $this->columns()->sortable);
            }

            return $columns;
        }

        /**
         * Set query to sort custom columns
         * @param WP_Term_Query $query
         */
        public function sortSortableColumns($query)
        {
            // don't modify the query if we're not in the post type admin
            if (!is_admin() || !in_array($this->name, $query->query_vars['taxonomy'])) {
                return;
            }

            // check the orderby is a custom ordering
            if (isset($_GET['orderby']) && array_key_exists($_GET['orderby'], $this->columns()->sortable)) {
                // get the custom sorting options
                $meta = $this->columns()->sortable[$_GET['orderby']];

                // check ordering is not numeric
                if (is_string($meta)) {
                    $meta_key = $meta;
                    $orderby = 'meta_value';
                } else {
                    $meta_key = $meta[0];
                    $orderby = 'meta_value_num';
                }

                // set the sort order
                $query->query_vars['orderby'] = $orderby;
                $query->query_vars['meta_key'] = $meta_key;
            }
        }

        /**
         * Se lance à la sauvegarde du post 
         *
         * @since 0.0.1
         * 
         * @param int $post_ID
         * @param WP_Post $post
         * @param bool $update
         *
         * @return void
         **/
        public function default_term($post_ID, $post, $update)
        {
            if (
                'publish' === $post->post_status
                && in_array($post->post_type, $this->posttypes)
            ) {
                $terms = wp_get_post_terms($post_ID, $this->name);
                if (empty($terms)) {
                    wp_set_object_terms(
                        $post_ID,
                        $this->is_default_term(),
                        $this->name
                    );
                }
            }
        }
    }
}
