<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */
global $start;
$members_quantity = WC()->cart->cart_contents_count / sizeof(WC()->cart->get_cart());
?>

<form name="checkout" id="contact" method="post" class="checkout woocommerce-checkout"
      action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
    <div>
        <div class="subheader">
            <h2 class="fw-bold"><?php _e('Bestill kurs'); ?></h2>
            <p><?php _e('Kursbestillingen består av 3 deler og tar vanligvis rundt 3 minutter.'); ?></p>
            <ul>
                <li><?php _e('Kurs og deltakerinformasjon'); ?></li>
                <li><?php _e('Bestill og betal'); ?></li>
                <li><?php _e('Kvittering på fullført bestilling'); ?></li>
            </ul>
        </div>
        <section>
            <h3 class="fw-bold"><?php _e('Valgte kurs og deltakere'); ?></h3>

            <?= get_template_part('template-parts/cart-items'); ?>

            <div class="d-flex justify-between align-unset row-reverse coupon-wrapper">
                <div>
                    <div class="w-100 text-right">
                        <p class="m-0 text-dark rounded7 bg-light toggle-coupon">
                            <span class="button-icon icon bg-primary arrow-left"></span>
                            <span class="button-text"> <?= __('Legg til kupong'); ?></span>
                        </p>
                    </div>
                </div>
                <div class="redeem-coupon">
                    <p class="mt-0 mb-3">
                        <?php _e('Hvis du har en kupongkode, vennligst bruk den nedenfor.'); ?></p>
                    <div class="d-flex align-unset">
                        <input type="text"
                               class="m-0 p-1"
                               name="coupon"
                               placeholder="<?php _e('Legg inn kupong'); ?>"
                               id="coupon">
                        <div class="apply-coupon p-1 text-light bg-softdark" id="apply-coupon">
                            <?php _e('Bruk kupong'); ?></div>
                    </div>
                    <p class="coupon-message m-0"></p>
                </div>
            </div>

            <div class="members_wrap py-3">
                <ul class="member" id="ToggleCard">
<!--                    --><?php //for ($i = 1; $i <= $members_quantity; $i++):
                        echo get_template_part('template-parts/participants-fields');
//                    endfor; ?>
                </ul>

                <div class="add_member text-center text-decoration">
                    <?php _e('Legg til ekstra deltaker'); ?>
                </div>
            </div>

            <div class="betaling">
                <h3 class="mb-0 fw-bold"><?php _e('Betaling'); ?></h3>
                <p class="mt-2"><?php _e('Hvem skal betale kurset?'); ?></p>
                <div class="checkout-choice betaler_type d-flex align-center">
                    <div>
                        <div class="checkout-choice__item d-flex align-center">
                            <label><?php _e('Kursdeltager'); ?>
                                <input type="radio" name="betaler_type" id="deltager"
                                       value="deltager"
                                       data-description="Kursdeltager skal betale. Er det flere deltagere velger du betaler i neste steg."
                                       checked>
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="checkout-choice__item d-flex align-center mb-2">
                            <label><?php _e('Bedrift'); ?>
                                <input type="radio" name="betaler_type" id="bussiness"
                                       value="bussiness"
                                       data-description="En organisasjon eller bedrift skal stå som betaler. Kortbetaling og faktura kan benyttes.">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="checkout-choice_info ">
                    <?= get_template_part('template-parts/deltager'); ?>
                    <?= get_template_part('template-parts/bussiness'); ?>
                </div>
            </div>


        </section>

        <section>
            <div id="step-2">
                <?php $installed_payment_methods = WC()->payment_gateways->get_available_payment_gateways(); ?>
                <div class="checkout-choice paymentMethod d-flex align-center">
                    <div>
                        <?= get_template_part('template-parts/payment-method', null, ['start' => $start, 'installed_payment_methods' => $installed_payment_methods]); ?>
                    </div>
                    <div class="checkout-choice_info"></div>
                </div>
            </div>
        </section>

        <section class="last-step">


            <input type="hidden" id="checkout_data" name="checkout_data">

            <?php wp_nonce_field('woocommerce-process_checkout'); ?>
            <?php echo apply_filters('woocommerce_order_button_html', '<button type="submit" class="my-3 button alt rounded7" name="woocommerce_checkout_place_order" disabled id="place_order" value="' . __("Fullfør bestilling", "woocommerce") . '" data-value="' . __("Fullfør bestilling", "woocommerce") . '">' . __("Fullfør bestilling", "woocommerce") . '</button>'); ?>

            <?php do_action('woocommerce_review_order_after_submit'); ?>

            <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
        </section>
    </div>

</form>