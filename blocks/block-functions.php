<?php
/* Start Custom Gutenberg Blocks */


add_action('acf/init', 'gutenberg_block_courses');
function gutenberg_block_courses()
{
    // Check function exists.
    if (function_exists('acf_register_block_type')) {
        // register a testimonial block.
        acf_register_block_type(array(
            'name' => 'block-date-range',
            'title' => __('Date Range'),
            'render_template' => '/blocks/block-date-range.php',
            'category' => 'promotion',
            'icon' => 'admin-comments',
        ));

        acf_register_block_type(array(
            'name' => 'block-add-next-5',
            'title' => __('Add next 5'),
            'render_template' => '/blocks/block-add-next-5.php',
            'category' => 'promotion',
            'icon' => 'admin-comments',
        ));

        acf_register_block_type(array(
            'name' => 'block-multicoise-courses',
            'title' => __('Multichoice Courses'),
            'render_template' => '/blocks/block-multicoise-courses.php',
            'category' => 'promotion',
            'icon' => 'admin-comments',
        ));
    }
}

if (is_admin()) {
    add_filter('acf/load_field/name=choose-course', 'acf_load_products_to_gutenberg');
    add_filter('acf/load_field/name=choose_courses', 'acf_load_products_to_gutenberg');
    add_filter('acf/load_field/name=choose_next_5_courses', 'acf_load_products_to_gutenberg');
    add_filter('acf/load_field/name=course_location', 'acf_load_locations_field');
}
function acf_load_products_to_gutenberg($field)
{
    // reset choices
    $field['choices'] = array();

    $courses = array();
    $args = array('post_type' => 'product', 'posts_per_page' => -1,);
    $loop = new WP_Query($args);

    while ($loop->have_posts()) : $loop->the_post();
        $array = array(
            'label' => get_the_title(),
            'value' => get_the_ID(),
        );
        array_push($courses, $array);
    endwhile;

    foreach ($courses as $course) {

        // vars
        $label = $course['label'];
        $value = $course['value'];
        // append to choices
        $field['choices'][$value] = $label;
    }

    // return the field
    return $field;
}

function acf_load_locations_field($field)
{
    $location_file = dirname(__FILE__) . '/../api/temp/locations.json';
    if (file_exists($location_file)) {
        $field['choices'] = array();
        $strJsonFileContents1 = file_get_contents($location_file);
        $full_data1 = json_decode($strJsonFileContents1, true, 512, JSON_UNESCAPED_UNICODE);
        foreach ($full_data1 as $key => $getLocation) {
            $field['choices'][$key] = $getLocation;
        }
        return $field;
    }

}


add_action('wp_ajax_grouped_location', 'grouped_location_function');
add_action('wp_ajax_nopriv_grouped_location', 'grouped_location_function');
function grouped_location_function()
{
    $location = $_POST['location'];
    $post_id = $_POST['post_id'];

    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];

    $repeater_field = "custom_products";
    $repeater = get_field($repeater_field, $post_id);
    $range = range($date_from, $date_to);

    $row_id = 0;
    foreach ($repeater as &$item) {
        $item['row_id'] = $row_id;
        $row_id++;
    } ?>

    <div>
        <?php
        $actual_loop = [];

        foreach ($repeater as $row) {
            if (idate("m") <= parse_date(strtotime($row['course_date_from']))['month_number']) {
                if (!isset($actual_loop[parse_date(strtotime($row['course_date_from']))['month_number']])) {
                    $actual_loop[parse_date(strtotime($row['course_date_from']))['month_number']] = [
                        'date' => $row['course_date_from'],
                        'number' => parse_date(strtotime($row['course_date_from']))['month_number'],
                        'date_num' => parse_date(strtotime($row['course_date_from']))['number'],
                        'rows' => []
                    ];
                }
                $actual_loop[parse_date(strtotime($row['course_date_from']))['month_number']]['rows'][] = $row;
            }

        }

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
        $orderbydate = "date";

        array_multisort($sortArray[$orderbydate], SORT_ASC, array_multisort($sortArray[$orderby], SORT_ASC, $actual_loop));


        $i = 0;
        $row_ids_array = array();


        foreach ($actual_loop as $item) {
            if (in_array($item['number'], $range)) {
                $arr_start = $item['rows'];
//                $keys = array_column($arr_start, 'course_location');
//                array_multisort($keys, SORT_ASC, $arr_start);

                foreach ($actual_loop as $item) {
                    if (in_array($item['number'], $range)) {

                        $arr_start = $item['rows'];

                        $keys = array_column($arr_start, 'course_location');
                        $keys2 = array_column($arr_start, 'course_date_from');


                        array_multisort($keys2, SORT_ASC, SORT_NUMERIC, array_multisort($keys, SORT_ASC, $arr_start));
                        foreach ($arr_start as $row) {
                            if ($row['course_location'] === $location):
                                $current_date = new DateTime(Date('Y-m-d'));
                                $deadline_date = DateTime::createFromFormat('d/m/Y H:i:s', $row['course_deadline'] . ' 00:00:00');
                                if ($current_date <= $deadline_date->modify('+3 day')) {


                                    $course_time = $row['course_time'];
                                    if ($course_time === 'day') {
                                        $course_time = 'Dagtid';
                                    } else if ($course_time === 'evening') {
                                        $course_time = 'Kveld';
                                    }
                                    $i++;
                                    if ($i > 5):break;endif;
                                    array_push($row_ids_array, $row['row_id']); ?>

                                    <div class="calendar-date-promo" data-id="<?php echo $post_id ?>"
                                         data-row-id="<?php echo $row['row_id']; ?>"
                                         data-row-location="<?php echo $row['course_location']; ?>">
                                        <span class="daterange" data-row-id="0"
                                              data-row-location="<?php echo $row['course_location']; ?>">
                                            <?php $diff = abs(parse_date(strtotime($row['course_date_from']))['month_number'] - parse_date(strtotime($row['course_date_to']))['month_number']); ?>
                                            <?php if ($diff > 2): ?>
                                                <?php
                                                $course_date = parse_date(strtotime($row['course_date_from']), 'd. F');
                                                $course_date_to = parse_date(strtotime($row['course_date_to']), 'd. F Y');
                                                ?>
                                                <?php echo $course_date . ' til ' . $course_date_to; ?>
                                            <?php else: ?>
                                                <?php
                                                $course_date = parse_date(strtotime($row['course_date_from']), 'l d.');
                                                $course_date_to = parse_date(strtotime($row['course_date_to']), 'l d. F Y');
                                                ?>
                                                <?php echo $course_date . ' til ' . $course_date_to . ' - ' . $row['course_location']; ?>
                                            <?php endif; ?>
                                        </span>

                                        <span class="timeofday text-center"
                                              data-row-id="<?php echo $row['row_id']; ?>"
                                              data-row-location="<?php echo $row['course_location']; ?>"><?php echo $course_time; ?></span>
                                        <?php if ($row['few_seats'] == 1): ?>
                                            <span class="few_seats white"
                                                  data-row-id="<?php echo $row['row_id']; ?>"
                                                  data-row-location="<?php echo $row['course_location']; ?>">Få plasser</span>
                                        <?php else: ?>
                                            <span class="price"
                                                  data-default-price="<?php echo str_replace('.00', '', $row['course_price']); ?>"
                                                  data-row-id="<?php echo $row['row_id']; ?>"
                                                  data-row-location="<?php echo $row['course_location']; ?>">
                                              <?php echo str_replace(',00', ',-', wc_price($row['course_price'])); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php
                                }

                            endif;
                        }
                    }
                }
            }
        }

        if (!$row_ids_array) { ?>
            <div class="p-2 bg-red white not_available">
                Ingen datoer for dette kurset
            </div>
        <?php } else { ?>
            <div class="promoevent-button"
                 data-id="<?php echo $post_id; ?>"
                 data-grouped_data="<?php echo $post_id; ?>:<?php echo implode(",", $row_ids_array); ?>"
                 data-grouped_row_ids="<?php echo implode(",", $row_ids_array); ?>"
                 data-from="<?php echo $date_from; ?>"
                 data-to="<?php echo $date_to; ?>"
                 data-type="grouped">
                Påmelding
            </div>
        <?php } ?>
    </div>

    <?php
    wp_die();
}

/* ===== NEXT FIVE ===== */

add_action('wp_ajax_next_five', 'next_five_function');
add_action('wp_ajax_nopriv_next_five', 'next_five_function');
function next_five_function()
{
    $location = $_POST['location'];
    $post_id = $_POST['post_id'];
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];
    $repeater_field = "custom_products";
    $repeater = get_field($repeater_field, $post_id);
    $range = range($date_from, $date_to);

    $row_id = 0;
    foreach ($repeater as &$item) {
        $item['row_id'] = $row_id;
        $row_id++;
    } ?>

    <div class="" data-type="grouped">
        <?php
        $actual_loop = [];

        foreach ($repeater as $row) {
            if (idate("m") <= parse_date(strtotime($row['course_date_from']))['month_number']) {
                if (!isset($actual_loop[parse_date(strtotime($row['course_date_from']))['month_number']])) {
                    $actual_loop[parse_date(strtotime($row['course_date_from']))['month_number']] = [
                        'date' => $row['course_date_from'],
                        'number' => parse_date(strtotime($row['course_date_from']))['month_number'],
                        'date_num' => parse_date(strtotime($row['course_date_from']))['number'],
                        'rows' => []
                    ];
                }
                $actual_loop[parse_date(strtotime($row['course_date_from']))['month_number']]['rows'][] = $row;
            }

        }

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
        $orderbydate = "date";

        array_multisort($sortArray[$orderbydate], SORT_ASC, array_multisort($sortArray[$orderby], SORT_ASC, $actual_loop));


        $i = 0;
        $row_ids_array = array();

        $count = 0;
        foreach ($actual_loop as $item) {
            if (in_array($item['number'], $range)) {

                $arr_start = $item['rows'];

                $keys = array_column($arr_start, 'course_location');
                $keys2 = array_column($arr_start, 'course_date_from');


                array_multisort($keys2, SORT_ASC, SORT_NUMERIC, array_multisort($keys, SORT_ASC, $arr_start));
                foreach ($arr_start as $key => $row) {

                    if ($row['course_location'] === $location):
                        $current_date = new DateTime(Date('Y-m-d'));
                        $deadline_date = DateTime::createFromFormat('d/m/Y H:i:s', $row['course_deadline'] . ' 00:00:00');
                        if ($current_date <= $deadline_date->modify('+3 day')) {
                            $count++; // Note that first iteration is $count = 1 not 0 here.
                            if ($count <= 5) continue;

                            $course_time = $row['course_time'];
                            if ($course_time === 'day') {
                                $course_time = 'Dagtid';
                            } else if ($course_time === 'evening') {
                                $course_time = 'Kveld';
                            }
                            $i++;
                            if ($i > 5):break;endif;
                            array_push($row_ids_array, $row['row_id']); ?>

                            <div class="calendar-date-promo" data-id="<?php echo $post_id ?>"
                                 data-row-id="<?php echo $row['row_id']; ?>"
                                 data-row-location="<?php echo $row['course_location']; ?>">
                                        <span class="daterange" data-row-id="0"
                                              data-row-location="<?php echo $row['course_location']; ?>">
                                            <?php $diff = abs(parse_date(strtotime($row['course_date_from']))['month_number'] - parse_date(strtotime($row['course_date_to']))['month_number']); ?>
                                            <?php if ($diff > 2): ?>
                                                <?php
                                                $course_date = parse_date(strtotime($row['course_date_from']), 'd. F');
                                                $course_date_to = parse_date(strtotime($row['course_date_to']), 'd. F Y');
                                                ?>
                                                <?php echo $course_date . ' til ' . $course_date_to; ?>
                                            <?php else: ?>
                                                <?php
                                                $course_date = parse_date(strtotime($row['course_date_from']), 'l d.');
                                                $course_date_to = parse_date(strtotime($row['course_date_to']), 'l d. F Y');
                                                ?>
                                                <?php echo $course_date . ' til ' . $course_date_to . ' - ' . $row['course_location']; ?>
                                            <?php endif; ?>
                                        </span>

                                <span class="timeofday text-center"
                                      data-row-id="<?php echo $row['row_id']; ?>"
                                      data-row-location="<?php echo $row['course_location']; ?>"><?php echo $course_time; ?></span>
                                <?php if ($row['few_seats'] == 1): ?>
                                    <span class="few_seats white"
                                          data-row-id="<?php echo $row['row_id']; ?>"
                                          data-row-location="<?php echo $row['course_location']; ?>">Få plasser</span>
                                <?php else: ?>
                                    <span class="price"
                                          data-default-price="<?php echo str_replace('.00', '', $row['course_price']); ?>"
                                          data-row-id="<?php echo $row['row_id']; ?>"
                                          data-row-location="<?php echo $row['course_location']; ?>">
                                              <?php echo str_replace(',00', ',-', wc_price($row['course_price'])); ?>
                                            </span>
                                <?php endif; ?>
                            </div>

                            <?php
                        }

                    endif;
                }
            }
        } ?>


        <?php if (!$row_ids_array) { ?>
            <div class="p-2 bg-red white not_available">
                Ingen datoer for dette kurset
            </div>
        <?php } else { ?>
            <div class="promoevent-button"
                 data-id="<?php echo $post_id; ?>"
                 data-grouped_data="<?php echo $post_id; ?>:<?php echo implode(",", $row_ids_array); ?>"
                 data-grouped_row_ids="<?php echo implode(",", $row_ids_array); ?>"
                 data-from="<?php echo $date_from; ?>"
                 data-to="<?php echo $date_to; ?>"
                 data-type="grouped">
                Påmelding
            </div>
        <?php } ?>
    </div>

    <?php
    wp_die();
}


/*=========== MULTI COURSES ==========*/

add_action('wp_ajax_multi_courses', 'multi_courses_function');
add_action('wp_ajax_nopriv_multi_courses', 'multi_courses_function');
function multi_courses_function()
{
    $location = $_POST['location'];
    $post_id = $_POST['post_id'];
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];
    $repeater_field = "custom_products";
    $repeater = get_field($repeater_field, $post_id);
    $range = range($date_from, $date_to);
    $row_id = 0;

    foreach ($repeater as &$item) {
        $item['row_id'] = $row_id;
        $row_id++;
    } ?>


    <div class="" data-type="grouped">
        <?php
        $actual_loop = [];

        foreach ($repeater as $row) {
            if (idate("m") <= parse_date(strtotime($row['course_date_from']))['month_number']) {
                if (!isset($actual_loop[parse_date(strtotime($row['course_date_from']))['month_number']])) {
                    $actual_loop[parse_date(strtotime($row['course_date_from']))['month_number']] = [
                        'date' => $row['course_date_from'],
                        'number' => parse_date(strtotime($row['course_date_from']))['month_number'],
                        'date_num' => parse_date(strtotime($row['course_date_from']))['number'],
                        'rows' => []
                    ];
                }
                $actual_loop[parse_date(strtotime($row['course_date_from']))['month_number']]['rows'][] = $row;
            }

        }

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
        $orderbydate = "date";

        array_multisort($sortArray[$orderbydate], SORT_ASC, array_multisort($sortArray[$orderby], SORT_ASC, $actual_loop));


        $i = 0;
        $row_ids_array = array();


        foreach ($actual_loop as $item) {
            if (in_array($item['number'], $range)) {
                $arr_start = $item['rows'];
//                $keys = array_column($arr_start, 'course_location');
//                array_multisort($keys, SORT_ASC, $arr_start);

                foreach ($actual_loop as $item) {
                    if (in_array($item['number'], $range)) {

                        $arr_start = $item['rows'];

                        $keys = array_column($arr_start, 'course_location');
                        $keys2 = array_column($arr_start, 'course_date_from');


                        array_multisort($keys2, SORT_ASC, SORT_NUMERIC, array_multisort($keys, SORT_ASC, $arr_start));
                        foreach ($arr_start as $row) {
                            if ($row['course_location'] === $location):
                                $current_date = new DateTime(Date('Y-m-d'));
                                $deadline_date = DateTime::createFromFormat('d/m/Y H:i:s', $row['course_deadline'] . ' 00:00:00');
                                if ($current_date <= $deadline_date->modify('+3 day')) {


                                    $course_time = $row['course_time'];
                                    if ($course_time === 'day') {
                                        $course_time = 'Dagtid';
                                    } else if ($course_time === 'evening') {
                                        $course_time = 'Kveld';
                                    }
                                    $i++;
                                    if ($i > 5):break;endif;
                                    array_push($row_ids_array, $row['row_id']); ?>

                                    <div class="calendar-date-promo" data-id="<?php echo $post_id ?>"
                                         data-row-id="<?php echo $row['row_id']; ?>"
                                         data-row-location="<?php echo $row['course_location']; ?>">
                                        <span class="daterange" data-row-id="0"
                                              data-row-location="<?php echo $row['course_location']; ?>">
                                            <?php $diff = abs(parse_date(strtotime($row['course_date_from']))['month_number'] - parse_date(strtotime($row['course_date_to']))['month_number']); ?>
                                            <?php if ($diff > 2): ?>
                                                <?php
                                                $course_date = parse_date(strtotime($row['course_date_from']), 'd. F');
                                                $course_date_to = parse_date(strtotime($row['course_date_to']), 'd. F Y');
                                                ?>
                                                <?php echo $course_date . ' til ' . $course_date_to; ?>
                                            <?php else: ?>
                                                <?php
                                                $course_date = parse_date(strtotime($row['course_date_from']), 'l d.');
                                                $course_date_to = parse_date(strtotime($row['course_date_to']), 'l d. F Y');
                                                ?>
                                                <?php echo $course_date . ' til ' . $course_date_to . ' - ' . $row['course_location']; ?>
                                            <?php endif; ?>
                                        </span>

                                        <span class="timeofday text-center"
                                              data-row-id="<?php echo $row['row_id']; ?>"
                                              data-row-location="<?php echo $row['course_location']; ?>"><?php echo $course_time; ?></span>
                                        <?php if ($row['few_seats'] == 1): ?>
                                            <span class="few_seats white"
                                                  data-row-id="<?php echo $row['row_id']; ?>"
                                                  data-row-location="<?php echo $row['course_location']; ?>">Få plasser</span>
                                        <?php else: ?>
                                            <span class="price"
                                                  data-default-price="<?php echo str_replace('.00', '', $row['course_price']); ?>"
                                                  data-row-id="<?php echo $row['row_id']; ?>"
                                                  data-row-location="<?php echo $row['course_location']; ?>">
                                              <?php echo str_replace(',00', ',-', wc_price($row['course_price'])); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php
                                }

                            endif;
                        }
                    }
                }
            }
        }
        if (!$row_ids_array) { ?>
            <div class="p-2 bg-red white not_available">
                Ingen datoer for dette kurset
            </div>
        <?php } else { ?>
            <div class="promoevent-button"
                 data-id="<?php echo $post_id; ?>"
                 data-grouped_data="<?php echo $post_id; ?>:<?php echo implode(",", $row_ids_array); ?>"
                 data-grouped_row_ids="<?php echo implode(",", $row_ids_array); ?>"
                 data-from="<?php echo $date_from; ?>"
                 data-to="<?php echo $date_to; ?>"
                 data-type="grouped">
                Påmelding
            </div>
        <?php } ?>
    </div>

    <?php
    wp_die();
}

/* End Custom Gutenberg Blocks */
?>