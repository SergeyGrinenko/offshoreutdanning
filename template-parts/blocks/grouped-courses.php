<?php
/**
 * Grouped Courses Block Template.
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'grouped_courses-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'grouped_courses mb-3';
if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}
if (!empty($block['align'])) {
    $className .= ' align' . $block['align'];
}
$type = false;
$empty = false;
$GetCourses = get_field('courses');
$GetStartDate = get_field('date_from');
$GetEndDate = get_field('date_to');
$Next5 = get_field('add_next_5');
$summ_variations = 0;


//setDatelocale($course['startdate'], 'd l', $duration)

if (!is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) :

    $course_type = array_combine(
        array_values(wp_list_pluck(WC()->cart->get_cart_contents(), 'product_id')),
        array_values(wp_list_pluck(WC()->cart->get_cart_contents(), 'grouped_course'))
    );


    $productsInCart = array_fill_keys(
        array_values(wp_list_pluck(WC()->cart->get_cart_contents(), 'product_id')),
        array_values(wp_list_pluck(WC()->cart->get_cart_contents(), 'course_id'))
    );

    if ($GetCourses): ?>
        <?php foreach ($GetCourses as $course):
            $_product = wc_get_product($course->ID);
            $course_dates = get_post_meta($course->ID, 'product_dates', true);
            $get_dates_range = get_dates_in_range($course_dates, $GetStartDate, $GetEndDate);
            if (array_key_exists($_product->id, $course_type) && $course_type[$_product->id] === true) {
                $type = true;
            } ?>
        <div id="<?php echo esc_attr($id); ?>"
             class="<?php echo esc_attr($className); ?>"
             data-product-id="<?= $course->ID; ?>"
             data-from="<?= $GetStartDate; ?>"
             data-to="<?= $GetEndDate; ?>"
             data-next5="<?= $Next5; ?>">
            <div class="grouped_courses__title p-3 bg-primary text-light">
                <h2 class="m-0"><?= $course->post_title; ?></h2>
            </div>

            <?php if ($course_dates): ?>
            <div class="grouped_courses__body p-3">
                <div class="select-course-locations w-100 mb-3">
                    <select class="bg-gray p-1 text-light w-100">
                        <?php foreach ($get_dates_range as $locality => $dates): ?>
                            <option value="<?= $locality; ?>"><?= $locality; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($_product->is_type('variable')) {
                    $variaption_array = array_combine(array_values(wp_list_pluck(WC()->cart->get_cart_contents(), 'variation_term_id')), array_values(wp_list_pluck(WC()->cart->get_cart_contents(), 'variation_ids')));
                    $variation_term_id = array_values(wp_list_pluck(WC()->cart->get_cart_contents(), 'variation_term_id'));
                    $term_id = null;
                    $practice_types = wc_get_product_terms($_product->id, 'pa_practictype', array('fields' => 'all'));
                    foreach ($practice_types as $key => $value) {
                        if (!empty($variaption_array)) {
                            $term_id = $value->term_id;
                        }
                    } ?>
                    <div class="practise-wrapper mb-3">
                        <div class="practise__heading bg-success text-light p-3">
                            <?php _e('Velg hvordan du ønsker å tilegne deg praksis'); ?><span
                                    class="text-danger">*</span>
                        </div>
                        <div class="practice__body bg-light p-3">
                            <div class="practise-type practice-part"
                                 aria-required="<?= (!is_null($term_id) ? 'false' : 'true') ?>">
                                <?php $attributes = $_product->get_attributes('pa_practictype');
                                $practice_types = wc_get_product_terms($_product->id, 'pa_practictype', array('fields' => 'all'));

                                foreach ($practice_types as $key => $value) { ?>
                                    <div class="mb-2">
                                        <label><?= $value->name; ?>
                                            <input type="radio" name="radio"
                                                   value="<?php echo $value->term_id; ?>" <?= (array_key_exists($value->term_id, $variaption_array) && $type === true ? "checked" : "") ?>>
                                            <span class="checkmark radio"></span>
                                        </label>
                                    </div>
                                <?php } ?>

                                <div class="mb-2">
                                    <label>
                                        <?php _e('Jeg trenger ikke trening'); ?>
                                        <input type="radio" name="radio"
                                               value="no-practise" <?= (array_key_exists('no-practise', $variaption_array) && $type === true ? "checked" : "") ?>>
                                        <span class="checkmark radio"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="practice-variation-wrapper show">
                                <div class="practice-type-message">
                                    <?php foreach ($practice_types as $key => $value) { ?>
                                        <div id="<?php echo $value->term_id; ?>"
                                             class="<?= (array_key_exists($value->term_id, $variaption_array) && $type === true ? "show my-3" : "hide") ?>"><?= $value->description; ?></div>
                                    <?php } ?>
                                </div>
                                <div class="practice-variation practice-part"
                                     aria-required="<?= (!is_null($term_id) ? 'false' : 'true') ?>">
                                    <?php $practice_types = wc_get_product_terms($_product->id, 'pa_mashine', array('fields' => 'all'));
                                    $variations_id_array = $_product->get_children();
                                    foreach ($variations_id_array as $key => $value) {
                                        $variation_obj = wc_get_product($value);
                                        $term_mashine = get_term_by('slug', $variation_obj->attributes['pa_mashine'], 'pa_mashine');
                                        $term_practic = get_term_by('slug', $variation_obj->attributes['pa_practictype'], 'pa_practictype');
                                        if (array_key_exists($term_practic->term_id, $variaption_array)) {
                                            $variation_item = $variaption_array[$term_practic->term_id];
                                            if (is_object($variaption_array[$term_practic->term_id])) {
                                                $variation_item = get_object_vars($variaption_array[$term_practic->term_id]);
                                            }
                                        } ?>
                                        <div class="mb-2 option <?= (array_key_exists($term_practic->term_id, $variaption_array) && $type === true ? "show" : "hide") ?>"
                                             id="<?= $term_practic->term_id; ?>">

                                            <label><?= $term_mashine->name; ?>
                                                <input type="checkbox" id="<?= $term_practic->term_id; ?>"
                                                       name="checkbox"
                                                       data-variation_id="<?= $value; ?>"
                                                       value="<?= $variation_obj->get_price(); ?>"
                                                    <?= (array_key_exists($term_practic->term_id, $variaption_array) &&
                                                        array_key_exists($value, $variation_item) &&
                                                        $variation_item[$value] == $value) ? 'checked' : ''; ?>>
                                                <span class="checkmark"></span>
                                                <span class="variation-price text-gray">+ kr <?= $variation_obj->get_price(); ?></span>
                                            </label>
                                        </div>

                                        <?php if (array_key_exists($term_practic->term_id, $variaption_array) &&
                                            array_key_exists($value, $variation_item) &&
                                            $variation_item[$value] == $value) {
                                            $summ_variations += $variation_obj->get_price();
                                        } ?>

                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="body-dates main">
                    <?php $get_location = array_key_first($get_dates_range); ?>
                    <?php foreach ($get_dates_range as $locality => $dates): ?>
                        <?php if ($locality === $get_location): ?>
                            <?php foreach (array_slice($dates, 0, 5) as $date): ?>
                                <?php $duration = getCourseDuration($date['startdate'], $date['enddate']); ?>
                                <?php $date_structure = setDatelocale($date['startdate'], 'd l', $duration) . ' ' . __('til') . ' ' . setDatelocale($date['enddate'], 'd l, Y', $duration); ?>
                                <div class="grouped-course-item d-flex mb-2 <?= (array_key_exists($course->ID, $productsInCart)) ? in_array($date['course_id'], $productsInCart[$course->ID]) && $type === true ? 'active' : '' : '' ?>"
                                     id="<?= $date['course_id']; ?>"
                                     data-id="<?= $date['course_id']; ?>"
                                     data-year="<?= $date['year']; ?>"
                                     data-month="<?= $date['month']; ?>"
                                     data-type="<?= $date['type']; ?>"
                                     data-group_type="main"
                                     data-index="<?= $date['index']; ?>">
                                    <div class="bg-info w-100 grouped-course-item__label p-1">
                                        <?= $date_structure; ?> - <?= $date['locality'] ?>
                                    </div>
                                    <div class="bg-primary text-light grouped-course-item__time p-1 text-center"><?= getDayphase($date['dayphase']); ?></div>
                                    <div class="bg-warning text-light grouped-course-item__price p-1 text-center"><?= formatted_price($date['price']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif ?>
                    <?php endforeach; ?>
                    <div class="w-100 text-center">
                        <a class="pamelding bg-success text-light px-3 w-100 py-2 mt-3 d-block rounded7"><?php _e('Påmelding'); ?></a>
                    </div>
                </div>


            </div>
        <?php endif; ?>


            <?php if ($Next5 === true): ?>
            <div class="grouped_courses__title p-3 bg-primary text-light">
                <h2 class="m-0"><?= $course->post_title; ?> (+5)</h2>
            </div>

            <?php if ($course_dates): ?>
                <div class="grouped_courses__body p-3">
                    <div class="body-dates next5">
                        <?php $get_location = array_key_first($get_dates_range);
                        ?>
                        <?php foreach ($get_dates_range as $locality => $dates): ?>
                            <?php if ($locality === $get_location): ?>
                                <?php if (!array_slice($dates, 5, 5)): $empty = true; endif; ?>
                                <?php foreach (array_slice($dates, 5, 5) as $date): ?>
                                    <?php $duration = getCourseDuration($date['startdate'], $date['enddate']); ?>
                                    <?php $date_structure = setDatelocale($date['startdate'], 'd l', $duration) . ' ' . __('til') . ' ' . setDatelocale($date['enddate'], 'd l, Y', $duration); ?>
                                    <div class="grouped-course-item d-flex mb-2 <?= (array_key_exists($course->ID, $productsInCart)) ? in_array($date['course_id'], $productsInCart[$course->ID]) ? 'active' : '' : '' ?>"
                                         id="<?= $date['course_id']; ?>"
                                         data-id="<?= $date['course_id']; ?>"
                                         data-year="<?= $date['year']; ?>"
                                         data-month="<?= $date['month']; ?>"
                                         data-type="<?= $date['type']; ?>"
                                         data-group_type="next5"
                                         data-index="<?= $date['index']; ?>">
                                        <div class="bg-info w-100 grouped-course-item__label p-1">
                                            <?= $date_structure; ?> - <?= $date['locality'] ?>
                                        </div>
                                        <div class="bg-primary text-light grouped-course-item__time p-1 text-center"><?= getDayphase($date['dayphase']); ?></div>
                                        <div class="bg-warning text-light grouped-course-item__price p-1 text-center"><?= formatted_price($date['price']); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif ?>
                        <?php endforeach; ?>
                        <?php if ($empty === true): ?>
                            <div class="not-found p-1 bg-danger text-light"><?php _e('Sorry, dates not found.'); ?></div>
                        <?php endif ?>
                        <?php if ($empty === false): ?>
                            <div class="w-100 text-center">
                                <a class="pamelding bg-success text-light px-3 w-100 py-2 mt-3 d-block rounded7"><?php _e('Påmelding'); ?></a>
                            </div>
                        <?php endif ?>
                    </div>

                </div>
            <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>