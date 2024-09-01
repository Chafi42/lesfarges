<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('LSC_Helper')) {

    /**
     * LSC_Helper
     *
     *  Class to facilitate the work with it.
     *
     * @since      1.0.0
     * @package    lfi-simple-carousel
     * @subpackage lfi-simple-carousel/includes
     * @author     LFI <contact@lafabriqueinfo.fr>
     *
     */
    class LSC_Helper
    {
        /**
         * @var LSC_Helper
         * @access private
         * @static
         */
        private static $_instance = null;

        /**
         * Plugin Name
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $name    Plugin Name
         */
        private $name;
        
        /**
         * Plugin version
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $version    Plugin version
         */
        private $version;
        
        /**
         * Plugin text-domain
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $domain    Plugin text-domain
         */
        private $domain;
        
        /**
         * Plugin status
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $status    Plugin status
         */
        private $status;
        
        /**
         * Plugin shortocde name
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $shortcode_name    Plugin shortocde name
         */
        private $shortcode_name;
        

        /**
         * Méthode qui crée l'unique instance de la classe
         * si elle n'existe pas encore puis la retourne.
         *
         * 
         * @return LSC_Helper
         */
        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new LSC_Helper();
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
            if (defined('LSC_STATUS')) {
				$this->status = LSC_STATUS;
			} else {
				$this->status = 'dev';
			}
            if (defined('LSC_VER')) {
				$this->version = LSC_VER;
			} else {
				$this->version = '1.0.0';
			}
			if (defined('LSC_NAME')) {
				$this->name = LSC_NAME;
			} else {
				$this->name = 'lfi-simple-carousel';
			}
			if (defined('LSC_DOMAIN')) {
				$this->name = LSC_DOMAIN;
			} else {
				$this->name = 'lfi-simple-carousel';
			}
            $this->shortcode_name = 'slider';
        }

        /**
         * Set the theme status into developpement
         *
         * By default it's on production
         *
         * @param bool $status
         **/
        public function get_plugin_status()
        {
            return $this->status;
        }

        /**
         * Get a random number
         *
         * Useful to have a new css or js file every time and avoid caching
         *
         * @return string
         **/
        public function get_random($min = 1, $max = 99999999999)
        {
            return rand($min, $max);
        }

        /**
         * Get Plugin Name
         *
         * @return string The plugin name
         **/
        public function get_plugin_name()
        {
            return $this->name;
        }

        /**
         * Get Plugin version
         *
         * @return string The plugin version
         **/
        public function get_plugin_ver()
        {
            return $this->version;
        }

        /**
         * Get Plugin text-domain
         *
         * @return string The plugin text-domain
         **/
        public function get_plugin_domain()
        {
            return $this->domain;
        }

        /**
         * Get Plugin shortcode name
         *
         * @return string The plugin shortcode name
         **/
        public function get_shortcode_name()
        {
            return $this->shortcode_name;
        }

        /**
         * Get theme path URI or ABS
         *
         * @param string $filename file path from folder (ex: class-lfi.php)
         * @param string $folder folder name from theme root (ex: includes ou assets)
         * @param string $path Absolute (abs) or Relative (uri)
         * @return string
         **/
        private function get_path($file, $type = 'abs')
        {
            $abs_path = trailingslashit(plugin_dir_path(LSC_FILE));
            $uri_path = trailingslashit(plugin_dir_url(LSC_FILE));

            if ($this->status == "dev" && $type == "uri") {
                $file = $file . '?' . $this->get_random();
            }

            switch ($type) {
                case 'uri':
                    return $uri_path . $file;
                    break;

                default:
                    return $abs_path . $file;
                    break;
            }
        }

        /**
         * Get the plugin absolute path + the file path given
         *
         * @since 1.0.0
         * 
         * @param string $file path from the plugin dir
         * @return string
         * 
         **/
        public function get_abs_path($file = '')
        {
            return $this->get_path($file);
        }
        
        /**
         * Get the plugin relative path + the file path given
         * 
         * @since 1.0.0
         *
         * @param string $file path from the plugin dir
         * @return string
         * 
         **/
        public function get_uri_path($file = '')
        {
            return $this->get_path($file, 'uri');
        }

        /**
         * Require files wrapper
         * 
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies 
         *      $dependencies = [
         *          'type' = [
         *              'file path from ',
         *              'file',
         *              'file',
         *              ...
         *          ]
         *      ]  
         * @param bool $once require_once or require
         * @return void
         * 
         **/
        public function require($dependencies = array(), $once = true)
        {
            foreach ($dependencies as $type => $files) {
                $type = is_numeric($type) ? 'abs' : $type;
                if (!is_array($files)) continue;
                foreach ($files as $filename) {
                    if ($once) {
                        require_once $this->get_path($filename, $type);
                    } else {
                        require $this->get_path($filename, $type);
                    }
                }
            }
        }
    }
}
