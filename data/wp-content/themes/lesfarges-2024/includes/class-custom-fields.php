<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('LFG_2024_Cfs')) {

    /**
     * LFG_2024_Cfs
     *
     *  Description
     *
     * @since   1.0.0
     * @package lesfarges-2024
     * @subpackage includes/lesfarges-2024
     *
     */
    class LFG_2024_Cfs
    {
        /**
         * @var LFG_2024_Cfs
         * @access private
         * @static
         */
        private static $_instance = null;

        /**
         * @var LFI_Loader $loader  
         */
        protected $loader;

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
         * @return LFG_2024_Cfs
         */
        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new LFG_2024_Cfs();
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
            // Set/Register Meta Boxes first
            $this->post_example_custom_fields();

            // Initiate meta-boxes creation
            $this->add_hooks();
        }

        /**
         * Hooks
         *
         **/
        private function add_hooks()
        {
            $loader = new LFI_Loader();
            $loader->add_action(
                'cmb2_admin_init',
                $this,
                'add_custom_fields'
            );

            $loader->run();
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
                    if ($field['type'] == 'group' && is_array($field['group-fields'])) {
                        $grp_field_id = $cmb->add_field($field);
                        foreach ($field['group-fields'] as $key => $field_grp) {
                            $cmb->add_group_field($grp_field_id, $field_grp);
                        }
                    } else {
                        $cmb->add_field($field);
                    }
                }
            }
        }

        /**
         * Posts example Custom Fields
         *
         * @since 1.0.0
         * 
         * @return void
         **/
        public function post_example_custom_fields()
        {
            $prefix = '_post_';
            $this->meta_boxes[] = array(
                'meta-box' => array(
                    'id'    => 'post-extra',
                    'title' => 'Extras',
                    'context' => 'side', //  'normal', 'side', and 'advanced'
                    'priority' => 'high', // 'high' and 'low'
                    'object_types' => array('post'),
                ),
                'fields'   => array(
                    array(
                        'id'   => $prefix . 'subtitle',
                        'type' => 'text',
                        'name' => 'Sous-Titre'
                    ),
                    array(
                        'id'           => $prefix . 'slides',
                        'type'         => 'file_list',
                        'name'         => 'Diapos',
                        // 'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
                    ),
                )
            );
        }
    }
}