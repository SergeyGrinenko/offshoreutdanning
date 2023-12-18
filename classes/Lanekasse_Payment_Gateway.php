<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
    return;
}
class Lanekasse_Payment_Gateway extends WC_Payment_Gateway
{
    public function __construct()
    {
        $this->id = 'lanekasse'; // Payment method ID
        $this->method_title = 'Lånekassen'; // Displayed title
        $this->title = 'Lånekassen'; // Payment title
        $this->method_description = 'Pay with Lånekassen. This Payment method avalaible only for Akvakultur VG2 and Brønnteknikk VG2.'; // Description

        $this->init_form_fields();
        $this->init_settings();

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
    }


    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => __( 'Enable/Disable', 'woocommerce' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable Lånekassen Payment Gateway', 'woocommerce' ),
                'default' => 'yes',
            ),
            'restricted_product_ids' => array(
                'title'       => __( 'Allowed Product IDs', 'woocommerce' ),
                'type'        => 'text',
                'description' => __( 'Comma-separated list of product IDs for which Lånekassen payment method should be available', 'woocommerce' ),
                'default'     => '522,518', // Default allowed product IDs
            ),
        );

    }

    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);

        // Process the payment for orders containing allowed products
        $order->update_status('processing');

        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url($order),
        );
    }
}

function add_lanekasse_payment_gateway($methods)
{
    $methods[] = 'Lanekasse_Payment_Gateway';
    return $methods;
}

add_filter('woocommerce_payment_gateways', 'add_lanekasse_payment_gateway');