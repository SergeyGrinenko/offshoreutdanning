<?php $members_quantity = $args['members_quantity']; ?>

<div class="checkout_betaler py-3 hide">
    <?php session_start();

    $item = 1;
    if ($_SESSION["betalerID"]) {
        echo get_template_part('template-parts/participants-fields', null, ['item' => $_SESSION["betalerID"], 'editable' => false, 'billing' => true]);
    } else {
        echo get_template_part('template-parts/bussiness', null, ['editable' => false, 'billing' => true]);
    } ?>
</div>

