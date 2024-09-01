<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    lfi-2024
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    lfi-2024
 * @author     Your Name <email@example.com>
 */
class LFI_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_theme_textdomain() {
		// Theme translation
        load_theme_textdomain(
            LFI_Helper()->domain(),
            LFI_Helper()->get_abs_path('languages')
        );

	}

}
