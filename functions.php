<?php

add_action('admin_enqueue_scripts', 'load_admin_styles');
function load_admin_styles()
{
    wp_enqueue_style('admin-styles', get_stylesheet_directory_uri() . '/assets/scss/admin.min.css');
    wp_enqueue_script('admin-scripts', get_stylesheet_directory_uri() . '/assets/js/admin.js', '', array(), true);
}

function disable_wp_blocks()
{
    $wstyles = array(
//        'wp-block-library',
        'wc-blocks-style',
        'wc-blocks-style-active-filters',
        'wc-blocks-style-add-to-cart-form',
        'wc-blocks-packages-style',
        'wc-blocks-style-all-products',
        'wc-blocks-style-all-reviews',
        'wc-blocks-style-attribute-filter',
        'wc-blocks-style-breadcrumbs',
        'wc-blocks-style-catalog-sorting',
        'wc-blocks-style-customer-account',
        'wc-blocks-style-featured-category',
        'wc-blocks-style-featured-product',
        'wc-blocks-style-mini-cart',
        'wc-blocks-style-price-filter',
        'wc-blocks-style-product-add-to-cart',
        'wc-blocks-style-product-button',
        'wc-blocks-style-product-categories',
        'wc-blocks-style-product-image',
        'wc-blocks-style-product-image-gallery',
        'wc-blocks-style-product-query',
        'wc-blocks-style-product-results-count',
        'wc-blocks-style-product-reviews',
        'wc-blocks-style-product-sale-badge',
        'wc-blocks-style-product-search',
        'wc-blocks-style-product-sku',
        'wc-blocks-style-product-stock-indicator',
        'wc-blocks-style-product-summary',
        'wc-blocks-style-product-title',
        'wc-blocks-style-rating-filter',
        'wc-blocks-style-reviews-by-category',
        'wc-blocks-style-reviews-by-product',
        'wc-blocks-style-product-details',
        'wc-blocks-style-single-product',
        'wc-blocks-style-stock-filter',
        'wc-blocks-style-cart',
        'wc-blocks-style-checkout',
        'wc-blocks-style-mini-cart-contents',
        'classic-theme-styles-inline'
    );

    foreach ($wstyles as $wstyle) {
        wp_deregister_style($wstyle);
    }

    $wscripts = array(
        'wc-blocks-middleware',
        'wc-blocks-data-store'
    );

    foreach ($wscripts as $wscript) {
        wp_deregister_script($wscript);
    }
}

//add_action( 'init', 'disable_wp_blocks', 100 );

add_action('wp_enqueue_scripts', 'theme_styles');
function theme_styles()
{

    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('photoswipe');
    wp_dequeue_style('photoswipe-default-skin');
    wp_deregister_style('photoswipe-default-skin');
    wp_dequeue_script('zoom');
    wp_dequeue_script('flexslider');
    wp_deregister_script('photoswipe');
    wp_deregister_script('photoswipe-ui-default');
    wp_deregister_script('wp-embed');
//    wp_deregister_script('wp-polyfill');
    remove_theme_support('wc-product-gallery-lightbox');
    remove_theme_support('wc-product-gallery-zoom');

    wp_register_style('base', get_stylesheet_directory_uri() . '/assets/scss/base.min.css');
    wp_register_style('offshore-page', get_stylesheet_directory_uri() . '/assets/scss/pages/offshore-page.min.css');
    wp_register_style('single-course', get_stylesheet_directory_uri() . '/assets/scss/pages/single-course.min.css');
    wp_register_style('archive', get_stylesheet_directory_uri() . '/assets/scss/pages/archive-courses.min.css');
    wp_register_style('posts', get_stylesheet_directory_uri() . '/assets/scss/posts.min.css');
    wp_register_style('checkout-style', get_stylesheet_directory_uri() . '/assets/scss/pages/checkout.min.css');
    wp_register_style('blog-style', get_stylesheet_directory_uri() . '/assets/scss/blog.min.css');
    wp_register_style('jquery-ui-style', get_stylesheet_directory_uri() . '/assets/css/jquery-ui.min.css');
    wp_register_style('contact', get_stylesheet_directory_uri() . '/assets/scss/contact.min.css');
    wp_register_style('about_us', get_stylesheet_directory_uri() . '/assets/scss/pages/about_us.min.css');
    wp_register_style('landing_page', get_stylesheet_directory_uri() . '/assets/scss/pages/landing.min.css');
    wp_register_style('guide', get_stylesheet_directory_uri() . '/assets/scss/guide.min.css');

    wp_register_script('menu', get_stylesheet_directory_uri() . '/assets/js/_menu.min.js', '', array(), true);
    wp_register_script('course', get_stylesheet_directory_uri() . '/assets/js/_course.min.js', '', array(), true);
    wp_register_script('accordion-offut', get_stylesheet_directory_uri() . '/assets/js/_accordion.min.js', '', array(), true);
    wp_register_script('video-lightbox', get_stylesheet_directory_uri() . '/assets/js/_video-lightbox.min.js', '', array(), true);
    wp_register_script('checkout', get_stylesheet_directory_uri() . '/assets/js/_checkout.js', '', array(), true);

    wp_register_script('course-script', get_stylesheet_directory_uri() . '/assets/js/courses.js', '', array(), true);
//    wp_register_script('steps-validate', get_stylesheet_directory_uri() . '/assets/js/jquery.validate.js', '', array(), true);
//    wp_register_script('steps-script', get_stylesheet_directory_uri() . '/assets/js/jquery.steps.js', '', array(), true);
//    wp_register_script('checkout-script', get_stylesheet_directory_uri() . '/assets/js/checkout.js', '', array(), true);
//    wp_register_script('cookie-script', get_stylesheet_directory_uri() . '/assets/js/jquery.cookie.js', '', array(), true);
    wp_register_script('blog-script', get_stylesheet_directory_uri() . '/assets/js/blog.js', '', array(), true);
//    wp_register_script('jquery-ui', get_stylesheet_directory_uri() . '/assets/js/jquery-ui.min.js', '', array(), true);
//    wp_register_script('jquery-ui-no', get_stylesheet_directory_uri() . '/assets/js/datepicker-no.min.js', '', array(), true);

    wp_enqueue_style('base');
//    wp_enqueue_style('jquery-ui-style');


    wp_enqueue_script('menu');
    wp_enqueue_script('accordion-offut');


    if ( is_product()) {
        wp_enqueue_script('course');
        wp_enqueue_script('video-lightbox');
    }

//    wp_enqueue_script('course-script');
//    wp_enqueue_script('jquery-ui');
//    wp_enqueue_script('jquery-ui-no');

    if (is_page_template('templates/offshore-page-tpl.php'))
        wp_enqueue_style('offshore-page');

    if (is_page_template('templates/contact_us-tpl.php'))
        wp_enqueue_style('contact');

    if (is_singular('landing'))
        wp_enqueue_style('landing_page');


    if (is_page_template('templates/about_us-tpl.php'))
        wp_enqueue_style('about_us');

    if (is_product())
        wp_enqueue_style('single-course');

    if (is_archive() || is_single()) {
        wp_enqueue_style('archive');
        wp_enqueue_style('posts');
        wp_enqueue_style('guide');
    }


    if (is_page_template('professions-guide-tpl.php') || is_page_template('templates/help-tpl.php') || is_post_type_archive('hjelp') || is_singular('hjelp') || is_singular('post')) {
        wp_enqueue_style('blog-style');
        wp_enqueue_script('blog-script');
    }

    if (is_checkout()) {

        wp_enqueue_style('checkout-style');
//        wp_enqueue_script('steps-validate');
//        wp_enqueue_script('cookie-script');
//        wp_enqueue_script('steps-script');
        wp_enqueue_script('checkout');
    }

}

add_theme_support('woocommerce');
add_image_size('course-thumbnail', 425, 312, true);
add_image_size('post-thumbnail', 350, 225, true);

add_filter('xmlrpc_enabled', '__return_false');

function turn_off_feed()
{
    wp_die(__('Our Feed is currently off'));
}

add_action('do_feed', 'turn_off_feed', 1);
add_action('do_feed_rdf', 'turn_off_feed', 1);
add_action('do_feed_rss', 'turn_off_feed', 1);
add_action('do_feed_rss2', 'turn_off_feed', 1);
add_action('do_feed_atom', 'turn_off_feed', 1);
add_action('do_feed_rss2_comments', 'turn_off_feed', 1);
add_action('do_feed_atom_comments', 'turn_off_feed', 1);
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
// REMOVE WP EMOJI
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');

add_filter('woocommerce_placeholder_img_src', 'custom_woocommerce_placeholder_img_src');


//remove_filter( 'woocommerce_email_recipient_cancelled_order', 'blz_add_recipient' );
function custom_woocommerce_placeholder_img_src($src)
{
    $upload_dir = wp_upload_dir();
    $uploads = untrailingslashit($upload_dir['baseurl']);
    // replace with path to your image
    $src = $uploads . '/2022/05/offshore-placeholder-1024x1024-1-425x312.png';
    return $src;
}

function generate_course_schema($schema)
{
    global $schema;
    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE) . '</script>';
}

add_action('wp_footer', 'generate_course_schema', 100);

require_once 'classes/Offutd_Menu_Walker.php';
require_once 'classes/Lanekasse_Payment_Gateway.php';
//require_once 'inc/blog-actions.php';
require_once 'inc/admin-settings.php';
require_once 'inc/course-actions.php';
require_once 'inc/meta-boxes.php';
//require_once 'inc/acf-blocks.php';
//require_once 'classes/Course_Date_URL.php';

add_action('wp_loaded', 'load_custom_class_after_woocommerce');

function load_custom_class_after_woocommerce()
{
    // Load your custom class file

//    require_once 'classes/Course_Date_URL.php';

}

//add_action( 'plugins_loaded', array( 'Course_Date_URL', 'init' ));
add_filter('woocommerce_rest_check_permissions', 'my_woocommerce_rest_check_permissions', 90, 4);

function my_woocommerce_rest_check_permissions($permission, $context, $object_id, $post_type)
{
    return true;
}

function cancel_unpaid_orders()
{
    $unpaid_orders = wc_get_orders(array(
        'status' => 'pending',
        'limit' => -1,
        'orderby' => 'date',
        'order' => 'ASC',
    ));

    foreach ($unpaid_orders as $order) {
        $order_date = strtotime($order->get_date_created());
        date_default_timezone_set('Europe/Oslo');
        $current_time = time();
        $oslo_time_zone = new DateTimeZone('Europe/Oslo');
        $creation_date_oslo = new DateTime();
        $current_time_oslo = new DateTime();
        $creation_date_oslo->setTimestamp($order_date);
        $creation_date_oslo->setTimezone($oslo_time_zone);
        $current_time_oslo->setTimezone($oslo_time_zone);
        $orderDate = $creation_date_oslo->getTimestamp();
        $NewOrderDate = strtotime('+30 minutes', $orderDate);

        if ($current_time > $NewOrderDate) {
            $order->update_status('on-hold', __('The order on-hold.', 'woocommerce'));
            $order->update_status('cancelled', __('The order was canceled automatically due to lack of payment.', 'woocommerce'));
        }
    }
}

add_action('init', 'cancel_unpaid_orders');

function wpza__add_apple_touch_icons()
{ ?>
    <link rel="apple-touch-icon"
          href="<?php echo get_template_directory_uri(); ?>/assets/images/favicons/touch-icon.png"/>
    <link rel="apple-touch-icon" sizes="57x57"
          href="<?php echo get_template_directory_uri(); ?>/assets/images/favicons/touch-icon-57x57.png"/>
    <link rel="apple-touch-icon" sizes="72x72"
          href="<?php echo get_template_directory_uri(); ?>/assets/images/favicons/touch-icon-72x72.png"/>
    <link rel="apple-touch-icon" sizes="76x76"
          href="<?php echo get_template_directory_uri(); ?>/assets/images/favicons/touch-icon-76x76.png"/>
    <link rel="apple-touch-icon" sizes="114x114"
          href="<?php echo get_template_directory_uri(); ?>/assets/images/favicons/touch-icon-114x114.png"/>
    <link rel="apple-touch-icon" sizes="120x120"
          href="<?php echo get_template_directory_uri(); ?>/assets/images/favicons/touch-icon-120x120.png"/>
    <link rel="apple-touch-icon" sizes="144x144"
          href="<?php echo get_template_directory_uri(); ?>/assets/images/favicons/touch-icon-144x144.png"/>
    <link rel="apple-touch-icon" sizes="152x152"
          href="<?php echo get_template_directory_uri(); ?>/assets/images/favicons/touch-icon-152x152.png"/>
    <?php
}

add_action('wp_head', 'wpza__add_apple_touch_icons');
