<?php if ($args['price']):
    $product_cats_ids = wc_get_product_term_ids(get_the_ID(), 'product_cat');
//    $term_name = get_term($product_cats_ids[1])->name;

    unset($product_cats_ids[0]);
    $min = min($args['price']);
    $max = max($args['price']);
    $course_price = formatted_price(trim(str_replace(get_woocommerce_currency_symbol(), '', $min)));
    $price = $min;
    if ($min !== $max) {
        $price = __('fra') . ' ' . $min;
        $course_price = __('fra') . '&nbsp;' . formatted_price(trim(str_replace(get_woocommerce_currency_symbol(), '', $min)));
    }

    if (intval(trim(str_replace(get_woocommerce_currency_symbol(), '', $min))) > 0):?>

        <li class="mb-3 archive-course-list__item"
            id="<?= implode(",", $product_cats_ids); ?>"
            data-letter="<?= get_the_title()[0]; ?>"
            data-search-term="<?= strtolower(get_the_title()); ?> <?= $price; ?>">

            <a href="<?= get_the_permalink(); ?>" class="d-flex align-unset text-dark">

                <?php if (has_post_thumbnail()):
                    $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'course-thumbnail');
                else:
                    $thumbnail = wc_placeholder_img_src('course-thumbnail');
                endif; ?>

                <img src="<?= $thumbnail; ?>" alt="course-thumbnail" class="hide course-thumbnail" width="100%">

                <div class="item__title w-100 d-flex justify-between align-unset">
                    <div class="bg-light-1 px-3 py-1 w-100">
                        <div class="category-name hide"></div>
                        <?= get_the_title(); ?>
                    </div>
                    <div class="item_price bg-primary text-light px-3 d-flex justify-center"><?= $course_price; ?></div>
                </div>
                <div class="item__arrow bg-dark d-flex justify-center">
                    <img src="<?= get_stylesheet_directory_uri(); ?>/assets/images/icon-right.svg" alt="icon-right">
                </div>
            </a>
        </li>

    <?php endif; ?>
<?php endif; ?>