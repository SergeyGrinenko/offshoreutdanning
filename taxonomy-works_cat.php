<?php get_header();
$taxonomy = get_queried_object();
$tax_image = get_field('category_image', $taxonomy); ?>
<div id="page_content" class="archive-template">
    <div class="archive-header">
        <div class="bg-softdark">
            <div class="container p-0 position-relative">
                <img src="<?= $tax_image; ?>" width="100%" height="425" class="d-block taxonomy-image"  alt="category-image">
            </div>
        </div>

        <div class="bg-light-1 text-center">
            <div class="container py-0">
                <div class="archive-header__title">
                    <?php if ($taxonomy): ?>
                        <h1 class="m-0 text-dark-2">
                            <?= $taxonomy->name; ?>
                        </h1>

                        <?php if ($taxonomy->description): ?>
                            <p class="description"><?= $taxonomy->description; ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="archive-content bg-light">
        <div class="container py-0">
            <?php if (have_posts()) : ?>
                <div class="posts-cards">
                    <?php while (have_posts()) : the_post(); ?>
                        <a href="<?= get_the_permalink(); ?>" class="post-card text-dark">
                            <div class="post-card__content">
                                <img src="<?= get_the_post_thumbnail_url(get_the_ID(), 'post-thumbnail'); ?>"
                                     alt="post-image" width="100%" class="d-block">

                                <h2><?php the_title(); ?></h2>
                                <?php if (get_the_excerpt()): ?>
                                    <p class="short-description"><?= get_the_excerpt(); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="text-right post-card__link">
                                <span class="text-decoration text-dark">
                                    <?= get_field('learn_more__text', get_the_ID()); ?>
                                </span>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <h2 class="m-0 py-3 text-pink">Not found</h2>
            <?php endif; ?>
        </div>
    </div>

</div>
<?php get_footer(); ?>
