<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('LFG_2024_CPT')) {

    /**
     * LFG_2024_CPT
     *
     *  Description
     *
     * @since   1.0.0
     * @package lesfarges-2024
     * @subpackage includes/lesfarges-2024
     *
     */
    class LFG_2024_CPT
    {
        /**
         * @var LFG_2024_CPT
         * @access private
         * @static
         */
        private static $_instance = null;

        /**
         * @var LFI_Loader $loader  
         */
        protected $loader;
        
        /**
         * Méthode qui crée l'unique instance de la classe
         * si elle n'existe pas encore puis la retourne.
         *
         * 
         * @return LFG_2024_CPT
         */
        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new LFG_2024_CPT();
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
            // Add custom post types
            $this->add_post_types();
        }

        /**
         * Add Custom Post-type
         *
         * @since      1.0.0
         * @package    moz-2024
         * @subpackage moz-2024/admin
         * 
         * @return     void
         * 
         **/
        private function add_post_types()
        {
            $post_types = array(

            );

            LFI_CPT::add_post_types($post_types);
        }

        /**
         * Add Taxonomies
         *
         * @since      1.0.0
         * @package    moz-2024
         * @subpackage moz-2024/admin
         * 
         * @return     void
         * 
         **/
        public function add_taxonomies()
        {
            $taxonomies = array(
                array(
                    'posttype' => 'project',
                    'names' => array(
                        'name'     => 'project-cat',
                        'slug'     => 'projects-cat',
                        'singular' => 'Catégorie Projet',
                        'plural'   => 'Catégories Projet',
                    ),
                    'options' => array(
                        'show_ui'            => true,
                        // 'show_in_quick_edit' => false,
                        'show_admin_column'  => false,
                        // 'meta_box_cb'        => false
                    ),

                )
            );

            LFI_CPT::add_taxonomies($taxonomies);
        }
    }
}