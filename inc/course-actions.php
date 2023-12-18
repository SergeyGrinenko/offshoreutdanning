<?php
require_once 'courses/actions/add_to_cart.php';

add_action('wp_head', 'wc_custom_redirect_after_purchase');
function wc_custom_redirect_after_purchase()
{
    if (!is_wc_endpoint_url('order-received')) return;
    global $wp;

    // If order_id is defined

    if (isset($wp->query_vars['order-received']) && absint($wp->query_vars['order-received']) > 0) :

        $order_id = absint($wp->query_vars['order-received']); // The order ID
        $order = wc_get_order($order_id); // The WC_Order object
        $transaction_id = empty($order->get_transaction_id()) ? $order_id : $order->get_transaction_id(); // The transaction ID

        ?>
        <script>
            gtag('event', 'conversion', {
                'send_to': 'AW-1068195124/1NciCNKn2bkDELS6rf0D',
                'value': <?= $order->get_total() ?>,
                'currency': '<?= $order->get_currency() ?>',
                'transaction_id': '<?= $transaction_id; ?>'
            });
        </script>

        <script> window.uetq = window.uetq || [];
            window.uetq.push('event', 'PRODUCT_PURCHASE', {
                "ecomm_prodid": "REPLACE_WITH_PRODUCT_ID",
                "ecomm_pagetype": "PURCHASE",
                "revenue_value": <?= $order->get_total() ?>,
                "currency": <?= $order->get_total() ?>
            }); </script>
    <?php endif;
}

function add_my_query_var($vars)
{
    $vars[] = 'my_var';
    return $vars;
}

add_filter('query_vars', 'add_my_query_var');

add_filter('woocommerce_checkout_fields', 'billing_remove_fields', 9999);

function billing_remove_fields($woo_checkout_fields_array)
{
    unset($woo_checkout_fields_array['billing']['billing_state']);
    return $woo_checkout_fields_array;
}

function get_course_type_template($single_template)
{
    global $post;
    if ($post->post_type == 'product') {

        $single_template = dirname(__DIR__) . '/templates/courses/single-course-tpl.php';
    }
    return $single_template;
}

add_filter('single_template', 'get_course_type_template');


add_action('init', 'add_rules');
function add_rules()
{
    add_rewrite_rule('^kurs\/([^\/]+)\/([^\/]+)\/([^\/]+)', 'index.php?kursname=$matches[1]&course_location=$matches[2]&course_date=$matches[3]', 'top');
    add_rewrite_rule("produktkategori/kurs", 'index.php?shop_page=true', 'top');
    add_rewrite_rule("^kurskategori\/([^\/]+)", 'index.php?term=$matches[1]', 'top');
}


add_filter('query_vars', 'add_query_vars');
function add_query_vars($aVars)
{
    $aVars[] = "shop_page";
    $aVars[] = "kursname";
    $aVars[] = "course_location";
    $aVars[] = "course_date";
    $aVars[] = "term";
    return $aVars;
}


add_action('pre_get_posts', 'pce_dynamic_section_lookup', 0, 2);
function pce_dynamic_section_lookup($wp)
{
    global $wp_query;
    if (is_admin() || !$wp->is_main_query()) {
        return;
    }

    if ($wp->is_main_query()) {

        if (get_query_var('course_location')) {
            $product = get_page_by_path($wp->query_vars['kursname'], OBJECT, 'product');
            $wp->set('page_id', $product->ID);
            $wp->query_vars['post_type'] = 'product';
            $wp->query_vars['is_single'] = true;
            $wp->query_vars['is_singular'] = true;
            $wp->query_vars['is_archive'] = false;
            $wp->is_single = true;
            $wp->is_singular = true;
            $wp->is_archive = false;
            $wp->is_post_type_archive = false;
        }
        if (get_query_var('shop_page') === 'true') {
            $wp->query_vars['post_type'] = 'product';
            $wp->query_vars['is_archive'] = true;
            $wp->is_archive = true;
            $wp->is_post_type_archive = true;
        }

        if (get_query_var('term')) {
            $wp->query_vars['post_type'] = 'product';
            $wp->query_vars['is_archive'] = true;
            $wp->is_archive = true;
            $wp->is_post_type_archive = true;
        }
    }
}

function custom_cart_item_remove_link($link, $cart_item_key)
{
    $cart = WC()->cart->get_cart();

    if (!empty($cart)) {
        foreach ($cart as $item) {
            $inactive = false;
            if (isset($item['inactive']))
                $inactive = $item['inactive'];

            if ($inactive === true) {
                WC()->cart->remove_coupons();
                break; // Exit the loop if you only want to apply changes to one item
            }
        }
    }

    return $link;
}

add_filter('woocommerce_cart_item_remove_link', 'custom_cart_item_remove_link', 10, 2);

add_action('wp_ajax_course_remove_from_cart', 'course_remove_from_cart');
add_action('wp_ajax_nopriv_course_remove_from_cart', 'course_remove_from_cart');
function course_remove_from_cart()
{
    $product_id = $_POST['product_id'];
    $course_id = $_POST['course_id'];

    foreach (WC()->cart->get_cart() as $key => $product) {
        if ($product['product_id'] === intval($product_id) && intval($product['course_id']) === intval($course_id)) {
            WC()->cart->remove_cart_item($key);
        }
    }

    echo get_template_part('template-parts/mini-cart');
    exit();
}


function return_get_template_part($slug, $name = null)
{

    ob_start();
    get_template_part($slug, $name);
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

$content = return_get_template_part('content', 'page');

add_action('woocommerce_before_calculate_totals', 'set_course_price');
function set_course_price($cart)
{
    if (is_admin() && !defined('DOING_AJAX'))
        return;

    foreach ($cart->get_cart() as $cart_item) {
        if (isset($cart_item['course_price']) && $cart_item['course_price'] > 0) {
            $cart_item['data']->set_price($cart_item['course_price']);
        }
    }
}

add_action('woocommerce_checkout_update_order_meta', 'woocommerce_checkout_update_meta');
function woocommerce_checkout_update_meta($order_id)
{
    if (!empty($_POST['company-ordrereferanse'])) {
        update_post_meta($order_id, '_company-ordrereferanse', sanitize_text_field($_POST['company-ordrereferanse']));
    }
    if (!empty($_POST['billing_organization_number'])) {
        update_post_meta($order_id, '_billing_organization_number', sanitize_text_field($_POST['billing_organization_number']));
    }
}

function get_course_locations($course_dates)
{
    $localities = [];
    foreach ($course_dates as $months) {
        foreach ($months as $data) {
            foreach ($data as $courses) {
                foreach ($courses as $course) {
                    $localities[] = $course['locality'];
                }
            }
        }
    }
    return array_unique($localities);
}

function get_price_range($product_id, $currency = true)
{
    $course_dates = get_post_meta($product_id, 'product_dates', true);
    $prices = array();
    foreach ($course_dates as $months) {
        foreach ($months as $data) {
            foreach ($data as $courses) {
                foreach ($courses as $course) {
                    if ($currency === true) {
                        $prices[] = get_woocommerce_currency_symbol() . ' ' . $course['price'];
                    } else {
                        $prices[] = $course['price'];
                    }

                }
            }
        }
    }
    return $prices;
}

function formatted_price($price)
{
    return preg_replace('/([.,][0-9]+)/', ',-', wc_price($price));
}

function get_allowed_countries()
{
    $countries_obj = new WC_Countries();
    $countries = $countries_obj->get_allowed_countries();
    $temp = array($countries_obj->get_base_country() => $countries[$countries_obj->get_base_country()]);
    unset($countries[$countries_obj->get_base_country()]);
    $countries = $temp + $countries;
    return $countries;
}

function getCourseDuration(int $from = null, int $to = null, $type = 'm')
{
    if (!is_null($from) && !is_null($to)) {
        $dateFrom = new DateTime(date("d-m-Y", $from));
        $dateTo = new DateTime(date("d-m-Y", $to));
        $duration = $dateFrom->diff($dateTo);
        return $duration->$type;
    }
}

function local_date_i18n($format, $timestamp)
{

    $timezone_str = wp_timezone_string();
    $timezone = new \DateTimeZone($timezone_str);

    // The date in the local timezone.
    $date = new \DateTime(null, $timezone);
    $date->setTimestamp($timestamp);
    $date_str = $date->format('Y-m-d H:i:s');

    // Pretend the local date is UTC to get the timestamp
    // to pass to date_i18n().
    $utc_timezone = new \DateTimeZone('UTC');
    $utc_date = new \DateTime($date_str, $utc_timezone);
    $timestamp = $utc_date->getTimestamp();

    return date_i18n($format, $timestamp, true);
}

function setDatelocale(int $timestamp = null, string $format = null, int $duration = 0, bool $utf8 = false)
{
    if (!is_null($timestamp) && !is_null($format)) {
        if ($duration > 0)
            $format = 'd. F'; /* Show Month Name instead of day of week */

        $date = local_date_i18n($format, $timestamp);
        if ($utf8 === true)
            $date = utf8_encode(local_date_i18n($format, $timestamp));
        return $date;
    }
}

function getDayphase(string $dayphase = null)
{
    if (!is_null($dayphase)) {
        $array = ['weekend' => 'helg', 'day' => 'dag', 'evening' => 'kveld'];
        return ucfirst($array[$dayphase]);
    }
}

/* CHANGE QUANTITY */
add_action('wp_ajax_change_quantity', 'change_quantity');
add_action('wp_ajax_nopriv_change_quantity', 'change_quantity');
function change_quantity()
{
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        WC()->cart->set_quantity($cart_item_key, $_POST['quantity']);
    }
    $response = ['cart_items' => return_get_template_part("template-parts/cart-items")];

    echo json_encode((object)$response);
    die();
}

/* APPLY COUPON CODE */
add_action('wp_ajax_apply_coupon_code', 'apply_coupon_code');
add_action('wp_ajax_nopriv_apply_coupon_code', 'apply_coupon_code');
function apply_coupon_code()
{
    if (!empty($_POST['coupon'])) {
        WC()->cart->add_discount(wc_format_coupon_code(wp_unslash($_POST['coupon'])));
    } else {
        wc_add_notice(WC_Coupon::get_generic_coupon_error(WC_Coupon::E_WC_COUPON_PLEASE_ENTER), 'error');
    }

    $all_notices = WC()->session->get('wc_notices', array());
    $notice_types = apply_filters('woocommerce_notice_types', array('error', 'success', 'notice'));


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

    ob_start();

//    $response = ['cart_items' => return_get_template_part("template-parts/cart-items")];

//    echo json_encode((object)$response);
    get_template_part('template-parts/cart-items');
//    return_get_template_part("template-parts/cart-items");
    $data = ob_get_clean();


    if ($message === 'La til kupongkode.' || $message === 'Coupon code applied successfully.') {
        $send_json = array('success' => true, 'response' => $data, 'message' => '<p class="text-success mt-3 mb-0">' . $message . '</p>');
    } else {
        $send_json = array('success' => false, 'message' => '<p class="text-danger mt-3 mb-0">' . $message . '</p>');
    }

//    var_dump($send_json);
//    exit();

    wp_send_json($send_json);
    die();
}

/* GET BETALER CODE */
add_action('wp_ajax_betaler_type', 'betaler_type');
add_action('wp_ajax_nopriv_betaler_type', 'betaler_type');
function betaler_type($type = null, $callback = null, $id = null)
{
    define("DELTAGER", 'deltager');
    define('BUSSINESS', 'bussiness');

    if (!empty($_POST['type']))
        $type = $_POST['type'];

    if ($type === DELTAGER) {
        ob_start();
        if (is_null($callback)) {
            if (!empty($_POST['participants'])) {
                if (!empty($_POST['payer_id']))
                    $payer_id = $_POST['payer_id'];

                $dataArray = json_decode(stripslashes($_POST['participants']), true);
                if ($dataArray === null && json_last_error() !== JSON_ERROR_NONE) {
                    echo "Error decoding JSON: " . json_last_error_msg();
                } else {
                    // Define regular expressions to match keys
                    $firstnamePattern = '/^firstname-\d+$/';
                    $lastnamePattern = '/^lastname-\d+$/';

                    // Initialize flags
                    $hasFirstname = false;
                    $hasLastname = false;

                    // Loop through the keys in the array and check if they match the patterns
                    foreach ($dataArray as $key => $value) {
                        if (preg_match($firstnamePattern, $key)) {
                            $hasFirstname = true;
                        } elseif (preg_match($lastnamePattern, $key)) {
                            $hasLastname = true;
                        }
                    }

                    // Check if both keys are present
                    if ($hasFirstname && $hasLastname) {
                        get_template_part('template-parts/deltager', false, ['participants' => $_POST['participants'], 'payer_id' => $payer_id]);
                    }
                }

            }
        } else {
            if (!is_null($id)) {
                get_template_part('template-parts/participants-fields', null, ['item' => $id, 'editable' => false, 'billing' => true]);
            }
        }
        $data = ob_get_clean();
    }
    if ($type === BUSSINESS) {
        session_start();
        unset($_SESSION["betalerID"]);
        ob_start();
        if (is_null($callback)) {
            get_template_part('template-parts/bussiness');
        } else {
            get_template_part('template-parts/bussiness', null, ['editable' => false, 'billing' => true]);
        }
        $data = ob_get_clean();
    }

    wp_send_json($data);
    die();
}

/* SETUP BETALER */
add_action('wp_ajax_setup_betaler', 'setup_betaler');
add_action('wp_ajax_nopriv_setup_betaler', 'setup_betaler');
function setup_betaler()
{
    if (!empty($_POST['type'])) {
        session_start();
        $_SESSION["betalerType"] = $_POST['type'];
        if ($_POST['type'] === 'deltager') {
            $_SESSION["betalerID"] = 1;
            if (!empty($_POST['id'])) {
                $_SESSION["betalerID"] = $_POST['id'];
            }
        }

        betaler_type($_POST['type'], 'setup_betaler', $_POST['id']);
    }
    die();
}

/* UPDATE DELEGATE */
add_action('wp_ajax_update_delegates', 'update_delegates');
add_action('wp_ajax_nopriv_update_delegates', 'update_delegates');
function update_delegates()
{
    $members_quantity = $_POST['count'];
    for ($i = 1; $i <= $members_quantity; $i++):
        echo get_template_part('template-parts/participants-fields', null, ['item' => $i, 'editable' => false]);
    endfor;
    die();
}

function disable_checkout_script()
{
    wp_dequeue_script('wc-checkout');
}

add_action('wp_enqueue_scripts', 'disable_checkout_script');


function theme_options_add()
{
    register_setting('load_courses', 'load_courses');
}

//ADD LOAD CORSES PAGE TO MENU
function add_options()
{
    add_menu_page(__('Last inn kurs'), __('Last inn kurs'), 'manage_options', 'settings', 'theme_options_page', 'dashicons-plus-alt');
}

if (is_admin()) {
    add_action('admin_init', 'theme_options_add');
    add_action('admin_menu', 'add_options');
}

add_action('load_courses_cron', 'courses_cron_function');

function courses_cron_function()
{
    require_once(trailingslashit(get_template_directory()) . 'classes/loadCourses.php');
    $CourseLoading = new LoadCourses();
    $CourseLoading->load_courses(get_option('load_courses'));
}

function theme_options_page()
{
    require_once(trailingslashit(get_template_directory()) . 'api/testRestApi.php');
    $data = new X2_TestRestApi();
    $status = null;

    $getCourseTypes = $data->getCourseTypes(); ?>
    <div class="load_courses_wrap my-3">
    <form method="post" action="options.php">
        <div class="load_courses_manually" style="column-count: 3">
            <?php settings_fields('load_courses'); ?>
            <?php $options = get_option('load_courses'); ?>
            <?php foreach ($getCourseTypes as $key => $value): ?>
                <?php if (isset($key)): ?>
                    <div style="margin-bottom: 4px;">
                        <label class="<?php echo ($options[$key] == $key) ? 'active' : ' ' ?>"><input
                                    id="load_courses[<?php echo $key; ?>]" type="checkbox"
                                    name="load_courses[<?php echo $key; ?>]"
                                    value="<?php echo $key; ?>" <?php echo ($options[$key] == $key) ? 'checked' : ' ' ?> />
                            <span><?php echo $value; ?></span>
                        </label>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php submit_button('Save Changes and Load'); ?>
    </form>
    <?php


    if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] == true) {
        require_once(trailingslashit(get_template_directory()) . 'classes/loadCourses.php');
        $CourseLoading = new LoadCourses();
        if (get_option('load_courses')) {
            $CourseLoading->load_courses(get_option('load_courses'));
        }
        $status = $CourseLoading->status;
    }

    if (!is_null($status)) {
        echo '<p class="bg-success p-2 text-light">' . $status . '</p>';
    }
    echo '</div>';
}


add_action('wp_ajax_load_more_courses', 'load_more_courses');
add_action('wp_ajax_nopriv_load_more_courses', 'load_more_courses');

function load_more_courses()
{
    if (isset($_POST['product_id'])) {
        $product = wc_get_product($_POST['product_id']);
        $course_dates = get_post_meta($product->get_id(), 'product_dates', true);

        // Construct the path to the template file
        $template_path = locate_template('template-parts/course-dates.php');

        if ($template_path) {

            // Define variables for the template
            $template_args = [
                'product' => $product,
                'course_dates' => $course_dates,
                'location' => ($_POST['location']) ? $_POST['location'] : null,
                'summ_variations' => ($_POST['summ_variations']) ? $_POST['summ_variations'] : null,
                'grouped' => ($_POST['grouped']) ? $_POST['grouped'] : null,
                'filter' => ($_POST['filter']) ? $_POST['filter'] : null,
            ];

            ob_start(); // Start output buffering
            extract($template_args); // Extract variables for the template
            include $template_path; // Include the template file
            $output = ob_get_clean(); // Get the content of the output buffer and clean it

            echo $output; // Output the content
        } else {
            // Handle the case where the template file is not found
            echo 'Template file not found.';
        }

        // Always use die() or exit() after echoing the response
    }

    die();
}

function change_excerpt_more($more)
{
    return '...';
}

add_filter('excerpt_more', 'change_excerpt_more');


add_action('wp_ajax_submit_enroll_form', 'submit_enroll_form');
add_action('wp_ajax_nopriv_submit_enroll_form', 'submit_enroll_form');
function submit_enroll_form()
{
    if ($_POST['data']) {
        parse_str($_POST['data'], $searcharray);
        $html = '';
        foreach ($searcharray as $key => $value) {
            if ($key !== 'page_id') {
                $html .= $key . ' : ' . $value . '; ';
            }
        }

        if (!empty($html)) {
            $to = 'hjelp@offshoreutdanning.no';
            $subject = 'Enroll Form Data';
            $body = $html;
            wp_mail($to, $subject, $body);

            get_template_part('template-parts/contact_submitted', false, ['page_id' => $searcharray['page_id']]);
        }
    }
    die();
}


function change_product_category_term_url($termlink, $term, $taxonomy)
{
    if ($taxonomy === 'product_cat') {
        $termlink = str_replace('/produktkategori/courses/', '/kurskategori/', $termlink);
    }
    return $termlink;
}

add_filter('term_link', 'change_product_category_term_url', 10, 3);

add_filter('pre_update_option_woocommerce_cleanup_draft_orders_interval', '__return_false');

remove_action('woocommerce_scheduled_sales', 'woocommerce_cleanup_draft_orders');


function getProductVariation($product_id)
{
    $data = [];
    if (!WC()->cart->is_empty()) {
        foreach (WC()->cart->get_cart() as $key => $product_item) {
            if ($product_id == $product_item['product_id']) {
                if (isset($product_item['practices'])) {
                    if (isset($product_item['variation_term_id'])) $data['practice_type'] = $product_item['variation_term_id'];
                    if (isset($product_item['variation_ids'])) {
                        $parsedData = array_values(json_decode(stripslashes($product_item['variation_ids']), true));
                        foreach ($parsedData as $variation_id) {
                            $data['practice_variation'][] = $variation_id;
                        }
                    }
                }
            }
        }
    }
    return $data;
}
