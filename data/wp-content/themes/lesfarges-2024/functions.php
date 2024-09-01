<?php

if (!defined('LFG_PATH')) {
    define('LFG_PATH', get_stylesheet_directory());
}

if (!defined('LFG_INCLUDES')) {
    define('LFG_INCLUDES', LFG_PATH . '/includes/');
}

if (!defined('LFG_PATH_URI')) {
    define('LFG_PATH_URI', get_stylesheet_directory_uri());
}

if (!defined('CHILD_ASSETS')) {
    define('CHILD_ASSETS', LFG_PATH_URI . '/assets/');
}

require LFG_INCLUDES . 'class-lfg-2024.php';
add_action('after_setup_theme', function () {
    LFG_2024::getInstance();
});

// Add custom post type
