<?php
global $start;
$start_course = []; ?>

<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents mb-3 w-100" cellspacing="0">

    <tbody>
    <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $start_course[] = $cart_item['start'];
        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
        $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
        $custom_product_name = get_the_title($cart_item['product_id']);
        if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
            $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key); ?>
            <tr class="woocommerce-cart-form__cart-item p-1 <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                <td class="product-name bg-light-1 p-1"
                    data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
                    <?php if (!$product_permalink) {
                        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                        if ($cart_item['few_seats'] > 0) {
                            echo '<div class="few_seats bg-danger text-light text-center">' . __('Få plasser') . '</div>';
                        } ?>
                        <?php if ($cart_item['practices']): ?>
                            <div class="product_info_addons text-dark m-0">
                                <?php foreach ($cart_item['practices'] as $name => $price) {
                                    echo '<div class="bg-primary">+ ' . $name . ' - ' . formatted_price($price) . '</div>';
                                } ?>
                            </div>
                        <?php endif; ?>
                    <?php } else {
                        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s" class="text-dark">%s</a>', esc_url($product_permalink), $custom_product_name), $cart_item, $cart_item_key));
                        if ($cart_item['few_seats'] > 0) {
                            echo '<div class="few_seats bg-danger text-light text-center">' . __('Få plasser') . '</div>';
                        }

                        if (isset($cart_item['practices'])): ?>
                            <div class="product_info_addons text-dark m-0">
                                <?php foreach ($cart_item['practices'] as $name => $price) {
                                    echo '<div class="addon">+ ' . $name . ' - ' . formatted_price($price) . '</div>';
                                } ?>
                            </div>
                        <?php endif;
                    }

                    do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

                    echo wc_get_formatted_cart_item_data($cart_item);

                    if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                        echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification',
                            '<p class="backorder_notification">'
                            . esc_html__('Available on backorder', 'woocommerce') .
                            '</p>', $product_id));
                    } ?>

                    <?php echo '<div>' . $cart_item['location'] . '&nbsp;' . __('fra') . '&nbsp;' . $cart_item['data_from'] . '&nbsp;' . __('til') . '&nbsp;' . strtolower($cart_item['data_to']) . '</div>'; ?>
                </td>

                <td class="product-item-wrapper">
                    <div class="product-price bg-primary text-light text-center p-1"
                         data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                        <?= formatted_price($cart_item['course_price']); ?>
                    </div>

                    <div class="product-remove p-0">
                        <?php
                        echo apply_filters(
                            'woocommerce_cart_item_remove_link',
                            sprintf(
                                '<a href="%s" class="remove p-2 bg-softdark" aria-label="%s" data-product_id="%s" data-product_sku="%s">Fjern</a>',
                                esc_url(wc_get_cart_remove_url($cart_item_key)),
                                esc_html__('Remove this item', 'woocommerce'),
                                esc_attr($product_id),
                                esc_attr($_product->get_sku())
                            ),
                            $cart_item_key
                        ); ?>
                    </div>

                </td>
            </tr>
        <?php }
    }
    $start = min($start_course); ?>

    <tr class="summry-container">
        <td class="sum_name p-0">
            <div class="field-plain summary text-right fw-bold text-pink p-2" data-target="summary-text">
                <?php if (!empty(WC()->cart->get_discount_total())): ?>
                    <?php _e('Total rabatt'); ?><br>
                <?php endif; ?>
                <?php _e('Deltagere'); ?><br>
            </div>
            <div class="field-plain total text-right p-2"><strong>
                    <?php _e('Totalt'); ?></strong></div>
        </td>
        <td class="sum_count p-0">
            <div class="field-price summary p-2 text-center" data-target="summary-price">
                <?php if (!empty(WC()->cart->get_discount_total())): ?>
                    <?= formatted_price(WC()->cart->get_discount_total()) . '<br>'; ?>
                <?php endif; ?>
                <span class="count_members fw-bold text-pink">
    <?= $cart_item['quantity']; ?></span></div>
            <div class="field-price total p-2 text-center" data-target="total-price"
                 data-value="<?= wc_format_decimal(WC()->cart->cart_contents_total, 2); ?>">
                <strong><?= formatted_price(WC()->cart->cart_contents_total); ?></strong>
            </div>
        </td>
    </tr>
    </tbody>
</table>