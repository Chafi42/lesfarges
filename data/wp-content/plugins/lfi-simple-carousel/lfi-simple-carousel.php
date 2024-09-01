<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.lafabriqueinfo.fr
 * @since             1.0.0
 * @package           lfi-simple-carousel
 *
 * @wordpress-plugin
 * Plugin Name:       LFI Simple Carousel
 * Description:       Simple Carousel.
 * Version:           1.0.0
 * Author:            La Fabrique Info
 * Author URI:        https://www.lafabriqueinfo.fr/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lfi-simple-carousel
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Constants
if (!defined('LSC_FILE')) {
	define('LSC_FILE', __FILE__);
}
if (!defined('LSC_DIR')) {
	define('LSC_DIR', dirname(__FILE__));
}
if (!defined('LSC_NAME')) {
	define('LSC_NAME', 'lfi-simple-carousel');
}
if (!defined('LSC_VER')) {
	define('LSC_VER', '1.0.0');
}
if (!defined('LSC_DOMAIN')) {
	define('LSC_DOMAIN', 'lfi-simple-carousel');
}
if (!defined('LSC_STATUS')) {
	define('LSC_STATUS', 'dev');
}

// Posttype name and slug
if (!defined('LSC_CPT_NAME')) {
	define('LSC_CPT_NAME', 'lsc-slider');
}
if (!defined('LSC_CPT_SLUG')) {
	define('LSC_CPT_SLUG', 'lsc-sliders');
}

/**
 * The plugin Helper class that is used to facilitate work,
 */
require plugin_dir_path(__FILE__) . 'includes/class-lsc-helper.php';

if (!function_exists('LSC_Helper')) {
    /**
     * Retourne une instance de LSC_Helper
     *
     * @since  1.0.0
     * @return LSC_Helper
     */
    function LSC_Helper() { 
        return LSC_Helper::getInstance(array('status' => 'dev'));
    }
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lsc-activator.php
 */
function activate_lfi_simple_carousel() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lsc-activator.php';
	LSC_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-lsc-deactivator.php
 */
function deactivate_lfi_simple_carousel() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lsc-deactivator.php';
	LSC_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_lfi_simple_carousel' );
register_deactivation_hook( __FILE__, 'deactivate_lfi_simple_carousel' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-lsc.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_lfi_simple_carousel() {

	$plugin = new LFI_Simple_Carousel();
	$plugin->run();

}
run_lfi_simple_carousel();
