<?php
/* Template Name: Home Page */
get_header();
global $product;
$hero_bg = get_field('hero_background');
$hero_title = get_field('hero_title');
$set_title = get_field('set_title');
$set_desc = get_field('set_description');
$set_courses = get_field('set_courses');
$main_bg = get_field('main_background'); ?>

<div id="frontpage-lead-row">
    <div class="frontpage-lead" style="background-image: url(<?php echo $hero_bg; ?>)">
        <div>
            <h1 class="h1-1"><?php echo $hero_title ?></h1>
            <?php if (have_rows('subtitles_grid')): ?>
                <h2 class="mx-3">
                    <?php while (have_rows('subtitles_grid')) : the_row(); ?>
                        <a href="<?php the_sub_field('link'); ?>" class="m-2 p-2"><?php the_sub_field('name'); ?></a>
                    <?php endwhile; ?>
                </h2>
            <?php endif; ?>
        </div>
        <!--            <h1><span class="h1-1">--><?php //echo $hero_title ?><!--</span></h1>-->
    </div>
</div>

<div class="frontpage-promo-wrapper">
    <div class="frontpage-promo">
        <div class="frontpage-grid">
            <div class="left-column column">
                <?php if (have_rows('left_column')): ?>
                    <?php while (have_rows('left_column')) : the_row(); ?>
                        <?php
                        $left_col_color = (get_sub_field('item_color') ? get_sub_field('item_color') : 'white');
                        $left_col_text_color = (get_sub_field('text_color') ? get_sub_field('text_color') : 'white');
                        $left_col_heading = get_sub_field('heading');
                        $left_col_desc = get_sub_field('description');
                        $left_course_link_color = get_sub_field('course_link_color');
                        $left_col_courses = get_sub_field('courses'); ?>
                        <div class="p-3 column-item d-flex align-items-center"
                             style="background-color: <?= $left_col_color; ?>; color: <?= $left_col_text_color; ?>">
                            <div class="w-100">
                                <?php if ($left_col_heading): ?>
                                    <h2 class="m-0"><?= $left_col_heading; ?></h2>
                                <?php endif; ?>
                                <?php if ($left_col_desc): ?>
                                    <p class="m-0"><?= $left_col_desc; ?></p>
                                <?php endif; ?>

                                <?php if ($left_col_courses): ?>
                                    <ul>
                                        <?php foreach ($left_col_courses as $left_col_course):
                                            $permalink = get_permalink($left_col_course->ID);
                                            $title = (get_field('alt_title', $left_col_course->ID) ? get_field('alt_title', $left_col_course->ID) : get_the_title($left_col_course->ID)); ?>
                                            <li>
                                                <a href="<?php echo esc_url($permalink); ?>"
                                                   style="background-color: <?= $left_col_text_color; ?>; color: <?= $left_course_link_color; ?>"><span><?php echo esc_html($title); ?></span></a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
            <div class="right-column column"><?php if (have_rows('right_column')): ?>
                    <?php while (have_rows('right_column')) : the_row(); ?>
                        <?php
                        $right_col_color = (get_sub_field('item_color') ? get_sub_field('item_color') : 'white');
                        $right_col_text_color = (get_sub_field('text_color') ? get_sub_field('text_color') : 'white');
                        $right_col_heading = get_sub_field('heading');
                        $right_col_desc = get_sub_field('description');
                        $right_course_link_color = get_sub_field('course_link_color');
                        $right_col_courses = get_sub_field('courses');
                        ?>
                        <div class="p-3 column-item d-flex align-items-center"
                             style="background-color: <?= $right_col_color; ?>; color: <?= $right_col_text_color; ?>">
                            <div class="w-100">
                                <?php if ($right_col_heading): ?>
                                    <h2 class="m-0"><?= $right_col_heading; ?></h2>
                                <?php endif; ?>
                                <?php if ($right_col_desc): ?>
                                    <p class="m-0"><?= $right_col_desc; ?></p>
                                <?php endif; ?>

                                <?php if ($right_col_courses): ?>
                                    <ul>
                                        <?php foreach ($right_col_courses as $right_col_course):
                                            $permalink = get_permalink($right_col_course->ID);
                                            $title = (get_field('alt_title', $right_col_course->ID) ? get_field('alt_title', $right_col_course->ID) : get_the_title($right_col_course->ID)); ?>
                                            <li>
                                                <a href="<?php echo esc_url($permalink); ?>"
                                                   style="background-color: <?= $right_col_text_color; ?>; color: <?= $right_course_link_color; ?>"><span><?php echo esc_html($title); ?></span></a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?></div>


        </div>
    </div>

    <?php if ($set_title && $set_courses): ?>
        <div class="course_set">
            <div class="p-3">
                <div class="p-2 bg-white">
                    <h2 class="m-0"><?= $set_title; ?></h2>
                    <?php if ($set_desc): ?>
                        <p class="m-0"><?= $set_desc; ?></p>
                    <?php endif; ?>
                    <ul>
                        <?php foreach ($set_courses as $set_course):
                            $permalink = get_permalink($set_course->ID);
                            $title = (get_field('alt_title', $set_course->ID) ? get_field('alt_title', $set_course->ID) : get_the_title($set_course->ID)); ?>
                            <li>
                                <a href="<?php echo esc_url($permalink); ?>"><span><?php echo esc_html($title); ?></span></a>
                                <div class="list-arrow"></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php $related_pages = get_field('related_pages');
    if ($related_pages): ?>
        <div class="related-nav" <?= ($main_bg ? 'style="background-color: ' . $main_bg . '"' : ''); ?>>
            <?php foreach ($related_pages as $post):
                setup_postdata($post);
                $title = (get_field('alt_title', $post->ID) ? get_field('alt_title', $post->ID) : get_the_title($post->ID));
                ?>
                <a href="<?php the_permalink(); ?>">
                    <div class="related-nav__item text-center px-2"><?php echo $title; ?></div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>

    <?php $featured_posts = get_field('related_courses');
    if ($featured_posts): ?>
        <div class="related-courses" <?= ($main_bg ? 'style="background-color: ' . $main_bg . '"' : ''); ?>>
            <?php foreach ($featured_posts as $post):
                setup_postdata($post);
                if (has_post_thumbnail()):
                    $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'course-thumbnail');
                else:
                    $thumbnail = wc_placeholder_img_src('course-thumbnail');
                endif;

                $cross_sell_ids = $product->get_cross_sell_ids();

                ?>
                <div class="related-courses__item p-2">
                    <div class="course_image"
                         style="background-image: url(<?= $thumbnail ?>)"></div>
                    <div class="course_info bg-white black d-flex justify-content-between">
                        <div>
                            <h2 class="mt-0"><?php the_title() ?></h2>
                            <div class="course_info__description mb-2">
                                <?= get_the_excerpt(); ?>
                            </div>
                        </div>
                        <div class="link-wrap">
                        <a href="<?php the_permalink(); ?>" class="course_link d-flex">
                            <div class="course_link__text w-100 px-2 d-flex align-items-center"><?php the_title() ?></div>
                            <div class="course_link__icon bg-blue">
                                <img src="/wp-content/themes/offshoreutdanning/assets/images/icon-right.svg">
                            </div>
                        </a>

                        <?php if ($cross_sell_ids): ?>
                            <?php foreach ($cross_sell_ids as $course_id): ?>
                                <a href="<?php the_permalink($course_id); ?>" class="course_link d-flex">
                                    <div class="course_link__text w-100 px-2 d-flex align-items-center"><?= get_the_title($course_id) ?></div>
                                    <div class="course_link__icon bg-blue">
                                        <img src="/wp-content/themes/offshoreutdanning/assets/images/icon-right.svg">
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>


    <div class="container bg-midnightblue" <?= ($main_bg ? 'style="background-color: ' . $main_bg . '"' : ''); ?>>
        <div class="px-2">
            <?php $related_pages = get_field('related_pages');
            if ($related_pages): ?>
                <div class="related-nav border-0">
                    <?php foreach ($related_pages as $post):
                        setup_postdata($post); ?>
                        <!--            --><?php //var_dump($post);
                        ?>
                        <a href="<?php the_permalink(); ?>">
                            <div class="related-nav__item text-center px-2">Kurs
                                for <?php the_title(); ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php wp_reset_postdata(); ?>

                <div class="visit_courses w-100">
                    <a href="produktkategori/kurs/" class="m-0 d-block text-center px-2">Vis kurs
                        liste</a>
                </div>
            <?php endif; ?>


        </div>
    </div>

    <!--    <div class="frontpage-grid-showall-button"><a href="/produktkategori/kurs/">Vis alle kurs</a></div>-->

</div>


<?php get_footer(); ?>
