<?php
/**
 * Custom functions for the SPA theme.
 */

include "dev/dev.php";
include "includes/degree-cards.php";


add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/static/css/style.min.css' );
    
    // Customized Style
	wp_enqueue_style( 'child-style',  get_stylesheet_directory_uri() . '/style.css', array('parent-style'));
}

// Defining SPA department code
define( 'DEPT', 6 );

add_action('wp_enqueue_scripts', 'spa_menu_fix', 10, 0);
function spa_menu_fix() {
	wp_enqueue_script( 'menu-fix', get_stylesheet_directory_uri() . '/dist/js/menu-fix.js', ['jquery'], '20200505', true);
}

// Shadowing the function in the parent theme, so we can add special functionality.
function get_header_default_markup( $post ) {

	if( !isset( $post ) || !is_object( $post ) ) return;

    if ( 'studio' == $post->post_type || 'ensemble' == $post->post_type ) {
        return '';
    }
    
	$title         = get_header_title( $post );
	$subtitle      = get_header_subtitle( $post );
	$extra_content = get_field( 'page_header_extra_content', $post->ID );
	ob_start();
?>

	<div class="container">
		<?
			// Don't print multiple h1's on the page for person templates
			if ( $post->post_type === 'person' ):
		?>
		<strong class="h1 d-block mt-3 mt-sm-4 mt-md-5 mb-3"><?= $title; ?></strong>
		
		<?
			// Added this condition to prevent printing just a blank bar on the front page
			elseif (is_front_page()):
		?>

		<? else: ?>
			<h1 class="mt-3 mt-sm-4 mt-md-5 mb-3"><?= $title; ?></h1>
		
		<? endif; ?>

		<? if ( $subtitle ): ?>
			<p class="lead mb-4 mb-md-5"><?= $subtitle; ?></p>
		<? endif; ?>

		<? if ( $extra_content ): ?>
			<div class="mb-4 mb-md-5"><?= $extra_content; ?></div>
		<? endif; ?>
	</div>

<?
	return ob_get_clean();
}

// Enables the Excerpt meta box in post type edit screen.
function wpcodex_add_excerpt_support_for_post() {
	add_post_type_support( 'page', 'excerpt' );
}
add_action( 'init', 'wpcodex_add_excerpt_support_for_post' );

// Ensemble Interest Form Results
include_once "includes/ensemble-student-results.php";
add_action( 'init', [ "UCF\\CAH\\SPA\\Music\\EnsembleAdminPage", 'setup' ], 10, 0 );

// Theatre Audition Form
include_once "audition-forms/theatre-audition-form/theatre-audition-form-setup.php";
add_action( 'init', [ "UCF\\CAH\\SPA\\Theatre\\AuditionForm\\AuditionFormSetup", 'setup' ], 10, 0 );

include_once "audition-forms/theatre-audition-form/includes/theatre-audition-form-admin.php";
add_action( 'init', [ "UCF\\CAH\\SPA\\Theatre\\AuditionForm\\AuditionFormAdmin", 'setup' ], 10, 0 );

// Music Audition Form
include_once "audition-forms/music-audition-form/music-audition-form-setup.php";
add_action( 'init', [ "UCF\\CAH\\SPA\\Music\\AuditionForm\\AuditionFormSetup", 'setup' ], 10, 0 );

?>