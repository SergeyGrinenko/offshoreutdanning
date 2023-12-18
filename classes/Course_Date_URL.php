<?php

include_once site_url() . '/wp-load.php';
include_once site_url() . '/wp-admin/includes/post.php';
require_once(trailingslashit(get_template_directory()) . 'api/testRestApi.php');

//include_once '../../../../wp-load.php';
//include_once '../../../../wp-admin/includes/post.php';
//require_once(trailingslashit(get_template_directory()) . 'api/testRestApi.php');

class Course_Date_URL
{
    protected $coupon = null;
    protected $STATUS = null;
    const ARG_Coupon = 'coupon';

    public function __construct()
    {
        //https://offutd-dev2022.exsto.no/?no_cache=1&coupon=jg84jkf93jkf&exsto[courses][7]=56833

        //https://offutd-dev2022.exsto.no/?no_cache=1&coupon=jg84jkf93jkf&exsto[courses][190]=52142
        $url = parse_url(urldecode($_SERVER['REQUEST_URI']));

        if (!is_admin()) {
            if ($url['query'])
                $this->checkParams($url['query']);
        }
    }

    private function checkParams(string $url)
    {
        $args = [];
        $outputArray = [];
        $params = explode('&', $url);


        foreach ($params as $param) {
            list($key, $value) = explode('=', $param);
            $args[urldecode($key)] = urldecode($value);
        }

        $pattern = '/^exsto\[courses\]\[\d+\]$/';
        $outputExists = false;

        foreach ($args as $key => $value) {
            if ($key === 'coupon') {
                // Include the "coupon" parameter in $outputArray
                $outputArray[$key] = $value;
            }

            if (preg_match($pattern, $key)) {
                $outputExists = true;

                $keyParts = explode('[', str_replace(']', '', $key));
                $current = &$outputArray;

                foreach ($keyParts as $part) {
                    if (!isset($current[$part])) {
                        $current[$part] = [];
                    }
                    $current = &$current[$part];
                }

                $current = $value;
            }
        }


        if ($outputExists) {
            $this->createEnroll($outputArray);
            wp_redirect(wc_get_checkout_url());
        }
    }

    private function createEnroll(array $args = [])
    {
        $api = new X2_TestRestApi();
        foreach ($args as $key => $value) {
            if ($key === self::ARG_Coupon) {
                $this->coupon = $value;
            }


            if (is_array($value)) {
                foreach ($value['courses'] as $coursetype_id => $course_id) {
                    $course = $api->getDate($course_id, $coursetype_id);

//                   var_dump($course);
//                    exit();

                    $args = array(
                        'post_type' => 'product',
                        'meta_key' => 'api_course_id',
                        'meta_value' => $coursetype_id,
                        'post_status' => 'any'
                    );
                    $query = new WP_Query($args);
                    $product = $query->get_posts()[0];
                    $course_meta = [];
                    $course_month = null;

                    foreach ($course as $months) {
                        foreach ($months as $month => $data) {
                            $course_month = $month;
                            foreach ($data as $courses) {
                                foreach ($courses as $item) {
                                    $course_meta = $item;
                                }
                            }
                        }
                    }

                    $duration = getCourseDuration($course_meta['startdate'], $course_meta['enddate']);
                    $start = getCourseDuration(time(), $course_meta['startdate'], 'days');

                    $product_data = array(
                        'course_price' => $course_meta['price'],
                        'location' => $course_meta['locality'],
                        'start' => $start,
                        'data_from' => setDatelocale($course_meta['startdate'], 'l d.', $duration),
                        'data_to' => ($duration > 0) ? setDatelocale($course_meta['enddate'], 'l d.', $duration) : setDatelocale($course_meta['enddate'], 'l d.', $duration) . ' ' . ucfirst(setDatelocale($course_meta['startdate'], 'F', $duration)),
                        'data_mounth' => $course_month,
                        'data_time' => $course_meta['dayphase'],
                        'few_seats' => $course_meta['few_seats'],
                        'course_id' => $course_meta['course_id'],
                        'grouped_course' => false,
                        'inactive' => true
                    );

                    if (WC()->cart->get_cart()) {
                        foreach (WC()->cart->get_cart() as $key => $product_item) {
                            if (intval($product_item['product_id']) === $product->ID) {
                                $has_item = true;
                                if ($has_item) {
                                    WC()->cart->remove_cart_item($key);
                                }
                            }
                        }
                    }

                    WC()->cart->empty_cart();
                    WC()->cart->add_to_cart($product->ID, 1, '', '', $product_data);

                    if ($this->coupon) {
                        WC()->cart->remove_coupons();
                        WC()->session->set('applied_coupons', $this->coupon);
                    }
                }
            }
        }
    }
}

new Course_Date_URL();