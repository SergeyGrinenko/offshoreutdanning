<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">

    <thead>

    <tr>
        <th class="product-remove"></th>
        <th class="product-name"><?php esc_html_e('Kurs', 'woocommerce'); ?></th>
        <th class="product-location"><?php esc_html_e('Sted', 'woocommerce'); ?></th>
        <th class="product-date"><?php esc_html_e('Dato', 'woocommerce'); ?></th>
        <th class="product-time"></th>
        <th class="product-price"><?php esc_html_e('Pris', 'woocommerce'); ?></th>
    </tr>
    </thead>

    <tbody>

    <?php do_action('woocommerce_before_cart_contents'); ?>

    <?php

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

        $get_time = $cart_item['data_time'];
        if ($get_time === 'day') {
            $get_time = 'Dagtid';
        } else if ($get_time === 'evening') {
            $get_time = 'Kveld';
        }

        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
        $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
        $custom_product_name = get_field('custom_course_title', $cart_item['product_id']);

        if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
            $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key); ?>

            <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

                <td class="product-remove">
                    <?php
                    echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        'woocommerce_cart_item_remove_link',
                        sprintf(
                            '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><img src="' . get_stylesheet_directory_uri() . '/assets/images/icon-close.svg"></a>',
                            esc_url(wc_get_cart_remove_url($cart_item_key)),
                            esc_html__('Remove this item', 'woocommerce'),
                            esc_attr($product_id),
                            esc_attr($_product->get_sku())
                        ),
                        $cart_item_key
                    );
                    ?>
                </td>

                <td class="product-name" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">

                    <?php if ($cart_item['few_seats'] == 1): ?>
                        <span class="few_seats text-center pl-1 pr-1 mr-2 white">Få Plasser</span>
                    <?php endif; ?>

                    <?php
                    if (!$product_permalink) {
                        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');

                        if ($cart_item['variation_name']): ?>
                            <p class="product_info_addons white pt-1 m-0">Praksis:
                                <?php foreach ($cart_item['variation_name'] as $item) {
                                    echo $item['name'] . ' - ' . get_woocommerce_currency_symbol() . $item['price'] . ';';
                                } ?>
                            </p>
                        <?php endif;


                    } else {
                        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $custom_product_name), $cart_item, $cart_item_key));
                        if ($cart_item['variation_name']): ?>
                            <p class="product_info_addons white pt-1 m-0">Praksis:
                                <?php foreach ($cart_item['variation_name'] as $item) {
                                    echo $item['name'] . ' - ' . get_woocommerce_currency_symbol() . $item['price'] . ';';
                                } ?>
                            </p>
                        <?php endif;
                    }

                    do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

                    // Meta data.
                    echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

                    // Backorder notification.
                    if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                        echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p
                    class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') .
                            '</p>', $product_id));
                    }


                    ?>
                </td>

                <td class="product-location">
                    <?php echo '<div>' . $cart_item['location'] . '</div>'; ?>
                </td>

                <td class="product-date">

                    <?php echo $cart_item['data_from'] . ' til ' . $cart_item['data_to']; ?>

                </td>

                <td class="product-time white">
                    <?php echo '<div>' . $get_time . '</div>' ?>
                </td>

                <td class="product-price white" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                    <?php
                    echo apply_filters('woocommerce_cart_item_price', str_replace(',00', ',-', WC()->cart->get_product_price($_product)), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                    ?>
                </td>
            </tr>

            <input type="hidden"
                   name="cource_id"
                   id="cource_id"
                   value="<?php echo $product_id; ?>">

            <input type="hidden"
                   name="cource_date_<?php echo $product_id; ?>"
                   id="cource_date"
                   value="  <?php echo $cart_item['data_from'] . ' til ' . $cart_item['data_to']; ?>">

            <input type="hidden"
                   name="cource_location_<?php echo $product_id; ?>"
                   id="cource_location"
                   value="<?php echo $cart_item['location']; ?>">

            <input type="hidden"
                   name="cource_time_<?php echo $product_id; ?>"
                   id="cource_time"
                   value="<?php echo $cart_item['data_time']; ?>">

            <input type="hidden"
                   name="course_price_<?php echo $product_id; ?>"
                   id="course_price"
                   value="<?php echo $cart_item['custom_price']; ?>">

            <input type="hidden"
                   name="few_seats_<?php echo $product_id; ?>"
                   id="few_seats"
                   value="<?php echo $cart_item['few_seats']; ?>">

        <?php }
    }
    ?>

    <?php do_action('woocommerce_cart_contents'); ?>

    <?php $total_val = str_replace(',00', ',-', wc_price(WC()->cart->total)); ?>

    <tr class="summry-container">
        <td colspan="5" class="sum_name">

            <div class="field-plain summary text-right"
                 data-target="summary-text"><span
                        class="discount-status <?= (empty(WC()->cart->applied_coupons) ? 'hidden' : '') ?> ">Rabatt<br></span>
                Sum kurs<br>Deltagere<br>
            </div>
            <div class="field-plain total text-right"><strong>Totalt</strong></div>
        </td>
        <td class="sum_count">
            <div class="field-price summary bg-softgerey white" data-target="summary-price">
                <span class="discount-status <?= (empty(WC()->cart->applied_coupons) ? 'hidden' : '') ?>">

                    <?php $persent = 0;
                    foreach (WC()->cart->get_applied_coupons() as $coupon_code) {
                        $coupon = new WC_Coupon($coupon_code);
                        $persent += $coupon->amount;
                    }
                    $total_discount = WC()->cart->subtotal * ($persent / 100);
                    echo '<span class="discount">- ' . str_replace(',00', ',-', wc_price($total_discount)) . '<br></span>'; ?>

                    </span>
                <span class="total_val"><?php echo $total_val; ?></span>
                <br>
                <span class="count_members"><?= $cart_item['quantity']; ?></span>
            </div>
            <div class="field-price total bg-yellow white" data-target="total-price"
                 data-value="<?php echo str_replace(',00', ',-', number_format(WC()->cart->cart_contents_total, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator())); ?>">
                <strong><?php echo $total_val; ?></strong>
            </div>
        </td>
    </tr>


    <?php do_action('woocommerce_after_cart_contents'); ?>
    </tbody>
</table>