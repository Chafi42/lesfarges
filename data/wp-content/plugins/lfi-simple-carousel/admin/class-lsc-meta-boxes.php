<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('LSC_Custom_Fields')) {

    /**
     * LSC_Custom_Fields
     *
     *  Description
     *
     * @since      1.0.0
     * @package    moz-2023
     * @subpackage moz-2023/includes
     *
     */
    class LSC_Custom_Fields
    {
        /**
         * @var LSC_Custom_Fields
         * @access private
         * @static
         */
        private static $_instance = null;

        /**
         * Meta-boxes
         *
         * @since    1.0.0
         * @access   private
         * @var      array    $meta_boxes    Meta-boxes
         */
        private $meta_boxes;

        /**
         * Méthode qui crée l'unique instance de la classe
         * si elle n'existe pas encore puis la retourne.
         *
         * 
         * @return LSC_Custom_Fields
         */
        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new LSC_Custom_Fields();
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
            /** 
             * Initiate meta-boxes
             */
            // Project CPT
            $this->carousel_custom_fields();

            // Load meta boxes using action hook
            $this->add_hooks();
        }

        /**
         * Hooks
         *
         **/
        private function add_hooks()
        {
            $loader = new LSC_Loader();
            $loader->add_action(
                'cmb2_admin_init',
                $this,
                'add_custom_fields'
            );

            $loader->run();
        }

        /**
         * Add custom fileds to Project cpt
         *
         * @since      1.0.0
         * @package    moz-2023
         * @subpackage moz-2023/includes
         * 
         * @return void
         * 
         **/
        public function carousel_custom_fields()
        {
            $object_types = array(LSC_CPT_NAME);
            $prefix = '_lsc_';
            // Carousel
            $this->meta_boxes[] = array(
                'meta-box' => array(
                    'id'           => 'slider-info',
                    'title'        => 'Slider',
                    'context'      => 'normal', //  'normal', 'side', and 'advanced'
                    'priority'     => 'high', // 'high' and 'low'
                    'object_types' => $object_types,
                    'classes'      => 'bootstrap'
                ),
                'fields'   => array(
                    /* shortcode */
                    array(
                        'id'            => $prefix . 'shortcode',
                        'desc'          => 'Code court (shortcode) à utiliser pour l\'affichage du carousel.',
                        'type'          => 'title',
                        'render_row_cb' => array($this, 'shortcode_row_cb'),
                        'display_cb'    => array($this, 'shortcode_display_cb'),
                        'column'        => array(
                            'position' => 2,
                            'name'     => 'Shortcode',
                        ),
                        'classes'       => 'col-12 col-md-6'
                    ),
                    /* slug */
                    array(
                        'id'            => $prefix . 'slug',
                        'desc'          => 'Nom du fichier php à utiliser pour le template (sans l\'extension ".php").',
                        'type'          => 'text',
                        // 'render_row_cb' => array($this, 'shortcode_row_cb'),
                        // 'display_cb'    => array($this, 'shortcode_display_cb'),
                        'column'        => array(
                            'position' => 3,
                            'name'     => 'Slug',
                        ),
                        'classes'       => 'col-12 col-md-6'
                    ),
                    /* interval */
                    array(
                        'id'         => $prefix . 'interval',
                        'type'       => 'text',
                        'desc'       => 'En lecture automatique, définir l\'intervalle de temps entre 2 diapos (en ms).<br>Ne change pas si égale à zéro',
                        'default'    => 4000,
                        'attributes' => array(
                            'type' => 'number',
                            'step' => 1,
                            'min' => 0,
                            'style' => 'width:90px;'
                        ),
                        'classes'    => 'col-12 col-md-4'
                    ),
                    /* to_display */
                    array(
                        'id'         => $prefix . 'to_display',
                        'type'       => 'text',
                        'desc'       => 'Nombre de diapos à afficher.',
                        'default'    => 4,
                        'attributes' => array(
                            'type'  => 'number',
                            'step'  => 1,
                            'min'   => 0,
                            'style' => 'width:50px;'
                        ),
                        'classes'    => 'col-12 col-md-4'
                    ),
                    /* img_height */
                    array(
                        'id'         => $prefix . 'img_height',
                        'type'       => 'text',
                        'desc'       => 'Hauteur image (px).',
                        'default'    => 450,
                        'attributes' => array(
                            'type'  => 'number',
                            'step'  => 1,
                            'min'   => 0,
                            'style' => 'width:90px;'
                        ),
                        'classes'    => 'col-12 col-md-4'
                    ),
                    /* same_height */
                    array(
                        'id'         => $prefix . 'same_height',
                        'type'       => 'checkbox',
                        'desc'       => 'Forcer les diapositives à avoir la même hauteur.',
                        'classes'    => 'col-12 col-md-6'
                    ),
                    /* nav */
                    array(
                        'id'         => $prefix . 'nav',
                        'type'       => 'checkbox',
                        'desc'       => 'Afficher la navigation',
                        'classes'    => 'col-12 col-md-6'
                    ),
                    /* slides */
                    array(
                        'id'           => $prefix . 'slides',
                        'type'         => 'group',
                        'desc'         => 'Créer des diapos/slides.',
                        'options'      => array(
                            'group_title'   => 'Diapo {#}',
                            'add_button'    => 'Ajouter nouveau groupe',
                            'remove_button' => 'Supprimer groupe',
                            'sortable'      => true,
                            'closed'        => true,
                        ),
                        'column'       => array(
                            'position' => 3,
                            'name'     => 'Diapos',
                            'sortable' => false,
                        ),
                        'display_cb'   => array($this, 'slides_display_cb'),
                        'classes'      => 'col-12',
                        // 'repeatable'  => false,
                        'group-fields' => array(
                            /* color */
                            array(
                                'id'      => 'color',
                                'desc'    => 'Contraste général image',
                                'type'    => 'select',
                                'default' => 'black',
                                'options' => array(
                                    'white' => 'Blanc',
                                    'black' => 'Noir',
                                ),
                                'classes' => 'col-12 col-md-6'
                            ),
                            /* img */
                            array(
                                'id'           => 'img',
                                'desc'         => 'Ajouter l\'image',
                                'type'         => 'file',
                                // Optional:
                                'options'      => array(
                                    'url' => false, // Hide the text input for the url
                                ),
                                'text'         => array(
                                    'add_upload_file_text' => 'Ajouter imlage/photo'
                                ),
                                'preview_size' => 'medium',
                                'classes'      => 'col-12 col-md-6'
                            ),
                            /* text */
                            array(
                                'id'      => 'text',
                                'desc'    => 'Titre',
                                'type'    => 'wysiwyg',
                                'classes' => 'col-12 col-md-6',

                            ),
                            /* subtitle */
                            array(
                                'id'      => 'subtitle',
                                'desc'    => 'Sous-titre',
                                'type'    => 'wysiwyg',
                                'classes' => 'col-12 col-md-6'
                            ),
                            /* link */
                            array(
                                'id'      => 'link',
                                'type'    => 'post_link',
                                'name'    => 'Lien vers',
                                'desc'    => 'Ajouter un vers une publication existante.',
                                'classes' => 'col-12',
                            )
                        )
                    ),
                )
            );
        }

        /**
         * Actual mechanism to add custom fields
         *
         * @return void
         **/
        public function add_custom_fields()
        {
            if (!is_array($this->meta_boxes)) return false;
            foreach ($this->meta_boxes as $key => $value) {
                /**
                 * Initiate the metabox
                 */
                $cmb = new_cmb2_box($value['meta-box']);

                /**
                 * Add fields
                 */
                if (!is_array($value['fields'])) return false;
                foreach ($value['fields'] as $key => $field) {
                    $grpID = $cmb->add_field($field);
                    // Add group field
                    if ($field['type'] == 'group') {
                        foreach ($field['group-fields'] as $grp_field) {
                            $cmb->add_group_field($grpID, $grp_field);
                        }
                    }
                }
            }
        }

        /**
         * Display shortcode based on Post-type ID
         *
         * Undocumented function long description
         *
         * @param  object $field_args Current field args
         * @param  object $field      Current field object
         * @return type
         * @throws conditon
         **/
        public function shortcode_row_cb($field_args, $field)
        {
            $post_id = $field->object_id; //specify post id here
            $row_classes = 'cmb-row ' . $field->row_classes();
            $id          = $field->args('id');
            $label       = $field->args('name');
            $name        = $field->args('_name');
            $value       = $field->escaped_value();
            $description = $field->args('description');
            $shortcode_name = LSC_Helper()->get_shortcode_name();

            $slug = get_post_meta($post_id, '_lsc_slug', true);

            $output = '<div class="' . $row_classes . '">';
            if (!empty($label)) {
                $output .= '<div class="cmb-th">';
                $output .= '<label for="' . $id . '">' . $label . '</label>';
                $output .= '</div>';
            }
            $output .= '<div class="cmb-td">';
            $output .= '<p class="inline-shortcode">[' . $shortcode_name . ' id="' . $post_id . '"';
            $output .= !empty($slug) ? ' slug="' . $slug . '"' : '';
            $output .= ']</p>';
            $output .= '<p class="cmb2-metabox-description">' . $description . '</p>';
            $output .= '</div>';
            $output .= '</div>';
            echo $output;
        }

        /**
         * Display shortcode based on Post-type ID
         *
         * Undocumented function long description
         *
         * @param  object $field_args Current field args
         * @param  object $field      Current field object
         * @return string
         * @throws conditon
         **/
        public function shortcode_display_cb($field_args, $field)
        {
            $post_id = $field->object_id; //specify post id here
            $shortcode_name = LSC_Helper()->get_shortcode_name();
            // $output = '<div class="shortcode ' . $field->row_classes() . '">';
            $output = '<div class="shortcode">';
            $output .= '<p class="description text-nowrap">[' . $shortcode_name . ' id="' . $post_id . '"]</p>';
            $output .= '</div>';
            echo $output;
        }

        /**
         * Display shortcode based on Post-type ID
         *
         * Undocumented function long description
         *
         * @param  object $field_args Current field args
         * @param  object $field      Current field object
         * @return string
         * @throws conditon
         **/
        public function slides_display_cb($field_args, $field)
        {
            $post_id = $field->object_id; //specify post id here
            // $output = '<div class="slides ' . $field->row_classes() . '">';
            $output = '<div class="slides row">';
            foreach ($field->get_data() as $key => $value) {
                if (!array_key_exists('img_id', $value)) continue;
                $img_url = wp_get_attachment_image_url($value['img_id'], 'thumbnail');
                $output .= '<div class="col-2 mb-3">';
                $output .= '<img class="img-fit" src="' . $img_url . '" alt="">';
                $output .= '</div>';
            }
            $output .= '</div>';
            echo $output;
        }
    }
}
