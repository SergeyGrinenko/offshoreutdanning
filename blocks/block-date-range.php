<?php

$post_id = get_field('choose-course');
$date_from = get_month_number(get_field('date_from'));
$date_to = get_month_number(get_field('date_to'));
$repeater_field = "custom_products";
$repeater = get_field($repeater_field, $post_id);
$range = range($date_from, $date_to);
$status = get_post_meta($post_id, '_wporg_meta_key', true);
$product_data = wc_get_product($post_id);

$row_id = 0;
$course_location = array();
foreach ($repeater as &$item) {
    $item['row_id'] = $row_id;
    array_push($course_location, $item['course_location']);
    $row_id++;
}

sort($course_location)
?>
<input type="hidden" class="currency" value="<?php echo get_woocommerce_currency_symbol(); ?>">
<div class="promo-course range">
    <?php if ($product_data->is_type('variable')): ?>
        <input type="hidden" class="product-type" value="variable">
    <?php endif; ?>
    <div class="grouped_courses">

        <?php if ($status == 'disabled'): ?>
            <div class="p-2 bg-red white not_available">
                Beklager, dette kurset er ikke tilgjengelig.
            </div>
        <?php else: ?>
        <div id="page-lead-row">
            <div class="page-lead">
                <div class="page-lead-text-wrapper">
                    <div class="page-lead-text">
                        <h2>
                            <?php $custom_title = get_field('custom_course_title', $post_id);
                            if (!empty($custom_title)) {
                                echo $custom_title;
                            } else {
                                echo get_the_title($post_id);
                            } ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="main_content p-2">
            <div class="location pb-2">
                <select class="choose-location" data-id="<?php echo $post_id; ?>" data-from="<?php echo $date_from; ?>"
                        data-to="<?php echo $date_to; ?>">
                    <?php foreach (array_unique($course_location) as $location): ?>
                        <option><?php echo $location; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($product_data->is_type('variable')): ?>
                <div class="grid-div-course-variation pb-2">
                    <div class="variable_course bg-washedgrey <?php echo $post_id; ?>">

                        <div class="bg-green p-2">
                            <h3 class="white m-0">Velg hvordan du ønsker å tilegne deg
                                praksis*</h3>
                        </div>

                        <div class="practic_type p-2">
                            <?php
                            $attributes = $product_data->get_attributes('pa_practictype');

                            $values = wc_get_product_terms($product_data->id, 'pa_practictype', array('fields' => 'all'));
                            foreach ($values as $val) { ?>
                                <div class="practic_type__item" data-attr-term="<?php echo $val->name; ?>">
                                    <label for="<?php echo $post_id; ?>_<?php echo $val->term_id; ?>"><?php echo $val->name; ?>
                                        <input type="radio" id="<?php echo $post_id; ?>_<?php echo $val->term_id; ?>"
                                               name="practic_type_<?php echo $post_id; ?>"
                                               value="<?php echo $val->term_id; ?>"
                                               data-description="<?php echo $val->description; ?>">
                                        <span class="checkradio"></span>
                                    </label><br>
                                </div>
                            <?php } ?>


                            <div class="practic_type__item dont_need_practice">
                                <label for="<?php echo $post_id; ?>_dont_need">Jeg trenger ikke trening
                                    <input type="radio" id="<?php echo $post_id; ?>_dont_need"
                                           name="practic_type_<?php echo $post_id; ?>"
                                           value="dont_need">
                                    <span class="checkradio"></span>
                                </label><br>
                            </div>

                        </div>

                        <div class="mashine_type p-2 hidden">

                            <div class="description pb-2"></div>
                            <div style="column-count: 2;">
                                <?php
                                $attributes = $product_data->get_attributes('pa_mashine');

                                $values = wc_get_product_terms($product_data->id, 'pa_mashine', array('fields' => 'all'));
                                $variations_id_array = $product_data->get_children();

                                foreach ($variations_id_array as $variation) { ?>

                                    <?php $variation_obj = wc_get_product($variation); ?>
                                    <?php $_product = new WC_Product_Variation($variation); ?>
                                    <?php $formatted_name = $_product->get_attribute_summary(); ?>

                                    <?php
                                    preg_match_all("/Type:(.[A-z0-9].+)/", $_product->get_attribute_summary(), $matches);
                                    $exlude_type = str_replace('Type:', '', $matches[0][0]);
                                    if (preg_match('(, [A-z]+:)', $exlude_type)) {
                                        $attr_type_by_variation = preg_replace('/(, [A-z0-9].+:.[A-z0-9].+)/', '', $exlude_type);
                                    } else {
                                        $attr_type_by_variation = $exlude_type;
                                    }

                                    if (explode(",", $formatted_name[1])[0] !== $attr_type_by_variation) {
                                        $variation_name = preg_replace('/, Type:([A-z\s].+)/', '', explode('Mashine:', $formatted_name)[1]);
                                    } else {
                                        $variation_name = explode('Mashine:', $formatted_name)[0];
                                    }
                                    ?>


                                    <div class="mashine_type__item hidden"
                                         data-attr-name="<?php echo trim($attr_type_by_variation); ?>">

                                        <label for="<?php echo $post_id; ?>_<?php echo $variation; ?>"><?php echo $variation_name; ?>
                                            <span class="price"> +<?php echo get_woocommerce_currency_symbol(); ?> <input
                                                        type="checkbox"
                                                        id="<?php echo $post_id; ?>_<?php echo $variation; ?>"
                                                        name="mashine_type_<?php echo $post_id; ?>"
                                                        value="<?php echo $variation; ?>"
                                                        data-price="<?php echo $_product->get_price(); ?>">
                                                <span class="checkmark"></span><?php echo $_product->get_price(); ?></span>
                                        </label>
                                    </div>

                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <!-- End Variable product -->

            <div class="grouped_course_wrap first-five promo_item">

                <div class="">
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

                            $keys = array_column($arr_start, 'course_location');
                            $keys2 = array_column($arr_start, 'course_date_from');


                            array_multisort($keys2, SORT_ASC, SORT_NUMERIC, array_multisort($keys, SORT_ASC, $arr_start));
                            foreach ($arr_start as $row) {
                                if ($row['course_location'] === $course_location[0]):
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
                                                <?php echo $course_date . ' til ' . $course_date_to . ' - '.$row['course_location']; ?>
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

                    if (!$row_ids_array) { ?>
                        <div class="p-2 bg-red white not_available">
                            Ingen datoer for dette kurset
                        </div>
                    <?php } else { ?>
                        <div class="promoevent-button"
                             data-id="<?php echo $post_id; ?>"
                             data-grouped_data="<?php echo $post_id; ?>:<?php echo implode(",", $row_ids_array); ?>"
                             data-grouped_row_ids="<?php echo implode(",", $row_ids_array); ?>"
                             data-from="<?php echo get_field('date_from'); ?>"
                             data-to="<?php echo get_field('date_to'); ?>"
                             data-type="grouped">
                            Påmelding
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php endif ?>
        </div>
    </div>

