<?php
add_action('wp_ajax_course_add_to_cart', 'course_add_to_cart');
add_action('wp_ajax_nopriv_course_add_to_cart', 'course_add_to_cart');
function course_add_to_cart()
{
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
    $grouped_course = false;
    $quantity = 1;
    $product = wc_get_product($product_id);

    $ajaxData = json_decode(stripslashes($_POST['data']));

    if (isset($_POST['data']) && $product_id) {
        $grouped_course = true;
        removeItemsFromCart($product_id, $ajaxData[0]->group_type);
        $course_meta = get_post_meta($product_id, 'product_dates', true);

        foreach ($ajaxData as $group) {
            $course_data = getProductData($course_meta[$group->course_year][$group->course_month][$group->course_type][$group->course_index], $grouped_course, $group->group_type);

            if ($product->is_type('variable')) {
                $course_data = updateProductDataWithVariations($course_data, $group->variation_id, $product_id);
            }

            WC()->cart->add_to_cart($product_id, $quantity, '', '', $course_data);
        }
    } else {
        $course_meta = get_post_meta($product_id, 'product_dates', true)[$_POST['course_year']][$_POST['course_month']][$_POST['course_type']][$_POST['course_index']];
        $product_data = getProductData($course_meta, $grouped_course, null);

        if ($product->is_type('variable')) {
            $product_data = updateProductDataWithVariations($product_data, $_POST['variation_id'], $product_id);
        }

        $inactive = false;
        removeItemsFromCart($product_id);

        WC()->cart->add_to_cart($product_id, $quantity, '', '', $product_data);
    }

    $response = [
        'cart_items' => return_get_template_part("template-parts/mini-cart"),
        'header_info' => count(WC()->cart->get_cart_contents()) . ' ' . __('kurs') . ', ' . formatted_price(WC()->cart->cart_contents_total),
        'qty' => count(WC()->cart->get_cart_contents()),
    ];

    echo json_encode((object)$response);

    exit();
}

function removeItemsFromCart($product_id, $group_type = null)
{
    foreach (WC()->cart->get_cart() as $key => $product_item) {
        if ($product_item['product_id'] === intval($product_id) &&
            (($group_type !== 'main' && $product_item['group_type'] !== 'main') ||
                ($group_type !== 'next5' && $product_item['group_type'] !== 'next5'))) {
            WC()->cart->remove_cart_item($key);
        }
    }
}

function getProductData($course_meta, $grouped_course, $group_type = null)
{
    $duration = getCourseDuration($course_meta['startdate'], $course_meta['enddate']);
    return [
        'course_price' => $course_meta['price'],
        'location' => $course_meta['locality'],
        'start' => getCourseDuration(time(), $course_meta['startdate'], 'days'),
        'data_from' => setDatelocale($course_meta['startdate'], 'l d.', $duration),
        'data_to' => ($duration > 0) ? setDatelocale($course_meta['enddate'], 'l d.', $duration) :
            setDatelocale($course_meta['enddate'], 'l d.', $duration) . ' ' . ucfirst(setDatelocale($course_meta['startdate'], 'F', $duration)),
        'data_mounth' => $_POST['course_month'],
        'data_time' => $course_meta['dayphase'],
        'few_seats' => $course_meta['few_seats'],
        'course_id' => $course_meta['course_id'],
        'grouped_course' => $grouped_course,
        'group_type' => $group_type,
    ];
}

function updateProductDataWithVariations($product_data, $variation_ids, $product_id)
{
    $variation_term_id = 'no-practise';
    $variation_prices = [];
    $parsedData = json_decode(stripslashes($variation_ids), true)[$product_id];

//    var_dump($parsedData);
//    exit();

    foreach ($parsedData as $variation_id) {

        $variation = wc_get_product($variation_id);
        $pa_practictype = get_term_by('slug', $variation->get_attribute('pa_practice_type'), 'pa_practice_type');
        $term_mashine = get_term_by('slug', $variation->get_attribute('pa_practice'), 'pa_practice');

        $variation_prices[$term_mashine->name] = $variation->get_price();
        $variation_term_id = $pa_practictype->term_id;
    }

    $product_data['course_price'] += array_sum($variation_prices);
    $product_data['practices'] = $variation_prices;
    $product_data['variation_term_id'] = $variation_term_id;
    $product_data['variation_ids'] = $variation_ids;

    return $product_data;
}
