<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('LFG_2024_Tax')) {

    /**
     * LFG_2024_Tax
     *
     *  Description
     *
     * @since   1.0.0
     * @package lesfarges-2024
     * @subpackage includes/lesfarges-2024
     *
     */
    class LFG_2024_Tax
    {
        /**
         * @var LFG_2024_Tax
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
         * @return LFG_2024_Tax
         */
        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new LFG_2024_Tax();
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
            // Add taxonomies to Post-type
            $this->add_taxonomies();
        }

        /**
         * Add Taxonomies
         *
         * @since      1.0.0
         * @return     void
         **/
        public function add_taxonomies()
        {
            $taxonomies = array(
                array(
                )
            );

            LFI_CPT::add_taxonomies($taxonomies);
        }
    }
}