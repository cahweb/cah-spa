<?php get_header(); ?>


<div class="container-fluid mobile-header">
    <?
        $title = get_the_title();
        $subtitle = get_the_subtitle();
    ?>

    <? if ($title != ""): ?>
    <div class="row bg-primary">
        <div class="col-10 mx-auto p-3">
            <p class="h2 text-center text-uppercase mb-0 w-100"><?= $title ?></p>
        </div>
    </div>
    <? endif ?>
    <? if ($subtitle != ""): ?>
    <div class="row bg-inverse">
        <div class="col-10 mx-auto p-3">
            <p class="h3 font-sans-serif font-weight-bold text-center text-uppercase mb-0 w-100"><?= $subtitle ?></p>
        </div>
    </div>
    <? endif ?>
</div>

<? the_post(); ?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<div class="container mt-4 mt-sm-5 mb-5 pb-sm-4">
		<?php the_content(); ?>
	</div>
</article>

<?php get_footer(); ?>
