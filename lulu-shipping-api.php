<?php
/*
Plugin Name: Lulu Shipping API Integration
Description: Shows live Lulu shipping rates at checkout using Lulu's API.
Version: 1.0
Author: Umeh Pascal
Author URI: https://umeunegbupascal.netlify.app/
*/

// Register the shipping method
add_action('woocommerce_shipping_init', 'lulu_shipping_method_init');
function lulu_shipping_method_init() {
    require_once __DIR__ . '/class-wc-shipping-lulu.php';
}

add_filter('woocommerce_shipping_methods', 'add_lulu_shipping_method');
function add_lulu_shipping_method($methods) {
    $methods['lulu_shipping'] = 'WC_Shipping_Lulu';
    return $methods;
}

// Save the selected Lulu shipping level to order meta
add_action('woocommerce_checkout_update_order_meta', function($order_id) {
    if (isset($_POST['shipping_method'][0]) && $_POST['shipping_method'][0] === 'lulu_shipping') {
        $chosen_methods = WC()->session->get('chosen_shipping_methods');
        $rates = WC()->session->get('shipping_for_package_0')['rates'];
        if (isset($rates[$chosen_methods[0]]->meta_data['lulu_shipping_level'])) {
            update_post_meta($order_id, '_lulu_shipping_level', $rates[$chosen_methods[0]]->meta_data['lulu_shipping_level']);
        }
    }
});