<?php if ($args['start'] < 10)
    unset($args['installed_payment_methods']['cod']);
$i = 1;
?>

<?php foreach ($args['installed_payment_methods'] as $method): ?>
    <?php
    $method->chosen = false;

    if ($method->title === 'Lånekassen') {
        if ($method->form_fields['restricted_product_ids'] && $method->form_fields['restricted_product_ids']['default']) {
            $allowed_product_ids = explode(',', $method->form_fields['restricted_product_ids']['default']);

            $cart = WC()->cart->get_cart();
            $cart_contains_disallowed_product = false;

            foreach ($cart as $cart_item) {
                $product_id = $cart_item['product_id'];
                if (!in_array($product_id, $allowed_product_ids)) {
                    $cart_contains_disallowed_product = true;
                    break;
                }
            }
            if ($cart_contains_disallowed_product) {
                continue; // Skip this payment method if the cart contains a disallowed product
            }
        }
    }
    ?>

    <div class="checkout-choice__item d-flex align-center mb-2">
        <label><?= $method->title; ?>
            <input type="radio" name="payment_method"
                   id="<?= preg_replace('/([\s+\/]+)/', '', $method->title); ?>"
                   value="<?= $method->id; ?>"
                   data-description="<?= $method->description; ?>"
                <?= ($i === 1) ? 'checked' : ''; ?> >
            <span class="checkmark"></span>
        </label>
    </div>
    <?php $i++; ?>
<?php endforeach; ?>