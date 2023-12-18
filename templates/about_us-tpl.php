<?php
/* Template Name: About Us */
get_header();

$sidebar_title = get_field('sidebar_title');
$sidebar_posts = get_field('sidebar_posts');
$grid = ($sidebar_title && $sidebar_posts) ? 'grid-7-3' : null;
$page_title = get_field('page_title');
$page_subtitle = get_field('subtitle');
?>

    <div class="about-us">
        <div class="about-us__header">
            <div class="bg-softdark">
                <div class="container p-0 position-relative">
                    <img src="<?= get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" width="100%"
                         class="d-block page-image" alt="page-image">
                </div>
            </div>

            <div class="bg-light-1 text-center subheader">
                <div class="container p-0">
                    <div class="subheader__container">
                        <?php if ($page_title): ?>
                            <h1><?= $page_title; ?></h1>
                        <?php endif; ?>

                        <?php if ($page_subtitle): ?>
                            <h3><?= $page_subtitle; ?></h3>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="about-us__content bg-white">
            <div class="container-fluid py-0">
                <div class="container p-0 <?= $grid; ?>">
                    <div class="main-content">
                        <?php the_content(); ?>
                    </div>
                    <?php if (!empty($grid)): ?>
                        <div class="right-sidebar">
                            <?php $post_type = '' ?>
                            <h2><?= $sidebar_title; ?></h2>
                            <?php if ($sidebar_posts): ?>

                                <div class="right-sidebar__posts">
                                    <?php foreach ($sidebar_posts as $post):
                                        setup_postdata($post);
                                        $post_type = get_post_type(get_the_ID()); ?>

                                        <div class="post-card">
                                            <div class="post-card__content">
                                                <img src="<?= get_the_post_thumbnail_url($post->ID, 'post-thumbnail') ?>"
                                                        alt="post-image" width="100%" class="d-block">

                                                <div class="content-wrapper">
                                                    <h3><?php the_title(); ?></h3>

                                                    <?php if (get_the_excerpt($post->ID)): ?>
                                                        <p><?= get_the_excerpt($post->ID); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="text-right post-card__link">
                                                <a href="<?= get_the_permalink($post->ID); ?>"
                                                   class="text-decoration text-dark">
                                                    <?= get_the_title($post->ID); ?>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php wp_reset_postdata(); ?>
                            <?php endif; ?>
                            <a href="/tema" class="text-dark text-decoration d-block more_posts">Se flere poster</a>
                        </div>

                    <?php endif; ?>
                </div>
            </div>

            <div class="our-team container-fluid bg-light-1">
                <div class="container px-0">
                    <h2 class="m-0">Kontaktpunkt</h2>

                    <?php if (have_rows('our_team')): ?>
                        <div class="photos-grid">
                            <?php while (have_rows('our_team')) : the_row();
                                $photo = get_sub_field('photo');
                                $name = get_sub_field('name');
                                $position = get_sub_field('position');
                                $email = get_sub_field('email'); ?>

                                <div class="photos-grid__item">
                                    <img src="<?= $photo; ?>"
                                         alt="photo"
                                         width="128" height="128" class="photo">

                                    <div class="caption">
                                        <?php if ($name): ?>
                                            <h3 class="m-0"><?= $name; ?></h3>
                                        <?php endif; ?>

                                        <?php if ($position): ?>
                                            <h3 class="m-0"><?= $position; ?></h3>
                                        <?php endif; ?>

                                        <?php if ($email): ?>
                                            <h3 class="m-0"><?= $email; ?></h3>
                                        <?php endif; ?>

                                    </div>
                                </div>

                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
<?php get_footer(); ?>