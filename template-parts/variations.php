<?php
/**
 * @var array $args
 */
global $product;
if ($args['grid_area']) {
    $grid_area = $args['grid_area'];
}
if ($args['summ_variations']) {
    $summ_variations = $args['summ_variations'];
}

$getVariations = getProductVariation($product->get_id());



$cart_contents = WC()->cart->get_cart_contents();
$variaption_array = array_combine(array_column($cart_contents, 'variation_term_id'), array_column($cart_contents, 'variation_ids'));

$variation_term_id = array_column($cart_contents, 'variation_term_id');
$term_id = null;

$practice_types = wc_get_product_terms($product->get_id(), 'pa_practice_type', array('fields' => 'all'));

if (!empty($variaption_array)) {
    foreach ($practice_types as $value) {
        $term_id = $value->term_id;
    }
} ?>
<div class="singlecourse__practice practise-wrapper mb-3 rounded7"
     style="grid-area: <?= $grid_area; ?>">
    <div class="practise__heading bg-pink text-light-1 p-3">
        <?php _e('Velg hvordan du ønsker å tilegne deg praksis'); ?>
        <span class="text-danger">*</span>
    </div>
    <div class="practice__body bg-light-1 p-3">
        <div class="practise-type practice-part"
             >
            <?php $attributes = $product->get_attributes('pa_practice_type');
            $practice_types = wc_get_product_terms($product->get_id(), 'pa_practice_type', array('fields' => 'all'));
            foreach ($practice_types as $key => $value) { ?>
                <div class="mb-2">
                    <label><?= $value->name; ?>
                        <input type="radio" name="radio"
                               value="<?php echo $value->term_id; ?>">
                        <span class="checkmark radio"></span>
                    </label>
                </div>
            <?php } ?>

            <div class="mb-2">
                <label>
                    <?php _e('Jeg trenger ikke trening'); ?>
                    <input type="radio" name="radio"
                           value="no-practise">
                    <span class="checkmark radio"></span>
                </label>
            </div>
        </div>

        <div class="practice-variation-wrapper mt-3 show">
            <div class="practice-type-message">
                <?php foreach ($practice_types as $key => $value) { ?>
                    <div id="<?php echo $value->term_id; ?>"
                         class="hide"><?= $value->description; ?></div>
                <?php } ?>
            </div>

            <div class="practice-variation mt-3 practice-part">
                <?php $practice_types = wc_get_product_terms($product->get_id(), 'pa_practice', array('fields' => 'all'));
                $variations_id_array = $product->get_children();

                foreach ($variations_id_array as $key => $value) {
                    $variation_obj = wc_get_product($value);
                    if ($variation_obj) {
                        $term_mashine = get_term_by('slug', $variation_obj->get_attribute('pa_practice'), 'pa_practice');
                        $term_practic = get_term_by('slug', $variation_obj->get_attribute('pa_practice_type'), 'pa_practice_type');

                        if ($term_mashine && $term_practic) {
                            if (array_key_exists($term_practic->term_id, $variaption_array)) {
                                $variation_item = json_decode(stripslashes($variaption_array[$term_practic->term_id]), true);
                                if (is_object($variaption_array[$term_practic->term_id])) {
                                    $variation_item = get_object_vars($variaption_array[$term_practic->term_id]);
                                }
                            }

                            ?>
                            <div class="mb-2 option hide"
                                 id="<?= $term_practic->term_id; ?>">
                                <label><?= $term_mashine->name; ?>
                                    <input type="checkbox"
                                           id="<?= $term_practic->term_id; ?>"
                                           name="checkbox"
                                           data-variation_id="<?= $value; ?>"
                                           value="<?= $variation_obj->get_price(); ?>">
                                    <span class="checkmark"></span>
                                    <span class="variation-price text-gray">+ <?= $variation_obj->get_price_html(); ?></span>
                                </label>
                            </div>

                            <?php if (isset($term_practic->term_id) && isset($variaption_array) && isset($value) && isset($variation_item) && array_key_exists($term_practic->term_id, $variaption_array) &&
                                array_key_exists($value, $variation_item) &&
                                $variation_item[$value] == $value) {
//                                    $summ_variations += $variation_obj->get_price();
                            }
                        }
                    }
                } ?>
            </div>
        </div>
    </div>
</div>
