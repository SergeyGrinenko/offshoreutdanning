<?php get_header();
global $post;?>
<div id="page_content" class="single-template">
    <div class="singhe-header">
        <div class="bg-softdark">
            <div class="container p-0 position-relative">
                <img src="<?= get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" width="100%" class="d-block post-image" alt="post-image">
            </div>
        </div>

        <div class="bg-light-1 text-center">
            <div class="container py-0">
                <div class="single-header__title">
                    <h1 class="m-0 text-dark-2">
                        <?php the_title() ?>
                    </h1>
                    <div><?= get_post_breadcrumb(get_the_ID(), 'works_cat'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="archive-content bg-light">
        <?php the_content(); ?>
    </div>
</div>
<?php get_footer(); ?>
