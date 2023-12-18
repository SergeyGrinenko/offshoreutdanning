<?php
global $product;
$post_id = get_field('choose_next_5_courses');
$repeater_field = "custom_products";
$repeater = get_field($repeater_field, $post_id);
$product_data = wc_get_product($post_id);

foreach (parse_blocks(get_the_content()) as $block) {
    if ($block['blockName'] === 'acf/block-date-range') {
        $date_from = get_month_number($block['attrs']['data']['date_from']);
        $date_to = get_month_number($block['attrs']['data']['date_to']);
    }
}
$range = range($date_from, $date_to);

$row_id = 0;
$course_location = array();
foreach ($repeater as &$item) {
    $item['row_id'] = $row_id;
    array_push($course_location, $item['course_location']);
    $row_id++;
}
sort($course_location);
$status = get_post_meta($post_id, '_wporg_meta_key', true);
if ($status == 'disabled'): ?>
    <div class="p-2 bg-red white not_available">
        Beklager, dette kurset er ikke tilgjengelig.
    </div>
<?php else:
    if ($date_from && $date_to):?>
        <div id="page-lead-row">
            <div class="page-lead">
                <div class="page-lead-text-wrapper">
                    <div class="page-lead-text">
                        <h2>
                            <?php $custom_title = get_field('custom_course_title', $post_id);
                            if (!empty($custom_title)) {
                                echo $custom_title . ' (+5)';
                            } else {
                                echo get_the_title($post_id) . ' (+5)';
                            } ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Start Variable product -->

        <?php if ($product_data->is_type('variable')): ?>
            <input type="hidden" class="product-type" value="variable">
        <?php endif; ?>
        <div class="main_content p-2">
            <div class="mt-2 mb-2 row grouped_course_wrap next-five promo_item">

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

                                if ($row['course_location'] === $course_location[0]):
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
                        <div class="p-2 bg-red white not_available 2">
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
            </div>
        </div>

    <?php else: ?>
        <div class="p-2 bg-red white not_available 2">
            Ingen datoer for dette kurset
        </div>
    <?php endif; ?>
<?php endif; ?>
</div>
