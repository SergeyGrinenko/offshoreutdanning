<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined('ABSPATH') || exit;

get_header();

if (woocommerce_product_loop()) {
    $active_term = null;

    if (get_query_var('term')) {
        $grid = true;
        $active_term = get_term_by('slug', get_query_var('term'), 'product_cat')->term_id;
        $loop = new WP_Query([
            'post_type' => 'product',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'ASC',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat', // the custom vocabulary
                    'field' => 'slug',
                    'terms' => get_query_var('term'),      // provide the term slugs
                ),
            )
        ]);
    } else {
        $grid = false;
        $loop = new WP_Query([
            'post_type' => 'product',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);
    }

    $args = array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
    );

    $categories = get_terms($args);

    $parent_categories = array();
    $child_categories = array();

    foreach ($categories as $category) {
        if ($category->parent == 0) {
            $parent_categories[] = $category;
        } else {
            $child_categories[] = $category;
        }
    }

    $sorted_categories = array_merge($parent_categories, $child_categories);


    echo '<div class="inner-page-header bg-softdark">';
    echo '<div class="container py-0">';
    echo '<div class="bg-dark-2">';
    echo '<h1 class="woocommerce-products-header__title page-title text-white text-center m-0">' . woocommerce_page_title(false) . '</h1>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo '<div class="container position-relative">';

    echo '<div class="category-filter">';

    if (!empty($sorted_categories)) {
        echo '<div class="categories-wrapper">';
        $i = 1;

        foreach ($sorted_categories  as $category) {

            if ($category->slug === 'courses') {
                $term_link = '/produktkategori/kurs/';
            } else {
                $term_link = get_term_link($category->term_id);
            }

            $status = ' ';
            if (!is_null($active_term)) {
                if ($category->term_id == $active_term) {
                    $status = 'active';
                } else {
                    $status = ' ';
                }
            } else {
                if ($i === 1) {
                    $status = 'main active';
                } else {
                    $status = ' ';
                }
            }

            echo '<a href="' . $term_link . '"><div id="' . $category->term_id . '" class="category bg-light-1 text-dark ' . $status . '">' . $category->name . '</div></a>';
            $i++;
        }
        echo '</div>';


    }

    echo '</div>';

    echo '<div class="d-flex justify-end search_bar">';

    echo '<div class="search-course-bar d-flex">';
    echo '<input type="text" class="search-course-bar__input w-100" placeholder="Søk i alle kurs">';
    echo '</div>';
    echo '</div>';

    echo '<ul class="archive-course-list ' . ($grid === true ? 'card-grid' : '') . ' hidden m-0 p-0">';


    if ($loop->have_posts()):
        while ($loop->have_posts()) : $loop->the_post();
            global $product;
            get_template_part('templates/courses/course-list-tpl', null, [
                'price' => get_price_range(get_the_ID()),
                'grid' => $grid
            ]);
        endwhile;
    endif;
    wp_reset_query();
    echo '</ul>';

    echo '<div class="not-found p-1 text-danger hide">' . __('Beklager, kurset ble ikke funnet.') . '</div>';

    echo '</div>';
    echo '</div>';

} ?>

<?php get_footer();
