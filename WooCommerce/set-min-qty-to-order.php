<?php

/****
 ** This code will set a minimum quantity ( ex. 3) to order
 ** This code need to be placed on theme's functions.php file
 **/



// Set minimum quantity to 3 for all products on single product pages
add_filter('woocommerce_quantity_input_args', 'set_min_quantity', 10, 2);
function set_min_quantity($args, $product)
{
    if (is_product()) {
        $args['min_value'] = 3;
        $args['input_value'] = max($args['input_value'], 3);
    }
    return $args;
}

// Set default quantity to 3 on single product pages
add_filter('woocommerce_quantity_input_args', 'set_default_quantity', 10, 2);
function set_default_quantity($args, $product)
{
    if (is_product()) {
        $args['input_value'] = 3;
    }
    return $args;
}

// Validate minimum quantity when adding to cart from single product page
add_filter('woocommerce_add_to_cart_validation', 'validate_min_quantity', 10, 5);
function validate_min_quantity($passed, $product_id, $quantity, $variation_id = '', $variations = '')
{
    if (is_product() && $quantity < 3) {
        wc_add_notice(__('The minimum quantity for this product is 3.', 'woocommerce'), 'error');
        $passed = false;
    }
    return $passed;
}

// Update cart item quantity if it's less than 3
add_filter('woocommerce_update_cart_validation', 'update_cart_min_quantity', 10, 4);
function update_cart_min_quantity($passed, $cart_item_key, $values, $quantity)
{
    if ($quantity < 3) {
        wc_add_notice(__('The minimum quantity for each product is 3.', 'woocommerce'), 'error');
        $passed = false;
    }
    return $passed;
}

// Validate minimum quantity before checkout
add_action('woocommerce_check_cart_items', 'enforce_min_quantity_before_checkout');
function enforce_min_quantity_before_checkout()
{
    $min_quantity = 3;

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        if ($cart_item['quantity'] < $min_quantity) {
            $product_name = $cart_item['data']->get_name();
            wc_add_notice(
                sprintf(
                    __('The minimum quantity for "%s" is %d. Please update your cart before proceeding.', 'woocommerce'),
                    $product_name,
                    $min_quantity
                ),
                'error'
            );
        }
    }
}
