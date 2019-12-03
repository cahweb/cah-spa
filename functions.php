<?php
/**
 * Custom functions for the SPA theme.
 */

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/static/css/style.min.css' );
    
    // Customized Style
    wp_enqueue_style( 'child-style',  get_stylesheet_directory_uri() . '/style.css', array('parent-style'));
}

// Including child functions and their respective style sheets, which are separated into other files for organization.

// Events
include "functions/events.php";
wp_enqueue_style('events-styles', get_stylesheet_directory_uri() . '/styles/events.css');

// Developer functions for testing and debugging. Remove in production.
include "functions/dev.php";

?>