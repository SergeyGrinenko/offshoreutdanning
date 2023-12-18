<?php
defined('ABSPATH') || exit;

global $product;
global $woocommerce;
global $schema;
$curent_url = rawurldecode($_SERVER['REQUEST_URI']);
$schema = array();

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked wc_print_notices - 10
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
    echo get_the_password_form(); // WPCS: XSS ok.
    return;
}


global $wp_query;
$get_course_location = $wp_query->query_vars['course_location'];
$get_course_date = $wp_query->query_vars['course_date'];


$all_location = array();
$all_dates = array();
if (have_rows('custom_products')):

    $dates = array();

    while (have_rows('custom_products')) : the_row();
        $loc = get_sub_field('course_location');
        $date_from = get_sub_field('course_date_from');
        $date_to = get_sub_field('course_date_to');
        $getDate = parse_date(strtotime($date_to));
        $locations[$loc] = $loc;

        $array = [
            'from' => $date_from,
            'to' => $date_to,
            'month_num' => $getDate['month_number'],
        ];
        array_push($dates, $array);

    endwhile;

    foreach ($locations as $item):
        array_push($all_location, strtolower(str_replace(' / ', "-", $item)));
    endforeach;

    foreach ($dates as $date) {
        $date_structure = parse_date(strtotime($date['from']))['number'] . 'to' . parse_date(strtotime($date['to']))['number'] . '-' . parse_date(strtotime($date['to']))['month_number'] . '-' . parse_date(strtotime($date['to']))['year'];

        array_push($all_dates, trim($date_structure));
    }

endif;


if (!in_array(rawurldecode($get_course_location), $all_location) || !in_array(rawurldecode($get_course_date), $all_dates)) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part(404);

    exit();
} else {

    $course_data_array = array();
    $id = 0;
    while (have_rows('custom_products')) : the_row();
        $loc = get_sub_field('course_location');
        $date_from = get_sub_field('course_date_from');
        $date_to = get_sub_field('course_date_to');
        $price = get_sub_field('course_price');
        $status = get_sub_field('course_status');
        $course_time = get_sub_field('course_time');
        if ($course_time === 'day') {
            $course_time = 'Dagtid';
        } else if ($course_time === 'evening') {
            $course_time = 'Kveld';
        }
        $few_seats = get_sub_field('few_seats');

        $row_id = $id;

        $date_structure = parse_date(strtotime($date_from))['number'] . 'to' . parse_date(strtotime($date_to))['number'] . '-' . parse_date(strtotime($date_to))['month_number'] . '-' . parse_date(strtotime($date_to))['year'];


        if (trim($date_structure) === rawurldecode($get_course_date) && str_replace(' / ', "-", rawurldecode($loc)) === ucwords(rawurldecode($get_course_location), "-")) :

            $course_data = [
                'from' => $date_from,
                'to' => $date_to,
                'month_num' => parse_date(strtotime($date_to))['month_number'],
                'row_id' => $row_id,
                'price' => $price,
                'location' => $loc,
                'status' => $status,
                'course_time' => $course_time,
                'few_seats' => $few_seats,
                'struct_url' => get_the_permalink() . trim($date_structure),
            ];

            array_push($course_data_array, $course_data);
        endif;
        $id++;
    endwhile;


    if ($course_data_array): ?>
    <div id="product-<?php the_ID(); ?>" <?php wc_product_class('', $product); ?>>

        <input type="hidden" class="product-id" value="<?php the_ID(); ?>">
        <input type="hidden" class="currency" value="<?php echo get_woocommerce_currency_symbol(); ?>">

        <?php $status = get_post_meta(get_the_ID(), '_wporg_meta_key', true); ?>
        <?php if ($status == 'disabled'): ?>
            <div class="fluid_container mt-5">
                <div class="col-full">
                    <div class="p-2 bg-red white">Beklager, dette kurset er ikke
                        tilgjengelig.
                    </div>
                </div>
            </div>
        <?php else: ?>

            <div id="page-lead-row">
                <div class="page-lead">
                    <div class="page-lead-text-wrapper">
                        <div class="page-lead-text">
                            <h1>
                                <?php $custom_title = get_field('custom_course_title');
                                if (!empty($custom_title)) {
                                    echo $custom_title;
                                } else {
                                    the_title();
                                } ?>
                            </h1>
                            <p><?php echo get_the_excerpt(); ?></p>
                        </div>
                    </div>
                    <div class="page-lead-image"
                         style="background:url(<?php echo get_the_post_thumbnail_url(get_the_ID(), 'product_thumbnail'); ?>) center right / cover  no-repeat;"></div>
                </div>
            </div>
            <?php foreach (array_unique($course_data_array) as $course_data): ?>


                <?php $diff = abs(parse_date(strtotime($course_data['from']))['month_number'] - parse_date(strtotime($course_data['to']))['month_number']); ?>

                <div class="px-2">
                    <div class="singleevent-text" data-id="<?php echo get_the_ID(); ?>"
                         data-row-id="<?php echo $course_data['row_id']; ?>">
                        <?php if ($course_data['few_seats'] == 1): ?>
                            <span class="few_seats white">Få plasser</span>
                        <?php endif; ?>
                        <h2>
                            <?php $custom_title = get_field('custom_course_title');
                            if (!empty($custom_title)) {
                                echo $custom_title;
                            } else {
                                the_title();
                            } ?>
                        </h2>


                        <table>
                            <tr>
                                <td>Kurs:</td>
                                <td>
                                    <?php $custom_title = get_field('custom_course_title');
                                    if (!empty($custom_title)) {
                                        echo $custom_title;
                                    } else {
                                        the_title();
                                    } ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Sted:</td>
                                <td> <?php echo $course_data['location']; ?></td>
                            </tr>
                            <tr>
                                <td>Dato:</td>
                                <td>
                                    <?php if ($diff > 2): ?>
                                        <?php
                                        $course_date = parse_date(strtotime($course_data['from']), 'd F');
                                        $course_date_to = parse_date(strtotime($course_data['to']), 'd F - Y');
                                        ?>
                                        <?php echo $course_date . ' til ' . $course_date_to; ?>
                                    <?php else: ?>
                                        <?php
                                        $course_date = parse_date(strtotime($course_data['from']), 'd l');
                                        $course_date_to = parse_date(strtotime($course_data['to']), 'd l, F - Y');
                                        ?>
                                        <?php echo $course_date . ' til ' . $course_date_to; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Pris:</td>
                                <td><?php echo str_replace(',00', ',-', wc_price($course_data['price'])); ?></td>
                            </tr>
                        </table>

                        <div class="singleevent-button" data-row-id="<?php echo $course_data['row_id']; ?>"
                             data-id="<?php echo get_the_ID(); ?>" data-term="dont_need">
                            Påmelding
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
            <div class="px-2">
                <div class="singleevent-buttonback-wrapper">
                    <a href="<?php echo get_the_permalink(); ?>" class="singleevent-button">Se alle datoer og steder</a>
                </div>

                <div class="grid-div-singlecourse-table singleevent-table">
                    <?php echo get_field('table') ?>
                </div>
            </div>


            <?php

            $event = array(
                '@context' => "https://schema.org",
                '@type' => "EducationEvent",
                '@id' => $course_data_array[0]['struct_url'],
                'url' => $course_data_array[0]['struct_url'],
                'name' => $custom_title,
                'description' => get_the_excerpt(),
                'location' => array(
                    '@type' => 'Place',
                    'name' => $course_data_array[0]['location'],
                    'address' => $course_data_array[0]['location'],
                ),
                'offers' => array(
                    '@type' => 'Offer',
                    'availability' => 'InStock',
                    'price' => str_replace('.00', '', $course_data_array[0]['price']),
                    'priceCurrency' => get_woocommerce_currency(),
                    'url' => $course_data_array[0]['struct_url'],
                    'validFrom' => $course_data_array[0]['from'],
                ),
                'startDate' => $course_data_array[0]['from'].' 08:00:00.000',
                'endDate' => $course_data_array[0]['to'].' 08:00:00.000',
                'eventStatus' => 'EventScheduled',
                'image' => array(
                    '@type' => 'ImageObject',
                    'url' => get_the_post_thumbnail_url(get_the_ID(), 'product_thumbnail'),
                ),

            );
            array_push($schema, $event); ?>

        <?php endif; ?>

    <?php else:
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        get_template_part(404);
        exit();
    endif;

} ?>