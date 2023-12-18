<div class="mini-cart-body">
    <ul class="mini-cart-items">
        <?php global $kurs_qty; ?>
        <?php foreach (array_reverse(WC()->cart->get_cart()) as $cart_item) { ?>
            <li class="mini-cart-item">
                <strong>
                    <a href="<?php the_permalink($cart_item['product_id']); ?>" class="text-dark">
                        <?= get_the_title($cart_item['product_id']) ?>
                    </a>
                </strong>
                <p class="m-0">
                    <?= $cart_item['data_from']; ?>
                    <?= __('til'); ?>
                    <?= strtolower($cart_item['data_to']); ?>,
                    <?= $cart_item['location']; ?>
                </p>
                <?php if ($cart_item['practices']): ?>
                    <div class="product_info_addons text-dark m-0">
                        <?php foreach ($cart_item['practices'] as $name => $price) {
                            echo '<div class="addon">+ ' . $name . ' - ' . formatted_price($price) . '</div>';
                        } ?>
                    </div>
                <?php endif; ?>
            </li>
            <?php $kurs_qty++ ?>
        <?php } ?>
    </ul>
</div>

<div class="mini-cart-footer">

    <div class="d-flex align-center justify-between">

        <div class="mini-cart_checkout btn bg-light-1 rounded7 text-dark d-block justify-center close-cart">
                <?php _e('Fortsett å se'); ?>
        </div>

        <a href="<?= wc_get_checkout_url(); ?>"
           class="mini-cart_checkout btn bg-pink rounded7 text-light d-block justify-center">
            <div class="d-flex">
                <img src="<?= get_stylesheet_directory_uri(); ?>/assets/images/icon-cart.svg" alt="icon-cart" width="20"
                     class="mr-1">
                <?php _e(' Gå til handlekurv'); ?>
            </div>

        </a>
    </div>
</div>
