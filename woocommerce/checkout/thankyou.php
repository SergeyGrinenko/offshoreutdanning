<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.1.0
 *
 * @var WC_Order $order
 */

defined('ABSPATH') || exit;
?>

<div class="woocommerce-order">

    <?php
    if ($order) :
        $Totalt = 0;


        $products_data = $order->get_meta('order_items');
        $participants = $order->get_meta('participants');


        do_action('woocommerce_before_thankyou', $order->get_id()); ?>

        <?php if ($order->has_status('failed')) : ?>

        <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce'); ?></p>

        <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
            <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>"
               class="button pay"><?php esc_html_e('Pay', 'woocommerce'); ?></a>
            <?php if (is_user_logged_in()) : ?>
                <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"
                   class="button pay"><?php esc_html_e('My account', 'woocommerce'); ?></a>
            <?php endif; ?>
        </p>

    <?php else : ?>
        <h2 class="fw-bold woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received mb-0">
            <?php echo apply_filters('woocommerce_thankyou_order_received_text', esc_html__('Bra jobbet, kurset er bestilt!', 'woocommerce'), $order); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </h2>
        <p><?= esc_html__('Takk for at du valgte oss for ditt kurs, vi skal jobbe hardt for å sikre deg et godt resultat og en god opplevelse :)', 'woocommerce'); ?></p>
    <?php endif; ?>


        <h3 class="fw-bold"><?= esc_html__('Du har bestilt', 'woocommerce'); ?></h3>


        <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents mb-3 w-100" cellspacing="0">

            <tbody>
            <?php foreach ($products_data as $cart_item) {
                $custom_product_name = get_the_title($cart_item['product_id']); ?>
                <tr class="woocommerce-cart-form__cart-item p-1">
                    <td class="product-name bg-light-1 p-1 preview"
                        data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
                        <div class="fw-bold"><?php echo $custom_product_name; ?></div>
                        <?php if ($cart_item['few_seats_' . $cart_item['product_id']] === true) {
                            echo '<div class="few_seats bg-danger text-light text-center">' . __('Få plasser') . '</div>';
                        } ?>

                        <?php echo '<div class="course-info">' . $cart_item['cource_location_' . $cart_item['product_id']] . '&nbsp;' . __('fra') . '&nbsp;' . $cart_item['cource_date_' . $cart_item['product_id']] . '</div>'; ?>

                        <?php if ($cart_item['variation_name']): ?>
                            <div class="product_info_addons text-dark m-0">
                                <?php foreach ($cart_item['variation_name'] as $name => $price) {
                                    echo '<div class="addon">+ ' . $name . ' - ' . formatted_price($price) . '</div>';
                                } ?>
                            </div>
                        <?php endif; ?>
                    </td>

                    <td class="product-price bg-primary text-light text-center p-1 preview"
                        data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                        <?= formatted_price($cart_item['course_price_' . $cart_item['product_id']]); ?>
                    </td>

                </tr>
                <?php
            }

            foreach ($order->get_items() as $item_id => $item) {
                $quantity = $item->get_quantity();
            } ?>

            <tr class="summry-container">
                <td class="sum_name p-0">
                    <div class="field-plain summary text-right fw-bold text-pink p-2" data-target="summary-text">
                        <?php if (!empty(WC()->cart->get_discount_total())): ?>
                            <?php _e('Total rabatt'); ?><br>
                        <?php endif; ?>
                        <?php _e('Deltagere'); ?><br>
                    </div>
                    <div class="field-plain total text-right p-2"><strong><?php _e('Totalt'); ?></strong></div>
                </td>
                <td class="sum_count p-0">
                    <div class="field-price summary p-2 text-center" data-target="summary-price">
                        <?php if (!empty($order->get_discount_total())): ?>
                            <?= formatted_price($order->get_discount_total()) . '<br>'; ?>
                        <?php endif; ?>
                        <span class="count_members fw-bold text-pink"><?= $quantity; ?></span></div>
                    <div class="field-price total p-2 text-center" data-target="total-price">
                        <strong><?= formatted_price($cart_item['subtotal']); ?></strong>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="participants-wrap">
            <?php foreach ($participants as $participant) { ?>
                <div class="item">
                    <h4 class="mb-2"><?= esc_html__('Deltaker:', 'woocommerce'); ?>
                        <?php if (isset($participant->firstname) && isset($participant->lastname)): ?>
                        <strong><?= $participant->firstname . ' ' . $participant->lastname ?></strong></h4>
                    <?php endif; ?>
                    <ul class="participants-wrap__list">
                        <?php if (isset($participant->birthday)): ?>
                            <li>
                                <p class="m-0"><?= esc_html__('Fødselsdato:', 'woocommerce'); ?></p>
                                <p class="m-0"><?= $participant->birthday; ?></p>
                            </li>
                        <?php endif; ?>
                        <?php if (isset($participant->email)): ?>
                            <li>
                                <p class="m-0"><?= esc_html__('E-post:', 'woocommerce'); ?></p>
                                <p class="m-0"><?= $participant->email; ?></p>
                            </li>
                        <?php endif; ?>
                        <?php if (isset($participant->phone)): ?>
                            <li>
                                <p class="m-0"><?= esc_html__('Mobiltelefon:', 'woocommerce'); ?></p>
                                <p class="m-0"><?= $participant->phone; ?></p>
                            </li>
                        <?php endif; ?>
                        <?php if (isset($participant->postcode) && isset($participant->city) && isset($participant->location)): ?>
                            <li>
                                <p class="m-0"><?= esc_html__('Adresse:', 'woocommerce'); ?></p>
                                <p class="m-0"><?= $participant->street; ?></p>
                                <p class="m-0"><?= $participant->postcode . ' ' . $participant->city . ' ' . WC()->countries->countries[$participant->location]; ?></p>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php } ?>
        </div>

        <h3 class="fw-bold"><?= esc_html__('Betaler', 'woocommerce'); ?></h3>

        <div class="participants-wrap">

            <?php if (!empty($order->get_billing_company())) { ?>
                <div class="item">
                    <h4 class="mb-2"><?= esc_html__('Betaler:', 'woocommerce'); ?>
                        <strong><?= $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ?></strong>
                    </h4>
                    <ul class="participants-wrap__list">
                        <?php if (get_post_meta($order->get_id(), '_billing_organization_number', true)): ?>
                            <li>
                                <p class="m-0"><?= esc_html__('Organisasjonsummer:', 'woocommerce'); ?></p>
                                <p class="m-0"><?= get_post_meta($order->get_id(), '_billing_organization_number', true); ?></p>
                            </li>
                        <?php endif; ?>
                        <li>
                            <p class="m-0"><?= esc_html__('Organisasjonsnavn:', 'woocommerce'); ?></p>
                            <p class="m-0"><?= $order->get_billing_company(); ?></p>
                        </li>
                        <li>
                            <p class="m-0"><?= esc_html__('E-post:', 'woocommerce'); ?></p>
                            <p class="m-0"><?= $order->get_billing_email(); ?></p>
                        </li>
                        <li>
                            <p class="m-0"><?= esc_html__('Mobiltelefon:', 'woocommerce'); ?></p>
                            <p class="m-0"><?= $order->get_billing_phone(); ?></p>
                        </li>
                        <li>
                            <p class="m-0"><?= esc_html__('Adresse:', 'woocommerce'); ?></p>
                            <p class="m-0"><?= $order->get_billing_address_1(); ?></p>
                            <p class="m-0"><?= $order->get_billing_postcode() . ' ' . $order->get_billing_city() . ' ' . WC()->countries->countries[$order->get_billing_country()]; ?></p>
                        </li>
                        <?php if (get_post_meta($order->get_id(), '_company-ordrereferanse', true)): ?>
                            <li>
                                <p class="m-0"><?= esc_html__('Po-nummer/ordrereferanse:', 'woocommerce'); ?></p>
                                <p class="m-0"><?= get_post_meta($order->get_id(), '_company-ordrereferanse', true); ?></p>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php } else { ?>

                <?php $payer = array();
                foreach ($participants as $record) {
                    if ($record->firstname == $order->get_billing_first_name() && $record->lastname == $order->get_billing_last_name()) {
                        $payer = $record;
                        break;
                    }
                } ?>

                <div class="item">
                    <h4 class="mb-2"><?= esc_html__('Betaler:', 'woocommerce'); ?>
                        <strong><?= $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ?></strong>
                    </h4>
                    <ul class="participants-wrap__list">
                        <?php if (isset($payer->billing_birthday)): ?>
                            <li>
                                <p class="m-0"><?= esc_html__('Fødselsdato:', 'woocommerce'); ?></p>
                                <p class="m-0"><?= $payer->billing_birthday; ?></p>
                            </li>
                        <?php endif; ?>
                        <li>
                            <p class="m-0"><?= esc_html__('E-post:', 'woocommerce'); ?></p>
                            <p class="m-0"><?= $order->get_billing_email(); ?></p>
                        </li>
                        <li>
                            <p class="m-0"><?= esc_html__('Mobiltelefon:', 'woocommerce'); ?></p>
                            <p class="m-0"><?= $order->get_billing_phone(); ?></p>
                        </li>
                        <li>
                            <p class="m-0"><?= esc_html__('Adresse:', 'woocommerce'); ?></p>
                            <p class="m-0"><?= $order->get_billing_address_1(); ?></p>
                            <p class="m-0"><?= $order->get_billing_postcode() . ' ' . $order->get_billing_city() . ' ' . WC()->countries->countries[$order->get_billing_country()]; ?></p>
                        </li>
                    </ul>
                </div>
            <?php } ?>

        </div>

        <h2 class="fw-bold">Hva skjer nå?</h2>

        <p>Vi har sendt deg en bekreftelse på mottatt bestilling. En endelig bekreftelse på kursplassen kommer innen
            kort tid så fort vi får ferdigbehandlet.
            Oppdaget feil i bestillingen? Det går fint, ta bare kontakt med oss ved å bruke kontakt informasjonen
            nedenfor.</p>
        <br>
        <h3 class="fw-bold">Vanlige spørsmål etter bestilling</h3>

        <p>I bekreftelse eposten vi har sendt deg har du informasjon om</p>

        <ul>
            <li>Hvor kurset avholdes</li>
            <li>Når du skal møte opp</li>
            <li>Overnatting</li>
            <li>Transport</li>
        </ul>
        <br>
        <br>
        <p>Du kan nå trygt lukke dette vinduet, eller se etter noen andre kurs på
            <a href="<?= get_site_url() ?>" class="text-decoration text-dark">
                forsiden vår.
            </a>
        </p>

    <?php else : ?>

        <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters('woocommerce_thankyou_order_received_text', esc_html__('Thank you. Your order has been received.', 'woocommerce'), null); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

    <?php endif; ?>

</div>
