<?php $members_quantity = WC()->cart->cart_contents_count / sizeof(WC()->cart->get_cart()); ?>
<div class="deltager_payer hide">
    <h3 class="fw-bold"><?php _e('Betales av kursdeltaker'); ?></h3>
    <div class="deltager-tpl">
        <div class="my-3">
            <div class="select-course-locations w-100">
                <select class="p-1 w-100" id="select-deltager"></select>
            </div>
        </div>
    </div>
</div>