<?php
add_filter('manage_product_posts_columns', 'set_custom_price_column');
function set_custom_price_column($columns)
{
    $columns['course_price'] = __('Pris', 'your_text_domain');
    return $columns;
}

// Set price on archive products (from-to)
add_action('manage_product_posts_custom_column', 'custom_price_column', 10, 2);
function custom_price_column($column, $post_id)
{
    $product_ID = $post_id;
    $custom_products = get_post_meta($product_ID, 'product_dates', true);
    $price_array = get_price_range($product_ID, false);

    switch ($column) {
        case 'course_price' :
            if (isset($custom_products) && $price_array):
                $min = min($price_array);
                $max = max($price_array);

                if ($min !== false && $min !== '0.00'):
                    if ($min === $max) :
                        echo '<p style="font-weight: 600;">' . formatted_price($min) . '</p>';
                    else:
                        echo '<p style="font-weight: 600;">' . __('fra') . '&nbsp;' . formatted_price($min) . '</p>';
                    endif;

                else:
                    echo '<p>—</p>';
                endif;
            endif;
            break;
    }

}

function register_offshoreutdanning_menus()
{
    register_nav_menus(
        array(
            'header-menu' => __('Header Menu'),
            'course-menu' => __('Kurses Menu'),
            'help-menu' => __('Hjelp Menu'),
            'dropdown-menu' => __('Dropdown Menu'),
            'footer-menu' => __('Footer Menu')
        )
    );
}

add_action('init', 'register_offshoreutdanning_menus');

function add_additional_class_on_li($classes, $item, $args)
{
    if (isset($args->add_li_class)) {
        $classes[] = $args->add_li_class;
    }
    return $classes;
}

add_filter('nav_menu_css_class', 'add_additional_class_on_li', 1, 3);


function my_reigister_additonal_customizer_settings($wp_customize)
{
    $wp_customize->add_setting(
        'company_name',
        array(
            'default' => '',
            'type' => 'option',
            'capability' => 'edit_theme_options'
        ),
    );

    $wp_customize->add_setting(
        'company_phone',
        array(
            'default' => '',
            'type' => 'option',
            'capability' => 'edit_theme_options'
        ),
    );

    $wp_customize->add_setting(
        'company_address',
        array(
            'default' => '',
            'type' => 'option',
            'capability' => 'edit_theme_options'
        ),
    );

    $wp_customize->add_setting(
        'opening_hours',
        array(
            'default' => '',
            'type' => 'option',
            'capability' => 'edit_theme_options'
        ),
    );

    $wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        'company_name',
        array(
            'label' => __('Selskapsnavn', 'textdomain'),
            'settings' => 'company_name',
            'priority' => 10,
            'section' => 'title_tagline',
            'type' => 'text',
        )
    ));

    $wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        'company_phone',
        array(
            'label' => __('Firmatelefon', 'textdomain'),
            'settings' => 'company_phone',
            'priority' => 10,
            'section' => 'title_tagline',
            'type' => 'text',
        )
    ));

    $wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        'company_address',
        array(
            'label' => __('Firma adresse', 'textdomain'),
            'settings' => 'company_address',
            'priority' => 10,
            'section' => 'title_tagline',
            'type' => 'text',
        )
    ));

    $wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        'opening_hours',
        array(
            'label' => __('Apningstider', 'textdomain'),
            'settings' => 'opening_hours',
            'priority' => 10,
            'section' => 'title_tagline',
            'type' => 'text',
        )
    ));
}

//add_action('customize_register', 'my_register_additional_customizer_settings');

remove_action('woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 1);


function sp_sitemaps_external_link($custom_sitemap)
{
    $file = dirname(__DIR__) . '/api/temp/sitemap.json';
    $strJsonFileContents = file_get_contents($file);
    $site_map_data = array_unique(json_decode($strJsonFileContents));

    foreach ($site_map_data as $item) {
        if (!str_contains($item, '2100')) {
            $custom_sitemap[] = [
                'sitemap_url' => rawurldecode($item),
            ];
        }
    }

    return $custom_sitemap;
}

add_filter('seopress_sitemaps_external_link', 'sp_sitemaps_external_link');


add_filter('woocommerce_cod_process_payment_order_status', 'change_cod_payment_order_status', 10, 2);
function change_cod_payment_order_status($order_status, $order)
{
    return 'wc-processing';
}

add_action('woocommerce_thankyou', 'order_payment_completed', 10, 1);
function order_payment_completed($order_id)
{
    if (!$order_id) {
        return;
    }

    $order = wc_get_order($order_id);
    $paymethod = $order->get_payment_method();

    if ($paymethod !== 'cod') {
        if ($order->is_paid()) {
            $order->update_status('completed');
        }
    }
}

add_filter('woocommerce_valid_webhook_events', function ($events) {
    $events[] = 'completed';
    return $events;
});

add_filter('woocommerce_webhook_topics', function ($topics) {
    $topics['order.completed'] = __('Order completed', 'woocommerce');
    return $topics;
});

add_filter('woocommerce_webhook_topic_hooks', function ($topic_hooks) {
    $hooks = array('woocommerce_order_status_completed', 'woocommerce_order_status_processing');
    $statuses = array_filter(
        array_keys(wc_get_order_statuses()),
        function ($status) {
            return 'wc-completed' !== $status;
        }
    );

    foreach ($statuses as $status) {
        $hooks[] = 'woocommerce_order_status_completed_to_' . substr($status, 3);
        $hooks[] = 'woocommerce_order_status_processing_to_' . substr($status, 3);
    }

    $topic_hooks['order.completed'] = $hooks;
    $topic_hooks['order.updated'] = $hooks;

    return $topic_hooks;
});

add_action('init', 'change_post_object');

function change_post_object()
{
    $get_post_type = get_post_type_object('post');
    $labels = $get_post_type->labels;
    $labels->name = 'Yrkesguide';
    $labels->menu_name = 'Yrkesguide';
    $labels->name_admin_bar = 'Yrkesguide';
}

add_filter('woocommerce_billing_fields', 'additional_woocommerce_billing_fields'); //Custom billing country

function additional_woocommerce_billing_fields($fields)
{
    if ($fields['company-ordrereferanse']) {
        $fields['company-ordrereferanse'] = array(
            'label' => __('PO Number', 'woocommerce'),
            'required' => false,
            'clear' => false,
            'type' => 'text',
        );
    }
    if ($fields['billing_organization_number']) {
        $fields['billing_organization_number'] = array(
            'label' => __('Organisasjonsnummer', 'woocommerce'),
            'required' => true,
            'clear' => false,
            'type' => 'text',
        );
    }
    return $fields;
}

if (is_admin()) {
    add_action('woocommerce_admin_order_data_after_billing_address', 'display_admin_order_meta', 10, 1);
}

function display_admin_order_meta($order)
{
    if (get_post_meta($order->get_id(), '_billing_organization_number', true)) {
        echo '<strong>Organisasjonsnummer:</strong> <div>' . get_post_meta($order->get_id(), '_billing_organization_number', true) . '</div><br>';
    }

    if (get_post_meta($order->get_id(), '_company-ordrereferanse', true)) {
        echo '<strong>PO Number:</strong> <div>' . get_post_meta($order->get_id(), '_company-ordrereferanse', true) . '</div><br>';
    }
}