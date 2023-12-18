<?php get_header();
global $post;
$post_tags = get_the_terms($post->ID, 'tags'); ?>
<div class="single-help-page">
    <div class="singhe-header">
        <?php if ($post_tags[0]->slug === 'professions-guide') : ?>
            <div class="bg-softdark course-title-wrapper">
                <div class="container py-0 position-relative">
                    <div class="grid-6-4 align-center help-header">
                        <div>
                            <h1 class="text-white"><?php echo get_the_title($post->ID); ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-softdark">
                <div class="container p-0 position-relative">
                    <img src="<?= get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" width="100%"
                         class="d-block post-image" alt="post-image">
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="bg-light-1 text-center my-3">
        <div class="container py-0">

            <?php if ($post_tags[0]->slug === 'professions-guide') : ?>
                <?php echo get_template_part('template-parts/professions-guide', false, ['post_id' => $post->ID]); ?>
            <?php else: ?>

                <div class="single-header__title">
                    <h1 class="m-0 text-dark-2">
                        <?php the_title() ?>
                    </h1>
                    <div><?= get_post_breadcrumb(get_the_ID(), 'hjelpcat'); ?></div>

                    <?php if (has_excerpt()) : ?>
                        <h3><?= get_the_excerpt(); ?></h3>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <div class="post-content bg-light">
        <?php the_content(); ?>
    </div>

</div>
<?php get_footer(); ?>
