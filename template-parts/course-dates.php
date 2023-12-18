<?php
$maxLength = null;
if (isset($args)) {
    $maxLength = $args['maxLength'];
    $product = $args['product'];
    $course_dates = $args['course_dates'];
    $summ_variations = $args['summ_variations'];
    $grouped = $args['grouped'];
    $filter = $args['filter'];
}
if (isset($template_args)) {
    $product = $template_args['product'];
    $course_dates = $template_args['course_dates'];
    $summ_variations = $template_args['summ_variations'];
    if ($summ_variations == 'null')
        $summ_variations = 0;
    $grouped = $template_args['grouped'];
    $filter = $template_args['filter'];
}


$length = 0;
$class = $loc = $month_name = $status = '';
$productsInCart = array_combine(
    array_values(wp_list_pluck(WC()->cart->get_cart_contents(), 'product_id')),
    array_values(wp_list_pluck(WC()->cart->get_cart_contents(), 'course_id'))
);


foreach ($course_dates as $year => $months) {
    foreach ($months as $month => $data) {


        echo '<div class="d-flex column-reverse align-unset"><div>';
        $i = 0;

        foreach ($data as $type => $courses) {
            foreach ($courses as $index => $course) {


                $duration = getCourseDuration($course['startdate'], $course['enddate']);

                $course_price = formatted_price($course['price'] + $summ_variations);


                $class = $productsInCart && array_key_exists($product->get_id(), $productsInCart) && (
                    $productsInCart[$product->get_id()] === $course['course_id'] && $grouped == true ||
                    $productsInCart[$product->get_id()] === $course['course_id']
                ) ? 'active' : '';

                $locality = $course['locality'];
                $location_slug = preg_replace('([ +\/])', '', strtolower($locality));
                $date_structure = local_date_i18n("d", $course['startdate']) . 'til' . local_date_i18n("d", $course['enddate']) . '-' . $month . '-' . $year;


                if ($filter === $location_slug || $filter == 'null' || is_null($filter)) {
                    if (!is_null($maxLength))
                        if ($length === $maxLength) break;

                    echo '<div class="calendar-info">';
                    echo '<div class="calendar-month-location d-flex justify-between">';
                    echo '<div class="calendar-month fw-bold ' . ($month_name === $month ? 'empty' : '') . '">' . ($month_name != $month ? ucfirst(setDatelocale(mktime(0, 0, 0, $month, 10, $year), 'F Y')) : '') . '</div>';
                    if ($loc != $locality) echo '<div class="calendar-location">' . $locality . '</div>';
                    echo '</div></div>';

                    echo '<div class="calendar-item d-flex align-unset rounded7 ' . $class . '" id="' . $course['course_id'] . '" data-year="' . $year . '" data-month="' . $month . '" data-type="' . $type . '" data-index="' . $index . '">';
                    echo '<div class="calendar-item__status ' . ($class ? 'bg-pink' : 'bg-gray') . ' d-flex"><img src="' . get_stylesheet_directory_uri() . '/assets/images/icon-check.svg" loading="lazy" alt="check"></div>';
                    echo '<div class="calendar-item__date px-1 btn m-0 bg-light-1 ' . (($course['few_seats'] === true || $course['dayphase'] !== 'day') ? 'd-flex' : '') . '">' . setDatelocale($course['startdate'], 'l d.', $duration) . ' til ' . setDatelocale($course['enddate'], 'l d.', $duration) . '';

                    if ($course['few_seats'] === true || $course['dayphase'] !== 'day') {
                        echo '<div class="date-info d-flex align-center">';
                        if ($course['few_seats'] === true) echo '<div class="bg-danger text-light text-center">' . __("Få plasser") . '</div>';
                        if ($course['dayphase'] === 'evening') echo '<div class="bg-secondary text-light text-center">' . __("Kveld") . '</div>';
                        echo '</div>';
                    }

                    echo '</div>';
                    echo '<div class="calendar-item__time p-2 bg-primary text-light d-flex">' . getDayphase($course['dayphase']) . '</div>';
                    echo '<div class="calendar-item__price p-2 bg-primary text-light d-flex">' . $course_price . '</div>';
                    echo '<div class="calendar-item__link bg-softdark d-flex"><a href="' . get_the_permalink($product->get_id()) . strtolower($location_slug) . '/' . $date_structure . '" class="d-flex"><img src="' . get_stylesheet_directory_uri() . '/assets/images/icon-info-2.svg" width="17" height="17" loading="lazy" alt="icon-info"></a></div></div>';

                    $month_name = $month;
                    $length++;
                }
                $loc = $locality;

//                $EducationEvent = [
//                    '@context' => "https://schema.org",
//                    '@type' => "EducationEvent",
//                    '@id' => get_the_permalink($product->get_id()) . strtolower($location_slug) . '/' . $date_structure,
//                    'url' => get_the_permalink($product->get_id()) . strtolower($location_slug) . '/' . $date_structure,
//                    'name' => get_the_title($product->get_id()),
//                    'description' => get_the_excerpt(),
//                    'location' => ['@type' => 'Place', 'name' => $locality, 'address' => $locality],
//                    'offers' => [
//                        '@type' => 'Offer',
//                        'availability' => 'InStock',
//                        'price' => str_replace('.00', '', $course['price'] + $summ_variations),
//                        'priceCurrency' => get_woocommerce_currency(),
//                        'url' => get_the_permalink($product->get_id()) . strtolower($location_slug) . '/' . $date_structure,
//                    ],
//                    'startDate' => date_format(date_create('@' . $course['startdate']), 'c'),
//                    'endDate' => date_format(date_create('@' . $course['enddate']), 'c'),
//                    'eventStatus' => 'EventScheduled',
//                    'image' => ['@type' => 'ImageObject', 'url' => get_the_post_thumbnail_url($product->get_id(), 'course-thumbnail')],
//                ];
//
//                array_push($schema, $EducationEvent);
            }
            $i++;
        }
        echo '</div></div>';
    }
} ?>

<?php if ($length >= 10): ?>
    <div class="load-more-dates mt-3 text-dark justify-center d-flex align-center text-decoration"><?= __('Vis flere kursdatoer'); ?></div><?php endif; ?>