<?php

//$cron_jobs = get_option( 'cron' );
//var_dump($cron_jobs);

/* CREATE WORDPRESS CRON 20sec */
add_filter('cron_schedules', 'cron_add_twenty_sec'); // register cron 20 sec
function cron_add_twenty_sec($schedules)
{
    $schedules['twenty_sec'] = array(
        'interval' => 20,
        'display' => 'This Cron Working every 20 sec'
    );
    $schedules['four_hours'] = array(
        'interval' => 14400,
        'display' => 'This Cron Working every 4 hours'
    );
    return $schedules;
}


//14400
add_action( 'init', 'my_activation' );
function my_activation() {
    if (!wp_next_scheduled('load_courses_event')) {
        wp_schedule_event(time(), 'twenty_sec', 'load_courses_event');
    }
}



/* START LOAD COURSES EVENT */
add_action('load_courses_event', 'load_courses_cron'); // load courses from temporary .json file
function load_courses_cron()
{

//    error_log('hello shedule!');
//    require_once(ABSPATH . 'wp-admin/includes/post.php');
//    global $wp_error;

    $course_id = "course_id";
    $course_date_from = "course_date_from";
    $course_date_to = "course_date_to";
    $course_location = "course_location";
    $course_dayphase = "course_time";
    $course_status = "course_status";
    $course_few_seats = "few_seats";
    $course_price = "course_price";
    $course_showing = "course_deadline";
    $course_weekend = "weekend";
    $coursetype_technical = "coursetype_technical";
    $coursetype_id = 'coursetype_id';

    $file = dirname(__DIR__) . '/api/temp/courses.json';


    if (file_exists($file)) { // check if file exist
        $strJsonFileContents = file_get_contents($file);
        $full_data = json_decode($strJsonFileContents, true, 512, JSON_UNESCAPED_UNICODE);
        $coursetype_ids = array_column($full_data, 'coursetype_id');
        array_multisort($coursetype_ids, SORT_ASC, $full_data);
        $repeater_field = "custom_products";
        $step = 10; // the сount of items that are loaded at a time
        /* NOTE: more than 10 is not recommended, as it is likely to drop the server :). (too much data) */
        $page = get_field('page', 'option');

        $offset = $step * $page;
        $total = count($full_data); // get count elements
        $total_steps = ($total / $step); // get count of steps

        $i = 0;
        $x = 0;
        if ($page < round($total_steps)) {
            $post_name_array = array();
            foreach ($full_data as $item_data) {
                if ($i >= $offset && $i <= ($offset + $step)) {

                    $post_name = $item_data['coursetype_technical'];
                    array_push($post_name_array, $post_name);
                    $post_id = post_exists($post_name, '', '', 'product');
                    $check_custom_title = get_field('custom_course_title', $post_id);

                    $values = array(
                        $course_id => $item_data['course_id'],
                        $course_date_from => strtotime('+2 hour', $item_data['course_date_from']),
                        $course_date_to => strtotime('+2 hour', $item_data['course_date_to']),
                        $course_location => isset($item_data['course_location']) ? $item_data['course_location'] : '',
                        $course_dayphase => isset($item_data['course_time']) ? $item_data['course_time'] : '',
                        $course_status => isset($item_data['course_status']) ? $item_data['course_status'] : '',
                        $course_few_seats => isset($item_data['few_seats']) ? $item_data['few_seats'] : '',
                        $course_price => isset($item_data['course_price']) ? $item_data['course_price'] : '',
                        $course_showing => isset($item_data['course_deadline']) ? $item_data['course_deadline'] : '',
                        $course_weekend => isset($item_data['weekend']) ? $item_data['weekend'] : '',
                        $coursetype_technical => isset($item_data['coursetype_technical']) ? $item_data['coursetype_technical'] : '',
                        $coursetype_id => isset($item_data['coursetype_id']) ? $item_data['coursetype_id'] : ''
                    );

                    // if post exist
                    if ($post_id) {

                        // repeater loop
                        if (have_rows($repeater_field, $post_id)) {
                            $k = 0;
                            $existarray = array();

                            while (have_rows($repeater_field, $post_id)): the_row();
                                $course_id_array = get_sub_field('course_id', $post_id);
                                $deadline = get_sub_field('course_deadline', $post_id);
                                $current_date = new DateTime(Date('Y-m-d'));
                                $deadline_date = DateTime::createFromFormat('d/m/Y H:i:s', $deadline . ' 00:00:00');

                                // delete row if course deadline = current day + 3
                                if ($current_date > $deadline_date->modify('+3 day')) {
                                    delete_row($repeater_field, get_row_index(), $post_id);
                                }

                                // update existing courses
                                if (isset($item_data['course_id']) && $item_data['course_id'] == $course_id_array) {
                                    update_post_meta($post_id, $repeater_field . '_' . $k . '_' . $course_date_from, strtotime('+2 hour', $item_data['course_date_from']));
                                    update_post_meta($post_id, $repeater_field . '_' . $k . '_' . $course_date_to, strtotime('+2 hour', $item_data['course_date_to']));
                                    update_post_meta($post_id, $repeater_field . '_' . $k . '_' . $course_location, $item_data['course_location']);

                                    if (isset($item_data['weekend']) && $item_data['weekend'] == 'true') {
                                        update_post_meta($post_id, $repeater_field . '_' . $k . '_' . $course_dayphase, 'weekend');
                                    } else {
                                        update_post_meta($post_id, $repeater_field . '_' . $k . '_' . $course_dayphase, $item_data['course_time']);
                                    }

                                    update_post_meta($post_id, $repeater_field . '_' . $k . '_' . $course_status, isset($item_data['course_status']) ? $item_data['course_status'] : '');
                                    update_post_meta($post_id, $repeater_field . '_' . $k . '_' . $course_few_seats, isset($item_data['few_seats']) ? $item_data['few_seats'] : '');
                                    update_post_meta($post_id, $repeater_field . '_' . $k . '_' . $course_price, $item_data['course_price']);
                                    update_post_meta($post_id, $repeater_field . '_' . $k . '_' . $course_showing, $item_data['course_deadline']);
                                }

                                array_push($existarray, $course_id_array);

                                $k++;
                            endwhile;

                            if (!in_array($item_data['course_id'], $existarray, true) && $item_data['course_id']) {
                                add_row($repeater_field, $values, $post_id);
                            }

                        } else if ($item_data['course_id']) {
                            add_row($repeater_field, $values, $post_id);
                        }

                        if (empty($check_custom_title)) {
                            update_post_meta($post_id, 'custom_course_title', $post_name);
                        }

                        $paged = $page + 1;
                    } else {
                        $post = array(
                            'post_author' => 2,
                            'post_content' => '',
                            'post_status' => "publish",
                            'post_title' => $post_name,
                            'post_parent' => '',
                            'post_type' => 'product'
                        );

                        $post_id = wp_insert_post($post, false);

                        wp_set_object_terms($post_id, 'simple', 'product_type');
                        wp_set_object_terms($post_id, 15, 'product_cat', true); // default product category

                        update_post_meta($post_id, '_visibility', 'visible');
                        update_post_meta($post_id, '_stock_status', 'instock');
                        update_post_meta($post_id, '_virtual', 'yes');
                        update_post_meta($post_id, '_regular_price', "1");
                        update_post_meta($post_id, '_price', "1");
                        update_post_meta($post_id, '_product_attributes', array());
                        update_post_meta($post_id, '_wporg_meta_key', 'enabled'); // default product status

                        add_row($repeater_field, $values, $post_id); // add courses dates rows

                        $paged = $page + 1;
                    }
                }
                $i++;
            }

            $args = array(
                'post_type' => 'product',
                'posts_per_page' => -1,
            );

            $loop = new WP_Query($args);
            while ($loop->have_posts()) : $loop->the_post();
                if ($x >= $offset && $x <= ($offset + $step)) {
                    if (have_rows($repeater_field, get_the_ID())) {
                        while (have_rows($repeater_field, get_the_ID())): the_row();
                            $deadline = get_sub_field('course_deadline', get_the_ID());
                            $current_date = new DateTime(Date('Y-m-d'));
                            $deadline_date = DateTime::createFromFormat('d/m/Y H:i:s', $deadline . ' 00:00:00');
                            if ($current_date > $deadline_date->modify('+3 day')) {
                                delete_row($repeater_field, get_row_index(), get_the_ID());
                            }
                        endwhile;
                    } else {
                        $value = get_post_meta(get_the_ID(), '_wporg_meta_key', true);
                        if ($value === 'enabled') {
                            update_post_meta(get_the_ID(), '_wporg_meta_key', 'hidden');
                        }
                    }
                }
                $x++;
            endwhile;

            while ($loop->have_posts()) : $loop->the_post();
                if (!have_rows($repeater_field, get_the_ID())) {
                    update_post_meta(get_the_ID(), '_wporg_meta_key', 'hidden');
                } else {
                    if (get_post_meta(get_the_ID(), '_wporg_meta_key', true) === 'hidden') {
                        update_post_meta(get_the_ID(), '_wporg_meta_key', 'enabled');
                    }
                }
            endwhile;

            wp_reset_query();

        } else {
            wp_delete_file($file);
            update_field('page', 0, 'option');
            get_products_data();
        }
        update_field('page', $paged, 'option');
//        update_field('name', $post_name, 'option');
    }
}

/* END LOAD COURSES EVENT */

/* CREATE DAILY CRON AND START LOAD COURSES TO .JSON FILE  */
add_action('user_offer_expired_3_notification', 'load_courses_to_file'); // add daily cron and create file .json with data that api provides
if (!wp_next_scheduled('user_offer_expired_3_notification'))
    wp_schedule_event(time(), 'four_hours', 'user_offer_expired_3_notification');
function load_courses_to_file()
{
    global $wp_error;
    $options = get_option('load_courses');
//    if (isset($options)) {
    $data = new X2_TestRestApi();
    $getAllCourses = $data->getCourseDate($options);
    $course_id = "course_id";
    $course_date_from = "course_date_from";
    $course_date_to = "course_date_to";
    $course_location = "course_location";
    $course_dayphase = "course_time";
    $course_status = "course_status";
    $course_few_seats = "few_seats";
    $course_price = "course_price";
    $course_showing = "course_deadline";
    $course_weekend = "weekend";
    $coursetype_technical = "coursetype_technical";
    $coursetype_id = 'coursetype_id';
    $file = dirname(__DIR__) . '/api/temp/courses.json';
    $array = [];
    foreach (call_user_func_array('array_merge', call_user_func_array('array_merge', call_user_func_array('array_merge', $getAllCourses))) as $index => $item_data) {

        if ($item_data['course_id']) {
            $value = array(
                $course_id => $item_data['course_id'],
                $course_date_from => $item_data['startdate'],
                $course_date_to => $item_data['enddate'],
                $course_location => $item_data['locality'],
                $course_dayphase => $item_data['dayphase'],
                $course_status => $item_data['class_status'],
                $course_few_seats => $item_data['few_seats'],
                $course_price => $item_data['price'],
                $course_showing => $item_data['deadline'],
                $course_weekend => $item_data['weekend'],
                $coursetype_technical => $item_data['coursetype_technical'],
                $coursetype_id => $item_data['coursetype_id']
            );
            $emptyRemoved = array_filter($value);
            array_push($array, $emptyRemoved);
        }
    }
    $json = json_encode($array, JSON_UNESCAPED_UNICODE);

    // create courses.json file
    if (!file_exists($file)) {
        file_put_contents($file, $json, FILE_APPEND);
    }
//    }
}

/* END LOAD COURSES TO .JSON FILE  */


// if need delete cron

//wp_unschedule_event(wp_next_scheduled('load_courses_event'), 'load_courses_event');
//wp_unschedule_event(wp_next_scheduled('user_offer_expired_3_notification'), 'user_offer_expired_3_notification');

//wp_clear_scheduled_hook('load_courses_event');
//wp_clear_scheduled_hook('user_offer_expired_3_notification');

/* CREATE daily CRON AND START LOAD COURSES TO .JSON FILE  */
add_action('run_products_data', 'get_products_data'); // add daily cron and create file .json with all dates of course
if (!wp_next_scheduled('run_products_data'))
    wp_schedule_event(time(), 'four_hours', 'run_products_data');
function get_products_data()
{
    $repeater_field = "custom_products";
    $course_date_from = "course_date_from";
    $course_date_to = "course_date_to";
    $course_location = "course_location";
    $course_status = "course_status";

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
    );

    $loop = new WP_Query($args);
    $sub_page_url_array = array();

    while ($loop->have_posts()) : $loop->the_post();
        global $product;

        $post_id = $product->get_id();
        $permalink = get_permalink();

        if (have_rows($repeater_field, $post_id)) {
            $id = 0;

            while (have_rows($repeater_field, $post_id)): the_row();

                $date_from = get_sub_field($course_date_from);
                $date_to = get_sub_field($course_date_to);
                $location = strtolower(str_replace(' / ', "-", get_post_meta($product->get_id(), $repeater_field . '_' . $id . '_' . $course_location, true)));
                $status = get_post_meta($product->get_id(), $repeater_field . '_' . $id . '_' . $course_status, true);
                $product_status = get_post_meta($product->get_id(), '_wporg_meta_key', true);
                $course_date_values = explode(",", $date_from);
                $course_date_mouth = $course_date_values[1];
                $trim = preg_replace("/([0-9]+)|\s+/", '', $course_date_mouth);
                $month_num = str_pad(get_month_number($trim), 2, 0, STR_PAD_LEFT);
                $trim_date_from = preg_replace('([A-zø.]+)', '', $date_from);
                $trim_date_to = explode(".", $date_to);
                $month = $month_num;
                $explode_date_from = explode(", ", $trim_date_from);
                $explode_date_to = $trim_date_to[1];
                $date_structure = trim($explode_date_from[0]) . 'to' . $explode_date_to . '-' . $month . '-' . trim($explode_date_from[1]);

                if ($status && $location && $product_status === 'enabled') {
                    $sub_page_url = $permalink . $location . '/' . $date_structure;
                    array_push($sub_page_url_array, $sub_page_url);
                }
                $id++;
            endwhile;
        }
    endwhile;

    $file = dirname(__DIR__) . '/api/temp/sitemap.json';
    $json = json_encode(array_unique($sub_page_url_array), JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $json);
    wp_reset_query();
}