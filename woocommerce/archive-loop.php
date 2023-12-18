<?php
global $product;

$product_title = get_the_title();
$custom_title = get_field('custom_course_title');
$product_ID = $product->get_id();
$custom_title = get_field('custom_course_title', $product_ID);
$custom_products = get_field('custom_products', $product_ID);
$course_status = get_field('course_status', $product_ID);
$price_array = array();
$status = get_post_meta($product_ID, '_wporg_meta_key', true);


if ($custom_products):

    if ($status == 'enabled'):

        foreach ($custom_products as $custom_product):
            $product_price = $custom_product['course_price'];
            array_push($price_array, $product_price);
        endforeach;

        $min = min($price_array);
        $max = max($price_array);

        if (isset($custom_products)):

            $min = min($price_array);
            $max = max($price_array);

            if ($min !== '0.00' && $min):
                echo '<div class="archive_product_item" data-name="' . $product_title . '" data-letter="' . (!empty($custom_title) ? $custom_title[0] : $product_title[0]) . '">';
                echo '<a href="' . get_the_permalink($product_ID) . '">';
                echo '<p>' . (!empty($custom_title) ? $custom_title : $product_title) . '</p>';

                if ($min === $max):
                    echo '<span class="itemprice">' . str_replace(',00', ',-', wc_price($min)) . '</span><span class="arrow"></span>';
                else:
                    echo '<span class="itemprice">fra&nbsp;' . str_replace(',00', ',-', wc_price($min)) . '</span><span class="arrow"></span>';
                endif;
                echo '</a>';
                echo '</div>';


            endif;
        endif;


    endif;
endif; ?>

