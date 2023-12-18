<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 7.9.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_mini_cart');

?>

<?php if (!WC()->cart->is_empty()) : ?>
    <?php global $woocommerce; ?>

    <div style="height: inherit;position: relative;">

        <ul class="woocommerce-mini-cart <?php echo esc_attr($args['list_class']); ?>">
            <?php
            do_action('woocommerce_before_mini_cart_contents');

            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                $custom_product_name = get_field('custom_course_title', $cart_item['product_id']);
                $row_id = $cart_item['row_id'];

                if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)) {
                    $product_name = apply_filters('woocommerce_cart_item_name', $custom_product_name, $cart_item, $cart_item_key);
                    $product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);

                    $data_from = $cart_item['data_from'];
                    $data_to = $cart_item['data_to'];
                    $data_mounth = $cart_item['data_mounth'];
                    $location = $cart_item['location'];
                    $custom_price = str_replace(".00", "", $cart_item['custom_price']);
                    $get_time = $cart_item['data_time'];
                    if ($get_time === 'day') {
                        $get_time = 'Dagtid';
                    } else if ($get_time === 'evening') {
                        $get_time = 'Kveld';
                    }
                    $few_seats = $cart_item['few_seats'];
                    $kind = $cart_item['kind'];
                    $grouped_data = $cart_item['grouped_data'];
                    $variation_name = $cart_item['variation_name'];
                    $variation_term_id = $cart_item['variation_term_id'];
                    $grouped_data = $cart_item['grouped_data']; ?>

                    <li class="woocommerce-mini-cart-item gc-item-wrapper <?php echo esc_attr(apply_filters('woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key)); ?>"
                        data-kind="<?php echo $kind; ?>" data-grouped_data="<?php echo $grouped_data; ?>">

                        <div class="gc-item-subwrapper text-left">

                            <a href="<?php the_permalink($product_id); ?>" class="gc-item-coursename">
                                <?php if ($few_seats == 1): ?>
                                    <div class="gc-item-notice-few-seats">Få Plasser</div>
                                <?php endif; ?>
                                <?php echo $product_name; ?>

                                <?php if ($grouped_data): ?>
                                    <span></span>
                                <?php endif; ?>

                            </a>
                            <div class="gc-item-coursedate">

                                <?php echo $data_from; ?> til <?php echo $data_to; ?>

                                <?php if ($variation_name): ?>
                                    <p class="product_info_addons white m-0">+</br>Praksis:
                                        <?php foreach ($variation_name as $item) {
                                            echo '<span>' . $item['name'] . ' - ' . get_woocommerce_currency_symbol() . $item['price'] . '; </span>';
                                        } ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="gc-item-subsubwrapper">
                                <span class="gc-item-place"> <?php echo $location; ?></span>
                                <span class="gc-item-timeofday"><?php echo $get_time; ?></span>
                                <span class="gc-item-price"><?php echo str_replace(',00', ',-', wc_price($custom_price)); ?></span>
                            </div>
                        </div>

                        <?php
                        echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            'woocommerce_cart_item_remove_link',
                            sprintf(
                                '<a href="%s" class="gc-item-removebutton remove_from_cart_button" data-var-term="' . $variation_term_id . '" data-var-id="' . ($cart_item['variation_ids'] ? implode(',', $cart_item['variation_ids']) : '') . '" aria-label="%s" data-product_id="%s" data-row_id="' . $row_id . '" data-cart_item_key="%s" data-product_sku="%s"></a>',
                                esc_url(wc_get_cart_remove_url($cart_item_key)),
                                esc_attr__('Remove this item', 'woocommerce'),
                                esc_attr($product_id),
                                esc_attr($cart_item_key),
                                esc_attr($_product->get_sku())
                            ),
                            $cart_item_key
                        );
                        ?>

                        <div class="w-100 text-center hold_on hidden"
                             style="position: absolute;height: 100%;background: #0000009c; top: 0;"
                             id="<?php echo $product_id; ?>">
                        </div>

                    </li>
                    <?php
                }
            }

            do_action('woocommerce_mini_cart_contents');
            ?>
        </ul>
        <div class="w-100 text-center hold_on hidden"
             style="position: absolute;height: 100%;background: #0000009c; top: 0;"></div>
    </div>
    <?php
    $total = $woocommerce->cart->get_cart_total();
    $subtotal = $woocommerce->cart->get_subtotal();
    ?>

    <div class="gc-gotocheckout-wrapper">
        <div class="gc-gotocheckout-totalsubwrapper"><span class="gc-gotocheckout-totaltext">Totalt:</span><span
                    class="gc-gotocheckout-totalprice"><?php echo str_replace(',00', ',-', $total); ?></span></div>
        <a href="<?php echo wc_get_checkout_url(); ?>" class="gc-gotocheckout-button">Gå til påmeldingsskjema</a>
    </div>

<?php else : ?>
    <div class="p-2 bg-red white not_available">
        <?php esc_html_e('No products in the cart.', 'woocommerce'); ?>
    </div>

<?php endif; ?>

<?php do_action('woocommerce_after_mini_cart'); ?>
