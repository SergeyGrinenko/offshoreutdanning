<?php
// Ajax load more on single course
add_action('wp_ajax_load_dates', 'load_dates');
add_action('wp_ajax_nopriv_load_dates', 'load_dates');
function load_dates()
{
    global $woocommerce;
    $current_location = $_POST['current_location'];
    $cart_items = $woocommerce->cart->get_cart();

    $cart_array = array();
    foreach ($cart_items as $cart_item) :
        $cart_row_id = $cart_item['row_id'];
        $cart_product_id = $cart_item['product_id'];

        array_push($cart_array, array(
            'row_id' => $cart_row_id,
            'product_id' => $cart_product_id
        ));
    endforeach;

    $product_id = $_POST['product_id'];

    $repeater = get_field('custom_products', $product_id);

    if ($repeater) {

        $order = array();

        foreach ($repeater as $i => $row) {
            $order[$i] = $row['course_date_from'];
        }

        $row_id = 0;
        foreach ($repeater as &$item) {
            $item['row_id'] = $row_id;
            $row_id++;
        }

        array_multisort($order, SORT_ASC);
        $actual_loop = [];

        foreach ($repeater as $row) {
            $getDate = parse_date(strtotime($row['course_date_from']));


            if (!isset($actual_loop[$getDate['month_number']])) {

                $actual_loop[$getDate['month_number']] = [
                    'date' => $getDate['month_number'],
                    'number' => $getDate['month_number'],
                    'rows' => []
                ];
            }

            $actual_loop[$getDate['month_number']]['rows'][] = $row;
        }

        $mouth = '';
        $sortArray = array();
        foreach ($actual_loop as $person) {
            foreach ($person as $key => $value) {
                if (!isset($sortArray[$key])) {
                    $sortArray[$key] = array();
                }
                $sortArray[$key][] = $value;
            }
        }


        $orderby = "number";
        array_multisort($sortArray[$orderby], SORT_ASC, $actual_loop);

        foreach ($actual_loop as $loop) {

            if (idate("m") <= $loop['number']) {


                $getDate = parse_date(strtotime($loop['rows'][0]['course_date_from']));

                echo '<div data-num="' . $loop['number'] . '">';
                if ($loop['date'] !== $mouth):
                    echo '<h3 class="calendar-month">' . $getDate['month'] . ' ' . $getDate['year'] . '</h3>';
                endif;

                $loc = '';
                echo '<div class="product_year_locations">';
                $arr_start = $loop['rows'];
                $keys = array_column($arr_start, 'course_location');
                $keys2 = array_column($arr_start, 'course_date_from');


                array_multisort($keys2, SORT_ASC, SORT_NUMERIC, array_multisort($keys, SORT_ASC, $arr_start));

                foreach ($arr_start as $row) {
                    $date_structure = parse_date(strtotime($row['course_date_from']))['number'] . 'to' . parse_date(strtotime($row['course_date_to']))['number'] . '-' . parse_date(strtotime($row['course_date_to']))['month_number'] . '-' . parse_date(strtotime($row['course_date_to']))['year'];

                    $course_time = $row['course_time'];
                    if ($course_time === 'day') {
                        $course_time = 'Dagtid';
                    } else if ($course_time === 'evening') {
                        $course_time = 'Kveld';
                    }
                    $course_price = str_replace('.00', '', $row['course_price']);
                    $location = $row['course_location'];
                    $row_id = $row['row_id'];
                    $few_seats = $row['few_seats'];
                    $status = $row['course_status'];
                    $deadline = $row['course_deadline'];
                    $current_date = new DateTime(Date('Y-m-d'));
                    $deadline_date = DateTime::createFromFormat('d/m/Y H:i:s', $deadline . ' 00:00:00');

                    $parse_location = preg_replace("/[\s+\/]/", '', $location);

                    ?>

                    <?php if ($status === 'active' && $current_date <= $deadline_date->modify('+3 day')): ?>
                        <?php if (!empty($current_location)) : ?>
                            <?php if ($current_location === strtolower($parse_location)): ?>

                                <?php if ($location != $loc):
                                    echo '<h3 class="calendar-location">' . $location . '</h3>';
                                endif; ?>

                                <?php $diff = abs(parse_date(strtotime($row['course_date_from']))['month_number'] - parse_date(strtotime($row['course_date_to']))['month_number']); ?>


                                <div class="calendar-date" data-id="<?php echo $product_id; ?>"
                                     data-row-id="<?php echo $row_id; ?>"
                                     data-row-location="<?php echo $location; ?>">
                                                        <span class="daterange" data-row-id="<?php echo $row_id; ?>"
                                                              data-row-location="<?php echo $location; ?>">

                                                           <?php if ($diff > 2): ?>
                                                               <?php
                                                               $course_date = parse_date(strtotime($row['course_date_from']), 'd. F');
                                                               $course_date_to = parse_date(strtotime($row['course_date_to']), 'd. F');
                                                               ?>
                                                               <?php echo $course_date . ' til ' . $course_date_to; ?>
                                                           <?php else: ?>
                                                               <?php
                                                               $course_date = parse_date(strtotime($row['course_date_from']), 'l d.');
                                                               $course_date_to = parse_date(strtotime($row['course_date_to']), 'l d.');
                                                               ?>
                                                               <?php echo $course_date . ' til ' . $course_date_to; ?>
                                                           <?php endif; ?>

                                                           <div class="course_info">
                                                           <?php if ($course_time !== 'Dagtid'): ?>
                                                               <p class="m-0"><?= $course_time; ?></p>
                                                           <?php endif; ?>
                                                               <?php if ($few_seats == 1): ?>
                                                                   <p class="m-0 few_seats">Få plasser</p>
                                                               <?php endif; ?>
                                                        </div>
                                                        </span>

                                    <span class="timeofday text-center" data-row-id="<?php echo $row_id; ?>"
                                          data-row-location="<?php echo $location; ?>">
                                    <?php echo $course_time; ?>
                                </span>
                                    <span class="price" data-default-price="<?php echo $course_price; ?>"
                                          data-row-id="<?php echo $row_id; ?>"
                                          data-row-location="<?php echo $location; ?>"><?php echo str_replace(',00', ',-', wc_price($course_price)); ?></span>

                                    <a href="<?php echo get_the_permalink($product_id) . str_replace(' / ', "-", strtolower($location)) . '/' . trim($date_structure); ?>"
                                       class="product_url text-center p-1 pt-2 pb-2 m-0 bg-darkgrey white">
                                    </a>
                                </div>

                            <?php endif; ?>

                        <?php else: ?>
                            <?php if ($location != $loc):
                                echo '<h3 class="calendar-location">' . $location . '</h3>';
                            endif; ?>

                            <?php $diff = abs(parse_date(strtotime($row['course_date_from']))['month_number'] - parse_date(strtotime($row['course_date_to']))['month_number']); ?>
                            <div class="calendar-date" data-id="<?php echo $product_id; ?>"
                                 data-row-id="<?php echo $row_id; ?>"
                                 data-row-location="<?php echo $location; ?>">
                                                        <span class="daterange" data-row-id="<?php echo $row_id; ?>"
                                                              data-row-location="<?php echo $location; ?>">

                                                             <?php if ($diff > 2): ?>
                                                                 <?php
                                                                 $course_date = parse_date(strtotime($row['course_date_from']), 'd. F');
                                                                 $course_date_to = parse_date(strtotime($row['course_date_to']), 'd. F');
                                                                 ?>
                                                                 <?php echo $course_date . ' til ' . $course_date_to; ?>
                                                             <?php else: ?>
                                                                 <?php
                                                                 $course_date = parse_date(strtotime($row['course_date_from']), 'l d.');
                                                                 $course_date_to = parse_date(strtotime($row['course_date_to']), 'l d.');
                                                                 ?>
                                                                 <?php echo $course_date . ' til ' . $course_date_to; ?>
                                                             <?php endif; ?>

                                                           <div class="course_info">
                                                           <?php if ($course_time !== 'Dagtid'): ?>
                                                               <p class="m-0"><?= $course_time; ?></p>
                                                           <?php endif; ?>
                                                               <?php if ($few_seats == 1): ?>
                                                                   <p class="m-0 few_seats">Få plasser</p>
                                                               <?php endif; ?>
                                                        </div>
                                                        </span>

                                <span class="timeofday text-center" data-row-id="<?php echo $row_id; ?>"
                                      data-row-location="<?php echo $location; ?>">
                                <?php echo $course_time; ?>

                            </span>
                                <span class="price" data-default-price="<?php echo $course_price; ?>"
                                      data-row-id="<?php echo $row_id; ?>"
                                      data-row-location="<?php echo $location; ?>"><?php echo str_replace(',00', ',-', wc_price($course_price)); ?></span>
                                <a href="<?php echo get_the_permalink($product_id) . str_replace(' / ', "-", strtolower($location)) . '/' . trim($date_structure); ?>"
                                   class="product_url text-center p-1 pt-2 pb-2 m-0 bg-darkgrey white">
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php $loc = $location;
                    endif;
                }
                echo '</div>';
                echo '</div>';
                $mouth = $loop['date'];
            }
        }
    }
    die();
}

// Start coupon settings
add_action('wp_ajax_check_for_coupon', 'check_for_coupon');
add_action('wp_ajax_nopriv_check_for_coupon', 'check_for_coupon');
function check_for_coupon()
{
    global $woocommerce;

    if (!empty($_POST['coupon'])) {
        WC()->cart->add_discount(wc_format_coupon_code(wp_unslash($_POST['coupon']))); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    } else {
        wc_add_notice(WC_Coupon::get_generic_coupon_error(WC_Coupon::E_WC_COUPON_PLEASE_ENTER), 'error');
    }

    $all_notices = WC()->session->get('wc_notices', array());
    $notice_types = apply_filters('woocommerce_notice_types', array('error', 'success', 'notice'));

    // Buffer output.
    ob_start();
    foreach ($notice_types as $notice_type) {
        if (wc_notice_count($notice_type) > 0) {
            $messages = array();

            foreach ($all_notices[$notice_type] as $notice) {
                $messages[] = isset($notice['notice']) ? $notice['notice'] : $notice;
            }

            wc_get_template(
                "notices/{$notice_type}.php",
                array(
                    'messages' => array_filter($messages),
                    'notices' => array_filter($all_notices[$notice_type]),
                )
            );
        }
    }

    wc_clear_notices();

    $notices = wc_kses_notice(ob_get_clean());
    $message = trim(strip_tags($notices));

    if ($message === 'La til kupongkode.' || $message === 'Coupon code applied successfully.') {
        $send_json = array('success' => true, 'discount' => $woocommerce->cart->discount_cart, 'message' => '<p class="green">' . $message . '</p>', 'subtotal' => $woocommerce->cart->subtotal);
    } else {
        $send_json = array('success' => false, 'message' => '<p class="red">' . $message . '</p>');
    }

    wp_send_json($send_json);
    die();
}

/* CHANGE QUANTITY */
add_action('wp_ajax_change_quantity', 'change_quantity');
add_action('wp_ajax_nopriv_change_quantity', 'change_quantity');
function change_quantity()
{
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        WC()->cart->set_quantity($cart_item_key, $_POST['quantity']);
    }

    $response = [
        'total' => WC()->cart->total,
    ];

    if (!empty(WC()->cart->applied_coupons)):
        $persent = 0;
        foreach (WC()->cart->get_applied_coupons() as $coupon_code) {
            $coupon = new WC_Coupon($coupon_code);
            $persent += $coupon->amount;
        }
        $total_discount = WC()->cart->subtotal * ($persent / 100);
        $response['discount'] = $total_discount;
    endif;

    echo json_encode($response);
    exit();
}

/* START CUSTOM PRODUCT PRICE AJAX  */
add_action('wp_ajax_custom_product_price', 'custom_product_price_function');
add_action('wp_ajax_nopriv_custom_product_price', 'custom_product_price_function');
function custom_product_price_function()
{
    $type = $_POST['type'];
    $product_id = $_POST['product_id'];
    $variation_ids = $_POST['variation_id'];
    $product_row_id = $_POST['product_row_id'];
    $variation_term_id = $_POST['variation_term_id'];
    $variation_name_array = array();


    if (isset($variation_ids) & !is_null($variation_ids)) {
        $sum = 0;
        foreach ($variation_ids as $variation_id) {
            $variation = wc_get_product($variation_id);
            $_product = new WC_Product_Variation($variation);
            $formatted_name = $_product->get_attribute_summary();

            preg_match_all("/Type:(.[A-z0-9].+)/", $_product->get_attribute_summary(), $matches);
            $exlude_type = str_replace('Type:', '', $matches[0][0]);
            if (preg_match('(, [A-z]+:)', $exlude_type)) {
                $attr_type_by_variation = preg_replace('/(, [A-z0-9].+:.[A-z0-9].+)/', '', $exlude_type);
            } else {
                $attr_type_by_variation = $exlude_type;
            }

            if (explode(",", $formatted_name[1])[0] !== $attr_type_by_variation) {
                $var_name = preg_replace('/, Type:([A-z\s].+)/', '', explode('Mashine:', $formatted_name)[1]);
            } else {
                $var_name = explode('Mashine:', $formatted_name)[0];
            }

            $variation_data = array(
                'name' => $var_name,
                'price' => $_product->get_price(),
                'id' => $variation_id
            );

            array_push($variation_name_array, $variation_data);

            $variation_price = get_post_meta($variation_id, '_price', true);
            $sum += $variation_price;
        }
        $get_price = get_field('custom_products_' . $product_row_id . '_course_price', $product_id) + $sum;
    } else {
        $get_price = get_field('custom_products_' . $product_row_id . '_course_price', $product_id);
    }

    /* ------------------------------------------ */
    setlocale(LC_ALL, 'nb_NO');

    $get_location = get_field('custom_products_' . $product_row_id . '_course_location', $product_id);
    $get_date_from = get_field('custom_products_' . $product_row_id . '_course_date_from', $product_id);
    $get_date_to = get_field('custom_products_' . $product_row_id . '_course_date_to', $product_id);
    $get_time = get_field('custom_products_' . $product_row_id . '_course_time', $product_id);
    $few_seats = get_field('custom_products_' . $product_row_id . '_few_seats', $product_id);
    $course_id = get_field('custom_products_' . $product_row_id . '_course_id', $product_id);
    $row_ids_array = explode(",", $product_row_id);
    $diff = abs(parse_date(strtotime($get_date_from))['month_number'] - parse_date(strtotime($get_date_to))['month_number']);

    if ($diff > 2) {
        $data_from = parse_date(strtotime($get_date_from), 'd. F');
        $data_to = parse_date(strtotime($get_date_to), 'd. F Y');
    } else {
        $data_from = parse_date(strtotime($get_date_from), 'l d.');
        $data_to = parse_date(strtotime($get_date_to), 'l d. F Y');
    }


    $cart_item_data = array(
        'custom_price' => $get_price,
        'location' => $get_location,
        'data_from' => $data_from,
        'data_to' => $data_to,
        'data_mounth' => parse_date(strtotime($get_date_from))['month_number'],
        'row_id' => $product_row_id,
        'data_time' => $get_time,
        'few_seats' => $few_seats,
        'course_id' => $course_id,
        'variation_name' => $variation_name_array,
        'variation_ids' => $variation_ids,
        'variation_term_id' => $variation_term_id
    );


    if ($type === 'grouped') { // if is grouped courses (like on promotion page)

        foreach (WC()->cart->get_cart() as $key => $item1) {

            // Check if the item to remove is in cart
            if ($item1['product_id'] == $product_id) {
                $has_item = true;
                $key_to_remove = $key;
            }

            // Check if we add to cart the targeted product ID
            if ($product_id == $product_id) {
                $is_product_id = true;
            }

            if ($has_item && $is_product_id) {
                WC()->cart->remove_cart_item($key_to_remove);
            }
        }


        foreach ($row_ids_array as $item) {

            if ($variation_ids) {
                $get_price = get_field('custom_products_' . $item . '_course_price', $product_id) + $sum;
            } else {
                $get_price = get_field('custom_products_' . $item . '_course_price', $product_id);
            }
            setlocale(LC_ALL, 'nb_NO');
            $get_location = get_field('custom_products_' . $item . '_course_location', $product_id);
            $get_date_from = get_field('custom_products_' . $item . '_course_date_from', $product_id);
            $get_date_to = get_field('custom_products_' . $item . '_course_date_to', $product_id);
            $get_time = get_field('custom_products_' . $item . '_course_time', $product_id);
            $few_seats = get_field('custom_products_' . $item . '_few_seats', $product_id);
            $course_id = get_field('custom_products_' . $item . '_course_id', $product_id);

            $diff = abs(parse_date(strtotime($get_date_from))['month_number'] - parse_date(strtotime($get_date_to))['month_number']);
            if ($diff > 2) {
                $data_from = parse_date(strtotime($get_date_from), 'd. F');
                $data_to = parse_date(strtotime($get_date_to), 'd. F Y');
            } else {
                $data_from = parse_date(strtotime($get_date_from), 'l d.');
                $data_to = parse_date(strtotime($get_date_to), 'l d. F Y');
            }

            $cart_item_grouped_data = array(
                'custom_price' => $get_price,
                'location' => $get_location,
                'data_from' => $data_from,
                'data_to' => $data_to,
                'data_mounth' => parse_date(strtotime($get_date_from))['month_number'],
                'row_id' => $item,
                'data_time' => $get_time,
                'few_seats' => $few_seats,
                'kind' => $product_row_id,
                'grouped_data' => $product_id . ':' . $product_row_id,
                'course_id' => $course_id,
                'variation_name' => $variation_name_array,
                'variation_ids' => $variation_ids,
                'variation_term_id' => $variation_term_id
            );

            WC()->cart->add_to_cart($product_id, 1, '', '', $cart_item_grouped_data);

        }
    } else { // if is single courses

        foreach (WC()->cart->get_cart() as $key => $item) {

            if ($item['product_id'] == $product_id) {
                $has_item = true;
                $key_to_remove = $key;
            }
            if ($has_item) {
                WC()->cart->remove_cart_item($key_to_remove);
            }
        }

        WC()->cart->add_to_cart($product_id, 1, '', '', $cart_item_data);

    }

    wp_die();
}

/* END CUSTOM PRODUCT PRICE AJAX  */
/* START AJAX UPDATE MINI CART */
add_filter('wp_ajax_nopriv_ajax_update_mini_cart', 'ajax_update_mini_cart');
add_filter('wp_ajax_ajax_update_mini_cart', 'ajax_update_mini_cart');
function ajax_update_mini_cart()
{

    echo wc_get_template('cart/mini-cart.php');
    wp_die();
}

/* Add Participants */
add_action('wp_ajax_checkout_participant', 'checkout_participant');
add_action('wp_ajax_nopriv_checkout_participant', 'checkout_participant');
function checkout_participant()
{
    get_template_part('partials/checkout/participants', null, $_POST['participants']);

    wp_die();
}

/* Add MEMBER PAYER AJAX*/
add_action('wp_ajax_member_pay', 'member_pay_function');
add_action('wp_ajax_nopriv_member_pay', 'member_pay_function');
function member_pay_function()
{
    get_template_part('partials/checkout/member', null, array(
        'payers' => $_POST['payers'],
        'payments' => WC()->payment_gateways->get_available_payment_gateways()
    ));
    wp_die();
}

/* START AJAX COMPANY PAYER */
add_action('wp_ajax_bussines_pay', 'bussines_pay_function');
add_action('wp_ajax_nopriv_bussines_pay', 'bussines_pay_function');
function bussines_pay_function()
{
    $countries_obj = new WC_Countries();
    $countries = $countries_obj->get_allowed_countries();
    $temp = array($countries_obj->get_base_country() => $countries[$countries_obj->get_base_country()]);
    unset($countries[$countries_obj->get_base_country()]);
    $countries = $temp + $countries;
    get_template_part('partials/checkout/bussiness', null, array(
        'countries' => $countries,
        'payments' => WC()->payment_gateways->get_available_payment_gateways()
    ));
    wp_die();
}

/* END AJAX COMPANY PAYER */


add_action('wp_ajax_send_email_on_order_fail', 'send_email_on_order_fail');
add_action('wp_ajax_nopriv_send_email_on_order_fail', 'send_email_on_order_fail');
function send_email_on_order_fail()
{
    if ($_POST['payer']) {

        $data = json_decode(stripslashes($_POST['payer']));
        $tbl = '';
        $tbl = '<h3 style="color: red; margin-bottom: 15px;">Denne brukeren har problemer med å legge inn en bestilling</h3>';
        $tbl .= '<table>';

        foreach ($data as $key => $payer) {
            $tbl .= '<tr>';
            $tbl .= '<td>' . $key . '</td>';
            $tbl .= '<td>' . $payer . '</td>';
            $tbl .= '</tr>';
        }
        $tbl .= '</table>';
        $tbl .= '<h3>Error message:</h3>';
        $tbl .= '<div>' . $_POST['message'] . '</div>';

        echo $tbl;

//        espen@offshoreutdanning.no

        $to = 'espen@offshoreutdanning.no';
        $subject = 'Order Place Error';
        $body = $tbl;
        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($to, $subject, $body);

    }

    wp_die();
}