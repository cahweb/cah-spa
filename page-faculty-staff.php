<?
/*
	Faculty & Staff
	---------------
	Replaces Colleges-Theme template.
	Uses the Wordpress plugin: WP Subtitle
	Reference: https://github.com/benhuson/wp-subtitle/wiki/Template-Tags-(Legacy)
*/
?>

<? get_header(); ?>

<div class="container">
	<h1 class="mt-3 mt-sm-4 mt-md-5 mb-3"><?= the_title() ?></h1>

	<? if (!empty(get_the_subtitle($post, '', '', false))): ?>
		<p class="lead mb-4 mb-md-5"><? get_the_subtitle($post, '', '', true) ?></p>
	<? endif; ?>

	<? if (!empty(get_field('page_header_extra_content', $post->ID))): ?>
		<div class="mb-4 mb-md-5"><?= get_field('page_header_extra_content', $post->ID) ?></div>
	<? endif; ?>
</div>

<?
	// Renders the post's contents, including any included shortcodes.
	if (!empty($post->post_content)) {
		echo apply_filters('the_content', get_post_field('post_content', $post->ID));
	}
?>

<? get_footer(); ?>