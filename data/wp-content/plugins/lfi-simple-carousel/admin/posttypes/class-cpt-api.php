<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('LSC_CPT_API')) {

    /**
     * LSC_CPT_API
     *
     * Create WordPress custom post types easily
     *
     * @link    https://github.com/jjgrainger/PostTypes/
     * @author  jjgrainger
     * @link    https://jjgrainger.co.uk
     * @version 2.0
     * @license https://opensource.org/licenses/mit-license.html MIT License
     */
    class LSC_CPT_API
    {
        /**
         * The names passed to the LSC_CPT_API
         * @var array
         */
        public $names;

        /**
         * The name for the LSC_CPT_API
         * @var array
         */
        public $name;

        /**
         * The singular for the LSC_CPT_API
         * @var array
         */
        public $singular;

        /**
         * The plural name for the LSC_CPT_API
         * @var array
         */
        public $plural;

        /**
         * The slug for the LSC_CPT_API
         * @var array
         */
        public $slug;

        /**
         * Options for the LSC_CPT_API
         * @var array
         */
        public $options;

        /**
         * Labels for the LSC_CPT_API
         * @var array
         */
        public $labels;

        /**
         * Taxonomies for the LSC_CPT_API
         * @var array
         */
        public $taxonomies = [];

        /**
         * Filters for the LSC_CPT_API
         * @var mixed
         */
        public $filters;

        /**
         * The menu icon for the LSC_CPT_API
         * @var string
         */
        public $icon;

        /**
         * The column manager for the LSC_CPT_API
         * @var mixed
         */
        public $columns;

        /**
         * Create a LSC_CPT_API
         * @param mixed $names   A string for the name, or an array of names
         * @param array $options An array of options for the LSC_CPT_API
         */
        public function __construct($names, $options = [], $labels = [])
        {
            // assign names to the LSC_CPT_API
            $this->names($names);

            // assign custom options to the LSC_CPT_API
            $this->options($options);

            // assign labels to the LSC_CPT_API
            $this->labels($labels);
        }

        /**
         * Set the names for the LSC_CPT_API
         * @param  mixed $names A string for the name, or an array of names
         * @return $this
         */
        public function names($names)
        {
            // only the post type name is passed
            if (is_string($names)) {
                $names = ['name' => $names];
            }

            // set the names array
            $this->names = $names;

            // create names for the LSC_CPT_API
            $this->createNames();

            return $this;
        }

        /**
         * Set the options for the LSC_CPT_API
         * @param  array $options An array of options for the LSC_CPT_API
         * @return $this
         */
        public function options(array $options)
        {
            $this->options = $options;

            return $this;
        }

        /**
         * Set the labels for the LSC_CPT_API
         * @param  array $labels An array of labels for the LSC_CPT_API
         * @return $this
         */
        public function labels(array $labels)
        {
            $this->labels = $labels;

            return $this;
        }

        /**
         * Add a Taxonomy to the LSC_CPT_API
         * @param  string $taxonomy The Taxonomy name to add
         * @return $this
         */
        public function taxonomy($taxonomy)
        {
            $this->taxonomies[] = $taxonomy;

            return $this;
        }

        /**
         * Add filters to the LSC_CPT_API
         * @param  array $filters An array of Taxonomy filters
         * @return $this
         */
        public function filters(array $filters)
        {
            $this->filters = $filters;

            return $this;
        }

        /**
         * Set the menu icon for the LSC_CPT_API
         * @param  string $icon A dashicon class for the menu icon
         * @return $this
         */
        public function icon($icon)
        {
            $this->icon = $icon;

            return $this;
        }

        /**
         * Flush rewrite rules
         * @link https://codex.wordpress.org/Function_Reference/flush_rewrite_rules
         * @param  boolean $hard
         * @return void
         */
        public function flush($hard = true)
        {
            flush_rewrite_rules($hard);
        }

        /**
         * Get the Column Manager for the LSC_CPT_API
         * @return LSC_CPT_API_Cols
         */
        public function columns()
        {
            if (!isset($this->columns)) {
                $this->columns = new LSC_CPT_API_Cols;
            }

            return $this->columns;
        }

        /**
         * Register the PostType to WordPress
         * @return void
         */
        public function register()
        {
            // register the LSC_CPT_API
            add_action('init', [&$this, 'registerPostType']);

            // register Taxonomies to the LSC_CPT_API
            add_action('init', [&$this, 'registerTaxonomies']);

            // modify filters on the admin edit screen
            add_action('restrict_manage_posts', [&$this, 'modifyFilters']);

            if (isset($this->columns)) {
                // modify the admin edit columns.
                add_filter("manage_{$this->name}_posts_columns", [&$this, 'modifyColumns'], 10, 1);

                // populate custom columns
                add_filter("manage_{$this->name}_posts_custom_column", [&$this, 'populateColumns'], 10, 2);

                // run filter to make columns sortable.
                add_filter('manage_edit-' . $this->name . '_sortable_columns', [&$this, 'setSortableColumns']);

                // run action that sorts columns on request.
                add_action('pre_get_posts', [&$this, 'sortSortableColumns']);
            }
        }

        /**
         * Register the LSC_CPT_API
         * @return void
         */
        public function registerPostType()
        {
            // create options for the LSC_CPT_API
            $options = $this->createOptions();

            // check that the post type doesn't already exist
            if (!post_type_exists($this->name)) {
                // register the post type
                register_post_type($this->name, $options);
            }
        }

        /**
         * Create the required names for the LSC_CPT_API
         * @return void
         */
        public function createNames()
        {
            // names required for the LSC_CPT_API
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
         * Create options for LSC_CPT_API
         * @return array Options to pass to register_post_type
         */
        public function createOptions()
        {
            // default options
            $options = [
                'public' => true,
                'rewrite' => [
                    'slug' => $this->slug
                ]
            ];

            // replace defaults with the options passed
            $options = array_replace_recursive($options, $this->options);

            // create and set labels
            if (!isset($options['labels'])) {
                $options['labels'] = $this->createLabels();
            }

            // set the menu icon
            if (!isset($options['menu_icon']) && isset($this->icon)) {
                $options['menu_icon'] = $this->icon;
            }

            return $options;
        }

        /**
         * Create the labels for the LSC_CPT_API
         * @return array
         */
        public function createLabels()
        {
            // default labels
            $labels = [
                'name' => $this->plural,
                'singular_name' => $this->singular,
                'menu_name' => $this->plural,
                'all_items' => $this->plural,
                'add_new' => "Add New",
                'add_new_item' => "Add New {$this->singular}",
                'edit_item' => "Edit {$this->singular}",
                'new_item' => "New {$this->singular}",
                'view_item' => "View {$this->singular}",
                'search_items' => "Search {$this->plural}",
                'not_found' => "No {$this->plural} found",
                'not_found_in_trash' => "No {$this->plural} found in Trash",
                'parent_item_colon' => "Parent {$this->singular}:",
            ];

            return array_replace_recursive($labels, $this->labels);
        }

        /**
         * Register Taxonomies to the LSC_CPT_API
         * @return void
         */
        public function registerTaxonomies()
        {
            if (!empty($this->taxonomies)) {
                foreach ($this->taxonomies as $taxonomy) {
                    register_taxonomy_for_object_type($taxonomy, $this->name);
                }
            }
        }

        /**
         * Modify and display filters on the admin edit screen
         * @param  string $posttype The current screen post type
         * @return void
         */
        public function modifyFilters($posttype)
        {
            // first check we are working with the this LSC_CPT_API
            if ($posttype === $this->name) {
                // calculate what filters to add
                $filters = $this->getFilters();

                foreach ($filters as $taxonomy) {
                    // if the taxonomy doesn't exist, ignore it
                    if (!taxonomy_exists($taxonomy)) {
                        continue;
                    }

                    // get the taxonomy object
                    $tax = get_taxonomy($taxonomy);

                    // get the terms for the taxonomy
                    $terms = get_terms([
                        'taxonomy' => $taxonomy,
                        'orderby' => 'name',
                        'hide_empty' => false,
                    ]);

                    // if there are no terms in the taxonomy, ignore it
                    if (empty($terms)) {
                        continue;
                    }

                    // start the html for the filter dropdown
                    $selected = null;

                    if (isset($_GET[$taxonomy])) {
                        $selected = sanitize_title($_GET[$taxonomy]);
                    }

                    $dropdown_args = [
                        'option_none_value' => '',
                        'hide_empty'        => 0,
                        'hide_if_empty'     => false,
                        'show_count'        => true,
                        'taxonomy'          => $tax->name,
                        'name'              => $taxonomy,
                        'orderby'           => 'name',
                        'hierarchical'      => true,
                        'show_option_none'  => "Show all {$tax->label}",
                        'value_field'       => 'slug',
                        'selected'          => $selected
                    ];

                    wp_dropdown_categories($dropdown_args);
                }
            }
        }

        /**
         * Calculate the filters for the LSC_CPT_API
         * @return array
         */
        public function getFilters()
        {
            // default filters are empty
            $filters = [];

            // if custom filters have been set, use them
            if (!is_null($this->filters)) {
                return $this->filters;
            }

            // if no custom filters have been set, and there are
            // Taxonomies assigned to the LSC_CPT_API
            if (is_null($this->filters) && !empty($this->taxonomies)) {
                // create filters for each taxonomy assigned to the LSC_CPT_API
                return $this->taxonomies;
            }

            return $filters;
        }

        /**
         * Modify the columns for the LSC_CPT_API
         * @param  array  $columns  Default WordPress columns
         * @return array            The modified columns
         */
        public function modifyColumns($columns)
        {
            $columns = $this->columns->modifyColumns($columns);

            return $columns;
        }

        /**
         * Populate custom columns for the LSC_CPT_API
         * @param  string $column   The column slug
         * @param  int    $post_id  The post ID
         */
        public function populateColumns($column, $post_id)
        {
            if (isset($this->columns->populate[$column])) {
                call_user_func_array($this->columns()->populate[$column], [$column, $post_id]);
            }
        }

        /**
         * Make custom columns sortable
         * @param array  $columns  Default WordPress sortable columns
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
         * @param  WP_Query $query
         */
        public function sortSortableColumns($query)
        {
            // don't modify the query if we're not in the post type admin
            if (!is_admin() || $query->get('post_type') !== $this->name) {
                return;
            }

            $orderby = $query->get('orderby');

            // if the sorting a custom column
            if (array_key_exists($orderby, $this->columns()->sortable)) {
                // get the custom column options
                $meta = $this->columns()->sortable[$orderby];

                // determine type of ordering
                if (is_string($meta)) {
                    $meta_key = $meta;
                    $meta_value = 'meta_value';
                } else {
                    $meta_key = $meta[0];
                    $meta_value = 'meta_value_num';
                }

                // set the custom order
                $query->set('meta_key', $meta_key);
                $query->set('orderby', $meta_value);
            }
        }
    }
}
