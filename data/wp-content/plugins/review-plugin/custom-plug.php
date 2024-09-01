<?php

/**
 * Plugin Name: Custom-Review
 * Description: Gestion avis
 * Version: 1.0
 * Author: LFI - La Fabrique Info
 */

define('PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('PLUGIN_CPT_NAME', 'custom-review');

require_once PLUGIN_DIR_PATH . '/includes/plugin-comment.php';
// require_once (__DIR__) . 'includes/plugin-comment-ajax.php';
