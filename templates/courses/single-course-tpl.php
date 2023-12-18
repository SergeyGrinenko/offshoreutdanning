<?php
global $schema;
global $product;
const COURSE_PLACE = 'kurssted';
$schema = [];
$product = wc_get_product(get_the_ID());
$video_thumbnail = get_field('thumbnail');
$video = get_field('video');
$video_caption = get_field('description');
$course_dates = get_post_meta($product->get_id(), 'product_dates', true);
$CourseLocations = get_course_locations($course_dates);
$grouped = $filter = null;
$summ_variations = 0;
$grid_area = 1;
$maxLength = 10;
$kurssted = rawurldecode(get_query_var('course_location'));
$course_type = array_combine(
    array_values(wp_list_pluck(WC()->cart->get_cart_contents(), 'product_id')),
    array_values(wp_list_pluck(WC()->cart->get_cart_contents(), 'grouped_course'))
);

//if (array_key_exists($product->get_id(), $course_type) && $course_type[$product->get_id()] === false)
//    $grouped = true;

if (!empty($kurssted)) {
    $product = wc_get_product(get_page_by_path(get_query_var('kursname'), null, 'product'));
    $course_dates = get_post_meta($product->get_id(), 'product_dates', true);
    $CourseLocations = get_course_locations($course_dates);
    $filter = rawurldecode(get_query_var('course_date'));
} ?>

<div id="<?= $product->get_id(); ?>" <?= (!is_null($filter) ? 'data-location="' . $filter . '"' : '') ?> <?php wc_product_class('single-course' . ($product->is_type('variable') === true ? ' has-practice' : '') . '', $product); ?>>
    <div class="bg-light-1 course-title-wrapper">
        <div class="container py-0 position-relative">
            <div class="grid-6-4 align-center text-dark">
                <div>
                    <h1 class="fw-bold"><?= get_the_title($product->get_id()); ?></h1>
                    <?php if (get_the_excerpt()): ?>
                        <p><?php the_excerpt(); ?></p>
                    <?php endif; ?>

                    <div class="anchors_links">
                        <a href="#anchor-dates"
                           class="fw-normal text-decoration text-dark"><?php _e('Velg dato'); ?></a>
                        <?php if (!empty(get_the_content())): ?>
                            <a href="#anchor-kusinfo"
                               class="fw-normal text-decoration text-dark"><?php _e('Kursbeskrivelse'); ?></a>
                        <?php endif; ?>
                        <?php if (get_field('table')): ?>
                            <a href="#anchor-kurspraktisk"
                               class="fw-normal text-decoration text-dark"><?php _e('Praktisk info'); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php $thumbnail_url = has_post_thumbnail($product->get_id()) ? get_the_post_thumbnail_url($product->get_id(), 'medium_large') : wc_placeholder_img_src('course-thumbnail'); ?>
                <img src="<?= $thumbnail_url; ?>" alt="course-thumbnail" width="100%" height="100%" loading="lazy">
            </div>
        </div>
    </div>

    <div class="container">
        <div class="grid-6-4 course-content position-relative">

            <div class="calendar-header__mobile p-3 bg-softdark text-light text-center">
                <h2 class="my-0"><?php _e('Bestill kurs'); ?></h2>
                <h3 class="my-0"><?php _e('Velg dato nedenfor'); ?></h3>
            </div>

            <?php if ($video): ?>
                <div class="embed-course-video" style="grid-area: <?= $grid_area; ?>">
                    <div class="embed-course-video__wrapper d-flex align-center">
                        <img src="<?= $video_thumbnail ?: get_template_directory_uri() . '/assets/images/video-preview.png'; ?>"
                             class="video-preview js-modal" data-modal="#modal_1" alt="video-preview" width="210"
                             height="119">

                        <?php if ($video_caption): ?>
                            <div><?= $video_caption; ?></div>
                        <?php endif; ?>
                    </div>

                    <div id="modal_1" class="modal-window">
                        <a href="#" class="modal-close">
                            <img src="<?= get_template_directory_uri() . '/assets/images/icon-close.svg'; ?>"
                                 width="40">
                        </a>
                        <div class="modal-window__title">
                            <?= do_shortcode('[videojs_video url="' . $video . '" preload="none"]'); ?>
                        </div>
                    </div>

                    <div class="modal-window__backdrop hidden" id="modal-backdrop"></div>
                </div>
                <?php $grid_area = $grid_area + 1 ?>
            <?php endif; ?>

            <div class="singlecourse__steps courseSteps <?= !have_rows('steps') ? 'hide' : '' ?>"
                 style="grid-area: <?= $grid_area; ?>">
                <div class="courseStep">
                    <?php $i = 1; ?>
                    <?php if (have_rows('steps')): ?>
                        <?php $rowCount = count(get_field('steps')); //GET THE COUNT ?>
                        <?php while (have_rows('steps')) : the_row(); ?>
                            <div>
                                <h3 class="courseStep__heading mb-1 mt-0"><?php the_sub_field('step_title') ?></h3>
                                <?php if (have_rows('courses')): ?>
                                    <div class="courseStep__item d-flex mb-3">
                                        <?php while (have_rows('courses')) : the_row();
                                            $short_title = get_sub_field('short_title');
                                            $short_description = get_sub_field('short_description');
                                            $course_link = get_sub_field('course') ? get_sub_field('course') : '#';
                                            $item_status = get_sub_field('status');
                                            $course_category = array_shift(get_sub_field_object('course_category')['value']); ?>
                                            <a href="<?= $course_link; ?>"
                                               class="courseStep__option w-100 btn m-0 text-dark bg-light-1 rounded7 <?= $course_category === 'yes' ? 'courseStep_cat' : '' ?> <?= $item_status === true ? 'active' : '' ?>"><?= $short_title; ?></a>
                                        <?php endwhile; ?>
                                    </div>
                                <?php endif; ?>

                                <!--                                --><?php //if (count($CourseLocations) > 1 && get_sub_field('step_title') == 'Velg lokasjon'): ?>
                                <!--                                    <div class="singlecourse__locations inner" style="grid-area: -->
                                <?php //= $grid_area; ?><!--">-->
                                <!--                                        <ul class="filter-locations">-->
                                <!--                                            <li class="filter-locations__item bg-light-1 m-0 rounded7 -->
                                <?php //= is_null($filter) ? 'active fw-bold' : '' ?><!--"-->
                                <!--                                                value="all">-->
                                <!--                                                <a href="-->
                                <?php //= get_the_permalink($product->get_id()); ?><!--"-->
                                <!--                                                   class="d-flex btn align-unset justify-between text-dark">-->
                                <!--                                                    <div class="item__info">Vis alle datoer</div>-->
                                <!--                                                </a>-->
                                <!--                                            </li>-->
                                <!--                                            --><?php //foreach ($CourseLocations as $location_name):
                                //                                                $location_slug = rawurldecode(preg_replace('([ +\/])', '', strtolower($location_name)));
                                //                                                ?>
                                <!--                                                <li class="filter-locations__item bg-light-1 m-0 rounded7 -->
                                <?php //= $location_slug === $filter ? 'active fw-bold' : '' ?><!--"-->
                                <!--                                                    value="-->
                                <?php //= $location_slug ?><!--">-->
                                <!--                                                    <a href="-->
                                <?php //= get_the_permalink($product->get_id()) . COURSE_PLACE . '/' . $location_slug ?><!--"-->
                                <!--                                                       class="d-flex btn align-unset justify-between text-dark">-->
                                <!--                                                        <div class="item__info">-->
                                <?php //= $location_name ?><!--</div>-->
                                <!--                                                    </a>-->
                                <!--                                                </li>-->
                                <!--                                            --><?php //endforeach; ?>
                                <!--                                        </ul>-->
                                <!--                                    </div>-->
                                <!--                                --><?php //endif; ?>
                            </div>
                            <?php $i++; ?>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php $grid_area = $grid_area + 1 ?>

            <?php if (count($CourseLocations) > 1): ?>

                <div class="singlecourse__locations"
                     style="grid-area: <?= $grid_area; ?>">
                    <h3 class="courseStep__heading mb-1 mt-0 fw-bold"><?php _e('Velg lokasjon'); ?></h3>
                    <ul class="filter-locations">
                        <li class="filter-locations__item bg-light-1 m-0 rounded7 <?= is_null($filter) ? 'active fw-bold' : '' ?>"
                            value="all">
                            <a href="<?= get_the_permalink($product->get_id()); ?>"
                               class="d-flex btn align-unset justify-between text-dark">
                                <div class="item__info"><?php _e('Vis alle datoer'); ?></div>
                            </a>
                        </li>
                        <?php foreach ($CourseLocations as $location_name):
                            $location_slug = rawurldecode(preg_replace('([ +\/])', '', strtolower($location_name)));
                            ?>
                            <li class="filter-locations__item bg-light-1 m-0 rounded7 <?= $location_slug === $filter ? 'active fw-bold' : '' ?>"
                                value="<?= $location_slug ?>">
                                <a href="<?= get_the_permalink($product->get_id()) . COURSE_PLACE . '/' . $location_slug ?>"
                                   class="d-flex btn align-unset justify-between text-dark">
                                    <div class="item__info"><?= $location_name ?></div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php $grid_area = $grid_area + 1 ?>
            <?php endif; ?>

            <?php if ($product->is_type('variable')) { ?>
                <?php get_template_part('template-parts/variations', null, ['grid_area' => $grid_area, 'summ_variations' => $summ_variations]); ?>
                <?php $grid_area = $grid_area + 1 ?>
            <?php } ?>

            <?php if (!empty(get_the_content())): ?>
                <div class="singlecourse__content" id="anchor-kusinfo"
                     style="grid-area: <?= $grid_area + 1; ?>"><?php the_content(); ?></div>
                <?php $grid_area = $grid_area + 1 ?>
            <?php endif; ?>

            <?php if (get_field('visibility')): ?>
                <?php $left_side = get_field('left_side');
                $right_side = get_field('right_side'); ?>
                <div class="singlecourse__responsibility expand container-sm" style="grid-area: <?= $grid_area + 1; ?>">
                    <div class="responsibility">
                        <div class="grid-6-4">
                            <div>
                                <?php if (!empty($left_side['title'])): ?>
                                    <h3><?= $left_side['title']; ?></h3>
                                <?php endif; ?>

                                <?php if (!empty($left_side['description'])): ?>
                                    <p><?= $left_side['description']; ?></p>
                                <?php endif; ?>

                                <?php if ($left_side['image']): ?>
                                    <img src="<?= $left_side['image']; ?>" alt="image" width="155"
                                         class="responsibility-subimage">
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php if ($right_side['main_thumbnail']): ?>
                                    <img src="<?= $right_side['main_thumbnail'] ?>" alt="responsibility-thumbnail"
                                         width="283" class="responsibility-thumbnail">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php $grid_area = $grid_area + 1; ?>

            <?php if (have_rows('accordion')): ?>
                <div class="singlecourse__table expand container-sm w-100 mt-3" id="anchor-kurspraktisk"
                     style="grid-area: <?= $grid_area + 1; ?>">
                    <ul id="accordion" class="accordion main m-0">
                        <?php while (have_rows('accordion')) : the_row();
                            $tab_name = get_sub_field('tab_name');
                            $tab_content = get_sub_field('tab_content'); ?>
                            <li class="bg-light-1">
                                <div class="category-name d-flex align-center justify-between"><?= $tab_name; ?>
                                    <img src="<?= get_stylesheet_directory_uri() . '/assets/images/icon-plus.svg'; ?>"
                                         alt="circle-arrow" width="26.67">
                                </div>

                                <div class="accordion-content m-0"><?= $tab_content; ?></div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="singlecourse__dates course-calendar" id="anchor-dates" style="grid-area: <?= $grid_area - 2 ?>">

                <div class="calendar-header p-3 bg-softdark text-light text-center">
                    <h2 class="my-0"><?php _e('Bestill kurs'); ?></h2>
                    <h3 class="my-0"><?php _e('Velg dato nedenfor'); ?></h3>
                </div>

                <div class="py-3 calendar-dates">
                    <?php echo get_template_part('template-parts/course-dates', false, [
                        'maxLength' => $maxLength,
                        'course_dates' => $course_dates,
                        'product' => $product,
                        'summ_variations' => $summ_variations,
                        'grouped' => $grouped,
                        'filter' => $filter,
                    ]);

                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>