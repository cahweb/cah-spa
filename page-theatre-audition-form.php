<?php
get_header();
?>

<div class="container mb-4 mt-5" style="min-height: 240px;">
    <? wp_nonce_field( 'theatre_form_submit', 'theatre-form-nonce' ); ?>
    <div id="theatre-audition-app"></div>
</div>

<?php
get_footer();
?>