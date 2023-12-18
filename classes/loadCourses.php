<?php

include_once site_url() . '/wp-load.php';
include_once site_url() . '/wp-admin/includes/post.php';
require_once(trailingslashit(get_template_directory()) . 'api/testRestApi.php');

//include_once '../../../../wp-load.php';
//include_once '../../../../wp-admin/includes/post.php';
//require_once(trailingslashit(get_template_directory()) . 'api/testRestApi.php');

class LoadCourses
{
    protected $sitemap_dates = [];

    const POST_STATUS_PUBLISH = 'publish',
        POST_STATUS_PENDING = 'pending',
        GET_DATE_PRICE = 'price',
        GET_DATE_FULL = 'full';

    public $status = 'Done!';

    public function __construct()
    {
        $this->load_courses(get_option('load_courses'));
    }

    public function load_courses(array $courses = [])
    {
        $data = new X2_TestRestApi();
        $getCourseTypes = $data->getCourseTypes();

        if (!$courses) {
            foreach ($getCourseTypes as $courseTypeID => $courseName) {
                $args = array(
                    'post_type' => 'product',
                    'meta_key' => 'api_course_id',
                    'meta_value' => $courseTypeID,
                    'post_status' => 'any'
                );
                $query = new WP_Query($args);
                $posts = $query->get_posts();

                if ($posts) {
                    $post_id = array_shift($posts)->ID;
                    if (!is_null($post_id)) {
                        $getDates = $data->getCourseDate($courseTypeID);
                        update_post_meta($post_id, 'product_dates', $getDates);

                        if (count($getDates) === 0) {
                            $this->update_post_status($post_id, self::POST_STATUS_PENDING);
                        } else {
                            $price = $this->get_date_info($getDates, self::GET_DATE_PRICE);
                            if ($price == '0.00') {
                                $this->update_post_status($post_id, self::POST_STATUS_PENDING);
                            }
                        }
//                        var_dump('updated');
                    } else {
//                        var_dump('not found');
                    }

                } else {
                    $getDates = $data->getCourseDate($courseTypeID);
                    if (!empty($getDates)) {
                        $price = $this->get_date_info($getDates, self::GET_DATE_PRICE);
                        if ($price == '0.00') {
                            $post_id = $this->create_product_automatically($courseName, self::POST_STATUS_PENDING);
                        } else {
                            $post_id = $this->create_product_automatically($courseName);
                        }

                        add_post_meta($post_id, 'api_course_id', $courseTypeID, false);
                        add_post_meta($post_id, 'product_dates', $getDates, false);
//                        var_dump('created');
                    }
                }
                $this->update_sitemap($post_id, $getDates);
            }
        } else {
            foreach ($courses as $course_type_id) {
                $args = array(
                    'post_type' => 'product',
                    'meta_key' => 'api_course_id',
                    'meta_value' => $course_type_id,
                );
                $query = new WP_Query($args);
                $posts = $query->get_posts();
                $course_name = $data->getCourseType($course_type_id);

                if (!empty($posts)) {
                    $post_id = array_shift($posts)->ID;
                    $getDates = $data->getCourseDate($course_type_id);
                    update_post_meta($post_id, 'product_dates', $getDates);
                } else {
                    $getDates = $data->getCourseDate($course_type_id);
                    if (!empty($getDates)) {
                        $post_id = $this->create_product_automatically($course_name);
                        add_post_meta($post_id, 'api_course_id', $course_type_id, false);
                        add_post_meta($post_id, 'product_dates', $getDates, false);
                    }
                }
                $this->update_sitemap($post_id, $getDates);
            }
        }
        $this->pushToFile();
    }


    public function update_sitemap($post_id, $dates = array())
    {
        if ($post_id && $dates) {
            $dates = $this->get_date_info($dates, self::GET_DATE_FULL);
            foreach ($dates as $course) {
                if (!str_contains(get_the_permalink($post_id), '?post_type=')) {
                    $this->sitemap_dates[] = get_the_permalink($post_id) . preg_replace('([ +\/])', '', strtolower(rawurldecode($course['locality']))) . '/' . local_date_i18n("d", $course['startdate']) . 'til' . local_date_i18n("d", $course['enddate']) . '-' . $course['month'] . '-' . $course['year'];
                }
            }
        }
    }

    public function pushToFile()
    {
        $file = dirname(__DIR__) . '/api/temp/sitemap.json';
        $json = json_encode(mb_convert_encoding($this->sitemap_dates, 'UTF-8', 'UTF-8'), JSON_UNESCAPED_UNICODE);
        if ($json)
            file_put_contents($file, $json);
    }

    public function create_product_automatically($name, $status = self::POST_STATUS_PUBLISH)
    {
        $product = new WC_Product_Simple();
        $product->set_name($name);
        $product->set_status($status);
        $product->set_catalog_visibility('visible');
        $product->set_price(1.00);
        $product->set_regular_price(1.00);
        $product->set_virtual(true);
        $product->save();

        return $product->get_id();
    }

    public function update_post_status($post_id, $status = self::POST_STATUS_PUBLISH)
    {
        if ($post_id) {
            $get_post = get_post($post_id, 'ARRAY_A');
            if ($get_post['post_status'] !== self::POST_STATUS_PENDING) {
                $get_post['post_status'] = $status;
            }
        }
    }

    public function get_date_info($dates, string $return)
    {
        if (!empty($return)) {
            $prices = array();
            $full_data = array();
            foreach ($dates as $year => $months) {
                foreach ($months as $month => $data) {
                    foreach ($data as $courses) {
                        foreach ($courses as $course) {
                            $course['year'] = $year;
                            $course['month'] = $month;
                            $prices[] = $course['price'];
                            $full_data[] = $course;
                        }
                    }
                }
            }
            if ($return === self::GET_DATE_PRICE) return min($prices);
            if ($return === self::GET_DATE_FULL) return $full_data;
        }
    }
}

$test = new LoadCourses();