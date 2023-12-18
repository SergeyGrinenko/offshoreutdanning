<?php

add_action( 'after_setup_theme', 'wpse319485_add_woocommerce_support' );

function wpse319485_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
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
    $prices = [];
    foreach ($course_dates as $months) {
        foreach ($months as $data) {
            foreach ($data as $courses) {
                foreach ($courses as $course) {
                    $prices[] = ($currency === true) ? get_woocommerce_currency_symbol() . ' ' . $course['price'] : $course['price'];
                }
            }
        }
    }
    return $prices;
}

//function formatted_price($price)
//{
//    return preg_replace('/([.,][0-9]+)/', ',-', wc_price($price));
//}

function get_allowed_countries()
{
    $countries_obj = new WC_Countries();
    $countries = $countries_obj->get_allowed_countries();
    $temp = [$countries_obj->get_base_country() => $countries[$countries_obj->get_base_country()]];
    unset($countries[$countries_obj->get_base_country()]);
    return $temp + $countries;
}

function getCourseDuration(int $from = null, int $to = null, $type = 'm')
{
    if (!is_null($from) && !is_null($to)) {
        $dateFrom = new DateTime(date("d-m-Y", $from));
        $dateTo = new DateTime(date("d-m-Y", $to));
        return $dateFrom->diff($dateTo)->$type;
    }
}

function local_date_i18n($format, $timestamp)
{
    $timezone_str = wp_timezone_string();
    $timezone = new \DateTimeZone($timezone_str);
    $date = new \DateTime(null, $timezone);
    $date->setTimestamp($timestamp);
    $date_str = $date->format('Y-m-d H:i:s');
    $utc_timezone = new \DateTimeZone('UTC');
    return date_i18n($format, (new \DateTime($date_str, $utc_timezone))->getTimestamp(), true);
}

function setDatelocale(int $timestamp = null, string $format = null, int $duration = 0, bool $utf8 = false)
{
    if (!is_null($timestamp) && !is_null($format)) {
        $format = ($duration > 0) ? 'd. F' : $format;
        $date = local_date_i18n($format, $timestamp);
        return ($utf8 === true) ? utf8_encode($date) : $date;
    }
}

function getDayphase(string $dayphase = null)
{
    if (!is_null($dayphase)) {
        $array = ['weekend' => 'helg', 'day' => 'dag', 'evening' => 'kveld'];
        return ucfirst($array[$dayphase]);
    }
}