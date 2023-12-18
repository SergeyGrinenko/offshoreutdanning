<?php get_header();
global $schema;
$product = get_page_by_path(get_query_var('kursname'), null, 'product');
$course_location = rawurldecode(get_query_var('course_location'));
$course_date = get_query_var('course_date');
$course_dates = get_post_meta($product->ID, 'product_dates', true);
$query_var = explode('-', $course_date);
$query_date = explode('to', $query_var[0]);
$data = [];
$schema = [];
$type_id = null;
$index_id = null;
if ($course_dates[$query_var[2]][$query_var[1]]) {
    foreach ($course_dates[$query_var[2]][$query_var[1]] as $type => $courses) {
        foreach ($courses as $index => $course) {
            $date_structure = local_date_i18n("d", $course['startdate']) . 'til' . local_date_i18n("d", $course['enddate']) . '-' . $query_var[1] . '-' . $query_var[2];
            if ($date_structure === $course_date && $course_location === strtolower(str_replace(' / ', '', $course['locality']))) {
                $data = $course;
                $type_id = $type;
                $index_id = $index;
            }
        }
    }
} ?>

<div id="<?= $product->ID; ?>" class="single-course single-course-date">
    <div class="bg-light-1 course-title-wrapper">
        <div class="container position-relative py-0">
            <div class="grid-6-4 align-center">
                <div>
                    <?php if ($data['few_seats'] === true): ?>
                        <div class="few_seats-badge rounded7 bg-danger p-2 px-3 text-light"><?= __('Få plasser'); ?></div>
                    <?php endif; ?>
                    <div class="dayphase-badge rounded7 bg-primary p-2 px-3 text-light mt-1"><?= getDayphase($data['dayphase']); ?></div>
                    <h1 class="my-1 text-dark fw-bold"><?= get_the_title($product->ID); ?></h1>
                    <?php if (get_the_excerpt()): ?>
                        <div class="text-dark"><?php the_excerpt(); ?></div>
                    <?php endif; ?>

                </div>
                <?php if (has_post_thumbnail($product->ID)): ?>
                    <img src="<?= get_the_post_thumbnail_url($product->ID, 'medium_large'); ?>" width="100%">
                <?php else: ?>
                    <img src="<?= wc_placeholder_img_src('course-thumbnail'); ?>" width="100%">
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container">
        <?php if ($data) { ?>
            <?php $duration = getCourseDuration($data['startdate'], $data['enddate']); ?>
            <div class="single-course-date__card p-3">
                <table class="card-table w-100">
                    <tbody>
                    <tr>
                        <td class="card-table__label"><?= __('Kurs:') ?></td>
                        <td><?= get_the_title($product->ID); ?></td>
                    </tr>
                    <tr>
                        <td class="card-table__label"><?= __('Sted:') ?></td>
                        <td><?= $data['locality']; ?></td>
                    </tr>
                    <tr>
                        <td class="card-table__label"><?= __('Dato:') ?></td>
                        <td>
                            <?= setDatelocale($data['startdate'], 'F d l', $duration) . ' ' . __('til') . ' ' . setDatelocale($data['enddate'], 'F d l, Y', $duration) ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="card-table__label"><?= __('Pris:') ?></td>
                        <td><?= formatted_price($data['price']); ?></td>
                    </tr>
                    </tbody>
                </table>
                <div class="w-100 text-center" data-year="<?= $query_var[2]; ?>"
                     data-month="<?= $query_var[1]; ?>"
                     data-type="<?= $type_id; ?>"
                     data-index="<?= $index_id; ?>">
                    <a class="single-date bg-pink text-light px-3 w-100 py-2 mt-3 d-block rounded7"><?= __('Påmelding'); ?></a>
                </div>
            </div>
            <div class="px-3 text-center">
                <a href="<?php the_permalink($product->ID); ?>"
                   class="single-date bg-dark text-light px-3 w-100 py-2 mt-3 d-block rounded7"
                   id="<?= $data['course_id']; ?>">
                    <?= __('Se alle datoer og steder'); ?>
                </a>
            </div>


            <?php if (have_rows('accordion')): ?>
                <div class="singlecourse__table expand mt-3" id="anchor-kurspraktisk"
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

        <?php } else { ?>
            <div class="not-found p-1 bg-danger text-light"><?= __('Beklager, datoene ble ikke funnet.') ?></div>
        <?php } ?>
    </div>
</div>
<?php
if (date_create('@' . $data['startdate'])) {
    $EducationEvent = array(
        '@context' => "https://schema.org",
        '@type' => "EducationEvent",
        '@id' => get_the_permalink($product->ID) . strtolower($course_location) . '/' . $date_structure,
        'url' => get_the_permalink($product->ID) . strtolower($course_location) . '/' . $date_structure,
        'name' => get_the_title($product->ID),
        'description' => get_the_excerpt(),
        'location' => array(
            '@type' => 'Place',
            'name' => $course['locality'],
            'address' => $course['locality'],
        ),
        'offers' => array(
            '@type' => 'Offer',
            'availability' => 'InStock',
            'price' => str_replace('.00', '', $data['price']),
            'priceCurrency' => get_woocommerce_currency(),
            'url' => get_the_permalink($product->ID) . strtolower($course_location) . '/' . $date_structure,
        ),
        'startDate' => date_format(date_create('@' . $data['startdate']), 'c'),
        'endDate' => date_format(date_create('@' . $data['enddate']), 'c'),
        'eventStatus' => 'EventScheduled',
        'image' => array(
            '@type' => 'ImageObject',
            'url' => get_the_post_thumbnail_url($product->ID, 'course-thumbnail'),
        ),
    );
    array_push($schema, $EducationEvent);
} ?>
<?php get_footer(); ?>
