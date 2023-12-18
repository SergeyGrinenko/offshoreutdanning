<div class="mini-cart-nav d-flex justify-between text-light p-3">
    <div class="toggleCart">
        <a href="#" class="btn close-mini-cart bg-light text-dark rounded7">
            <span class="button-icon icon bg-dark close-icon"></span>
            <span class="px-3"><?php _e('Lukk'); ?></span>
        </a>
    </div>

    <a href="<?= wc_get_checkout_url(); ?>" class="btn bg-light text-dark rounded7 process-order">
        <span class="button-icon icon bg-primary arrow-left"></span>
        <span class="px-3"><?php _e('Påmelding'); ?></span>
    </a>

</div>

<div class="mini-cart-body">
    <?php $total = 0; ?>
    <?php $participants_qty = 1; ?>
    <?php foreach (array_reverse(WC()->cart->get_cart()) as $cart_item) { ?>
        <div class="mini-cart-item grid-7-3 grid-gap-0 mb-2"
             data-id="<?= $cart_item['product_id']; ?>"
             data-course_id="<?= $cart_item['course_id']; ?>">
            <div class="d-flex align-unset">
                <div class="mini-cart-item__status bg-success d-flex remove-course">
                    <img src="<?= get_stylesheet_directory_uri() ?>/assets/images/icon-check.svg"
                         alt="check">
                </div>
                <div class="mini-cart-item__info bg-info p-2 d-grid">
                    <?php if ($cart_item['few_seats'] === true): ?>
                        <div class="few_seats bg-danger text-light text-center"><?= __('Få plasser'); ?></div>
                    <?php endif; ?>
                    <div class="item_title"><?= get_the_title($cart_item['product_id']) ?></div>
                    <div class="item_date">
                        <?= $cart_item['data_from']; ?>
                        <?= __('til'); ?>
                        <?= $cart_item['data_to']; ?>,
                        <?= $cart_item['location']; ?>
                    </div>
                    <?php if (array_key_exists('practices', $cart_item)) : ?>
                        <div class="item_practices">
                            <?php foreach ($cart_item['practices'] as $label => $price) : ?>
                                <div>+ <?= $label ?>
                                    - <?= formatted_price($price); ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mini-cart-item__summary d-grid">
                <div class="item-time bg-primary text-light p-2 text-center d-flex justify-center"><?= getDayphase($cart_item['data_time']); ?></div>
                <div class="item-price bg-warning text-light p-2 text-center d-flex justify-center"><?= formatted_price($cart_item['course_price']); ?></div>
            </div>

        </div>
        <?php $total += $cart_item['course_price']; ?>
        <?php $participants_qty = $cart_item['quantity']; ?>

    <?php } ?>
</div>

<div class="mini-cart-footer">
    <div class="mini-cart__summary grid-7-3 bg-info mr-3">
        <div class="text-right">
            <div class="p-3">
                <div><?php _e('Sum kurs'); ?></div>
                <div><?php _e('Deltagere'); ?></div>
            </div>
            <div class="p-3"><?php _e('Totalt'); ?></div>
        </div>
        <div class="text-center">
            <div class="px-2 py-3 bg-gray">
                <div class="text-light"><?= formatted_price($total); ?></div>
                <div class="text-light"><?= $participants_qty ?></div>
            </div>
            <div class="bg-warning text-light px-2 py-3"><?= formatted_price($total); ?></div>
        </div>
    </div>

    <a href="<?= wc_get_checkout_url(); ?>" class="mini-cart_checkout bg-success text-light p-3 d-block">
        <div class="p-3 d-flex"><?php _e('Gå til påmeldingsskjema'); ?><img src="<?= get_stylesheet_directory_uri(); ?>/assets/images/icon-right.svg" alt="icon-right"></div>

    </a>
</div>
