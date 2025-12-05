<?php

if (! class_exists('WC_Shipping_Lulu')) {
    class WC_Shipping_Lulu extends WC_Shipping_Method
    {
        public function __construct()
        {
            $this->id                 = 'lulu_shipping';
            $this->method_title       = __('Lulu Shipping');
            $this->method_description = __('Get live shipping rates from Lulu.');
            $this->enabled            = "yes";
            $this->title              = "Lulu Shipping";
        }

        public function calculate_shipping($package = array())
        {
            try {
                // Gather shipping address
                $address = $package['destination'];
                $country = $address['country'] ?? '';
                $postcode = $address['postcode'] ?? '';
                $city = $address['city'] ?? '';
                $street1 = $address['address'] ?? '';

                // If the state/county field is present, use it; if not, set dummy for Lulu
                $state_field_present = array_key_exists('state', $address);
                $state = $state_field_present ? $address['state'] : 'N/A';

                // Only require all other Lulu fields (except state/county) to be filled
                if (
                    empty($country) ||
                    empty($postcode) ||
                    empty($city) ||
                    empty($street1)
                ) {
                    error_log('[Lulu Shipping] Required address field missing: ' . json_encode([
                        'country' => $country,
                        'postcode' => $postcode,
                        'city' => $city,
                        'street1' => $street1
                    ]));
                    return;
                }

                // Supported Lulu product IDs
                $supported_product_ids = [
                    6924, 6870, 6800, 6795, 6791, 6759, 6752, 6743, 6735, 6623, 6615, 6340, 6310, 6109, 6107, 6105, 6100, 6898, 6878, 6864, 6347, 6567, 6701, 6672, 6642, 6860, 6832, 6917, 6908, 6914, 6905, 6601, 6765, 6927, 6353, 6421, 6405, 6398, 6810, 6821, 6839, 6847, 6325, 6319, 6589, 6583, 6826, 6851, 6789, 6784, 6632, 6562, 6557, 6549, 6539, 6536, 6531, 6529, 6524, 6576, 6728, 6717
                ];

                // Product map for pod_package_id (add more fields if needed)
                $product_map = [
                    6924 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6870 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6800 => [ 'pod_package_id' => '0600X0900BWSTDPB060UW444GXX' ],
                    6795 => [ 'pod_package_id' => '0600X0900BWSTDPB060UW444GXX' ],
                    6791 => [ 'pod_package_id' => '0600X0900BWSTDPB060UW444GXX' ],
                    6759 => [ 'pod_package_id' => '0600X0900BWSTDPB060UW444GXX' ],
                    6752 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6743 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6735 => [ 'pod_package_id' => '0600X0900BWSTDPB060UW444GXX' ],
                    6623 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6615 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6340 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6310 => [ 'pod_package_id' => '0600X0900BWPREPB080CW444MXX' ],
                    6109 => [ 'pod_package_id' => '0600X0900BWPREPB080CW444GXX' ],
                    6107 => [ 'pod_package_id' => '0600X0900BWPREPB080CW444GXX' ],
                    6105 => [ 'pod_package_id' => '0600X0900BWPREPB080CW444GXX' ],
                    6100 => [ 'pod_package_id' => '0600X0900BWPREPB080CW444GXX' ],
                    6898 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6878 => [ 'pod_package_id' => '0600X0900BWSTDPB060UW444GXX' ],
                    6864 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6347 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6567 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6701 => [ 'pod_package_id' => '0850X0850FCSTDPB060UW444GXX' ],
                    6672 => [ 'pod_package_id' => '0850X0850FCSTDPB060UW444GXX' ],
                    6642 => [ 'pod_package_id' => '0850X0850FCSTDPB060UW444GXX' ],
                    6860 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6832 => [ 'pod_package_id' => '0600X0900BWSTDPB060UW444GXX' ],
                    6917 => [ 'pod_package_id' => '0600X0900BWSTDPB060UW444GXX' ],
                    6908 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6914 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6905 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6601 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6765 => [ 'pod_package_id' => '0600X0900BWSTDPB060UW444GXX' ],
                    6927 => [ 'pod_package_id' => '0850X0850FCSTDCW060UW444MXX' ],
                    6353 => [ 'pod_package_id' => '0600X0900BWSTDPB060UW444GXX' ],
                    6421 => [ 'pod_package_id' => '0600X0900BWPREPB080CW444GXX' ],
                    6405 => [ 'pod_package_id' => '0600X0900BWPREPB080CW444GXX' ],
                    6398 => [ 'pod_package_id' => '0600X0900BWPREPB080CW444GXX' ],
                    6810 => [ 'pod_package_id' => '0600X0900BWSTDPB060UW444GXX' ],
                    6821 => [ 'pod_package_id' => '0600X0900BWSTDPB060UW444GXX' ],
                    6839 => [ 'pod_package_id' => '0600X0900BWSTDPB060UW444GXX' ],
                    6847 => [ 'pod_package_id' => '0600X0900BWSTDPB060UW444GXX' ],
                    6325 => [ 'pod_package_id' => '0600X0900BWPREPB080CW444GXX' ],
                    6319 => [ 'pod_package_id' => '0600X0900BWPREPB080CW444GXX' ],
                    6589 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6583 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6826 => [ 'pod_package_id' => '0600X0900BWSTDPB060UW444GXX' ],
                    6851 => [ 'pod_package_id' => '0850X1100BWSTDPB060UW444GXX' ],
                    6789 => [ 'pod_package_id' => '0600X0900FCPRESS080CW444GXX' ],
                    6784 => [ 'pod_package_id' => '0600X0900FCPRESS080CW444GXX' ],
                    6632 => [ 'pod_package_id' => '0850X0850FCSTDPB060UW444GXX' ],
                    6562 => [ 'pod_package_id' => '0850X0850FCSTDPB060UW444GXX' ],
                    6557 => [ 'pod_package_id' => '0850X0850FCSTDPB060UW444GXX' ],
                    6549 => [ 'pod_package_id' => '0850X0850FCSTDPB060UW444GXX' ],
                    6539 => [ 'pod_package_id' => '0850X0850FCSTDPB060UW444GXX' ],
                    6536 => [ 'pod_package_id' => '0850X0850FCSTDPB060UW444GXX' ],
                    6531 => [ 'pod_package_id' => '0850X0850FCSTDPB060UW444GXX' ],
                    6529 => [ 'pod_package_id' => '0850X0850FCSTDPB060UW444GXX' ],
                    6524 => [ 'pod_package_id' => '0850X0850FCSTDPB060UW444GXX' ],
                    6576 => [ 'pod_package_id' => '0850X0850BWSTDPB060UW444GXX' ],
                    6728 => [ 'pod_package_id' => '0850X0850FCPRESS080CW444GXX' ],
                    6717 => [ 'pod_package_id' => '0850X0850FCPRESS080CW444GXX' ]
                ];

                // Gather cart items for Lulu line_items (multiple product support)
                $line_items = [];
                foreach ($package['contents'] as $cart_item) {
                    $product = $cart_item['data'];
                    $product_id = $product->get_id();
                    if (in_array($product_id, $supported_product_ids, true) && isset($product_map[$product_id])) {
                        $quantity = $cart_item['quantity'];
                        $page_count = (int) $product->get_meta('page_count');
                        if ($page_count <= 0) {
                            $page_count = 120;
                        }
                        $line_items[] = [
                            'pod_package_id' => $product_map[$product_id]['pod_package_id'],
                            'quantity' => $quantity,
                            'page_count' => $page_count
                        ];
                    }
                }

                if (empty($line_items)) {
                    error_log('[Lulu Shipping] No line items found in cart.');
                    return;
                }

                // Prepare shipping address for Lulu
                $shipping_address = [
                    'street1'    => $street1,
                    'city'       => $city,
                    'state_code' => $state,
                    'postcode'   => $postcode,
                    'country'    => $country,
                ];

                // Get Lulu API credentials
                $clientId = defined('LULU_CLIENT_ID') ? LULU_CLIENT_ID : '';
                $clientSecret = defined('LULU_CLIENT_SECRET') ? LULU_CLIENT_SECRET : '';
                if (empty($clientId) || empty($clientSecret)) {
                    error_log('[Lulu Shipping] Lulu API credentials not set.');
                    return;
                }

                // Get access token
                $authHeader = base64_encode("$clientId:$clientSecret");
                $tokenUrl = 'https://api.lulu.com/auth/realms/glasstree/protocol/openid-connect/token';
                // $tokenUrl = 'https://api.sandbox.lulu.com/auth/realms/glasstree/protocol/openid-connect/token';
                $tokenHeaders = [
                    'Authorization: Basic ' . $authHeader,
                    'Content-Type: application/x-www-form-urlencoded'
                ];
                $tokenData = http_build_query([
                    'grant_type' => 'client_credentials'
                ]);
                $ch = curl_init($tokenUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $tokenHeaders);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $tokenData);
                $tokenResponse = curl_exec($ch);
                if (curl_errno($ch)) {
                    error_log('[Lulu Shipping] Token request error: ' . curl_error($ch));
                    curl_close($ch);
                    return;
                }
                curl_close($ch);
                $tokenJson = json_decode($tokenResponse, true);
                if (!isset($tokenJson['access_token'])) {
                    error_log('[Lulu Shipping] Failed to get access token: ' . $tokenResponse);
                    return;
                }
                $accessToken = $tokenJson['access_token'];

                // Get shipping options from Lulu
                $shipping_options_url = 'https://api.lulu.com/shipping-options';
                // $shipping_options_url = 'https://api.sandbox.lulu.com/shipping-options';
                $shipping_options_payload = [
                    'currency' => 'USD',
                    'line_items' => $line_items,
                    'shipping_address' => $shipping_address
                ];
                $shipping_options_headers = [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json'
                ];
                $ch = curl_init($shipping_options_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $shipping_options_headers);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($shipping_options_payload));
                $shipping_options_response = curl_exec($ch);
                if (curl_errno($ch)) {
                    error_log('[Lulu Shipping] Shipping options request error: ' . curl_error($ch));
                    curl_close($ch);
                    return;
                }
                curl_close($ch);

                $shipping_options = json_decode($shipping_options_response, true);

                if (empty($shipping_options) || !is_array($shipping_options)) {
                    error_log('[Lulu Shipping] Invalid or empty shipping options response: ' . $shipping_options_response);
                    return;
                }

                // Sort shipping options by price (ascending)
                usort($shipping_options, function ($a, $b) {
                    return ($a['cost_excl_tax'] ?? 0) <=> ($b['cost_excl_tax'] ?? 0);
                });

                foreach ($shipping_options as $option) {
                    if (!isset($option['level']) || !isset($option['cost_excl_tax'])) {
                        error_log('[Lulu Shipping] Missing level or cost_excl_tax in option: ' . print_r($option, true));
                        continue;
                    }

                    // Build estimated delivery string
                    $estimated = '';
                    if (!empty($option['min_delivery_date']) && !empty($option['max_delivery_date'])) {
                        $estimated = 'Estimated delivery: ' . date('M j', strtotime($option['min_delivery_date'])) .
                            ' - ' . date('M j', strtotime($option['max_delivery_date']));
                    } elseif (!empty($option['total_days_min']) && !empty($option['total_days_max'])) {
                        $estimated = 'Estimated delivery: ' . $option['total_days_min'] . '-' . $option['total_days_max'] . ' days';
                    }

                    $rate = [
                        'id'    => $this->id . '_' . strtolower($option['level']),
                        'label' => 'Lulu ' . (isset($option['carrier_service_name']) ? $option['carrier_service_name'] : ucfirst(strtolower($option['level']))) .
                            ' (' . ucfirst(strtolower($option['level'])) . ')' . ($estimated ? ' — ' . $estimated : ''),
                        'cost'  => $option['cost_excl_tax'],
                        'meta_data' => [
                            'lulu_shipping_level' => $option['level'],
                            'estimated_delivery' => $estimated
                        ]
                    ];
                    $this->add_rate($rate);
                }
            } catch (Exception $e) {
                error_log('[Lulu Shipping] Exception: ' . $e->getMessage());
            }
        }
    }
}

// Default to not show the "Ship to a different address" checkbox
add_filter('woocommerce_ship_to_different_address_checked', '__return_false');

// Make the County (state) field required at checkout and always show an asterisk
add_filter('woocommerce_checkout_fields', function ($fields) {
    if (isset($fields['shipping']['shipping_state'])) {
        $fields['shipping']['shipping_state']['required'] = true;
        $fields['shipping']['shipping_state']['label'] = __('County', 'woocommerce') . ' *';
    }
    if (isset($fields['billing']['billing_state'])) {
        $fields['billing']['billing_state']['required'] = true;
        $fields['billing']['billing_state']['label'] = __('County', 'woocommerce') . ' *';
    }
    return $fields;
});

add_filter('woocommerce_default_address_fields', function ($fields) {
    if (isset($fields['state'])) {
        $fields['state']['required'] = true;
        $fields['state']['label'] = __('County', 'woocommerce') . ' *';
    }
    return $fields;
});

// Validate shipping method selection at checkout
add_filter('woocommerce_shipping_chosen_method', function ($chosen_method, $available_methods) {
    if (empty($chosen_method) && !empty($available_methods)) {
        $first_method = array_key_first($available_methods);
        return $first_method;
    }
    return $chosen_method;
}, 100, 2);
