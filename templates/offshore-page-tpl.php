<?php
/* Template Name: Offshore Page Template */

get_header();
global $product;
/* Hero Fields */
$hero_background = get_field('hero_background');
$hero_title = get_field('hero_title');
$hero_subtitle = get_field('hero_subtitle');
$hero_caption = get_field('hero_caption');

$button_1_name = get_field('button_1_name');
$button_1_link = get_field('button_1_link');

$button_2_name = get_field('button_2_name');
$button_2_link = get_field('button_2_link');

/* Promo Fields */

?>

<div class="offshore-page">
    <div class="bg-softdark">
        <div class="container p-0">
            <div class="hero-section">
                <div class="p-3 hero-info bg-dark-2">
                    <div>
                        <?php if ($hero_title): ?><h1 class="text-light p-2"><?= $hero_title; ?></h1><?php endif; ?>
                        <?php if ($hero_subtitle): ?><h5 class="text-light"><?= $hero_subtitle; ?></h5><?php endif; ?>
                        <?php if ($hero_caption): ?><p class="text-light"><?= $hero_caption; ?></p><?php endif; ?>
                    </div>

                    <div class="mb-3 text-center hero-cta">
                        <?php if ($button_1_name): ?>
                            <a href="<?= $button_1_link; ?>"
                               class="btn rounded7 m-0 w-100 justify-center text-white transparent">
                                <?= $button_1_name; ?>
                            </a>
                        <?php endif; ?>
                        <?php if ($button_2_name): ?>
                            <a href="<?= $button_2_link; ?>"
                               class="btn rounded7 m-0 w-100 justify-center text-dark bg-white">
                                <?= $button_2_name; ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <img src="<?= $hero_background; ?>" class="hero-image">
            </div>
        </div>
    </div>

    <div class="frontpage-promo-wrapper">
        <div class="container p-0">

            <?php if (have_rows('promo_sections')): ?>
                <div class="frontpage-promo">
                    <?php while (have_rows('promo_sections')) : the_row(); ?>
                        <div class="promo-part bg-white text-center">
                            <?php $section_title = get_sub_field('title'); ?>
                            <?php $section_link_title = get_sub_field('link_title'); ?>
                            <?php $section_link_url = get_sub_field('link_url'); ?>

                            <?php if ($section_title): ?>
                                <h2 class="text-dark fw-normal mt-0"><?= $section_title; ?></h2>
                            <?php endif; ?>

                            <?php if (have_rows('courses')): ?>
                                <div class="d-flex justify-between promo-part__grid">
                                    <?php while (have_rows('courses')) : the_row(); ?>
                                        <?php $extra_course_title = get_sub_field('extra_name'); ?>
                                        <?php $extra_course = get_sub_field('course'); ?>
                                        <?php $extra_course_desc = get_sub_field('course_desc'); ?>
                                        <div class="promo-item">
                                            <?php if ($extra_course): ?>
                                                <?php $permalink = get_permalink($extra_course->ID); ?>
                                                <?php $title = ($extra_course_title ? $extra_course_title : get_the_title($extra_course->ID)); ?>
                                                <a href="<?php echo esc_url($permalink) ?>"
                                                   class="btn m-0 bg-pink text-white justify-center rounded7">
                                                    <?php echo esc_html($title) ?>
                                                </a>
                                                <?php if ($extra_course_desc): ?>
                                                    <p class="caption text-dark mt-3">
                                                        <?= $extra_course_desc; ?>
                                                    </p>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($section_link_url && $section_link_title): ?>
                                <a href="<?= $section_link_url; ?>" class="text-dark text-decoration mt-3 more-promo">
                                    <?= $section_link_title; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>


            <?php if (have_rows('popular_questions')): ?>
                <div class="related-courses">
                    <?php while (have_rows('popular_questions')) : the_row(); ?>
                        <?php $card_image = get_sub_field('image'); ?>
                        <?php $card_title = get_sub_field('card_title'); ?>
                        <?php $card_link = get_sub_field('card_link'); ?>
                        <?php $card_caption = get_sub_field('card_caption'); ?>

                        <?php if ($card_image && $card_title): ?>
                            <div class="related-course bg-softdark p-3">
                                <img src="<?= $card_image; ?>" width="520" height="312" alt="card-image">

                                <div class="related-course__content py-3">
                                    <?php if ($card_link): ?><a href="<?= $card_link ?>"
                                                                class="text-white"><?php endif; ?>
                                        <h2 class="fw-normal mt-0"><?= $card_title ?></h2>
                                        <?php if ($card_link): ?></a><?php endif; ?>
                                    <?php if ($card_caption): ?>
                                        <p class="description"><?= $card_caption ?></p>
                                    <?php endif; ?>

                                    <?php if (have_rows('page_list')): ?>
                                        <ul>
                                            <?php while (have_rows('page_list')) : the_row(); ?>
                                                <?php $page_list_title = get_sub_field('page_title'); ?>
                                                <?php $page_list_link = get_sub_field('page_link'); ?>
                                                <?php if ($page_list_title && $page_list_link): ?>
                                                    <li>
                                                        <a href="<?= $page_list_link; ?>"
                                                           class="text-decoration text-white">
                                                            <?= $page_list_title; ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endwhile; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php get_footer(); ?>
