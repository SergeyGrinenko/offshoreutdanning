<?php /** @var $args */
$title = str_replace(' ', '-', strtolower($args->title));

?>
<div>
    <div class="payment_method d-flex mb-1" data-type="<?= $title ?>">
        <div class="payment_field field-radio d-flex align-items-center justify-content-center"></div>
        <div class="payment_field bg-ligthgrey d-flex align-items-center field-label"><?= $args->title ?></div>
        <input type="radio" name="payment_field_radio" class="required radio_btn" required
               style="visibility: hidden">
        <input id="payment_method_<?= esc_attr($args->id) ?>" type="radio" name="payment_method"
               class="required radio_btn input-radio" required style="visibility: hidden"
               value="<?= esc_attr($args->id) ?>">
    </div>
    <div class="info-box">
        <p><?= $args->description; ?></p>
    </div>
</div>