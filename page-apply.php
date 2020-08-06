<?php
global $post;

$parent = get_post( wp_get_post_parent_id( $post->ID ) );
$parent_slug = $parent->post_name;

get_header();
?>

<div class="container mb-4 mt-5" style="min-height: 240px;">
    <?php wp_nonce_field( "${parent_slug}_form_submit", "$parent_slug-form-nonce" ); ?>
    <div id="<?= "$parent_slug-audition-app" ?>"></div>
</div>

<?php
get_footer();
?>