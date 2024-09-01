<?php

/****
 ** If you need to set a fixed shipping method if the minimum order quantity is 'n'
 *
 ** This code will allow Free Shipping if the order quantity if at leas 4.
 *
 ** If the order amount is at least 4 it will disable all other methods except 'Free Shipping'. Else, it will show all your available Shipping methods you have created from the WC settings.
 *
 ** Make sure you have 'Free Shipping' method added from the WC settings.
 *
 ** This code need to be placed on theme's functions.php file
 **/


// *****
// Min Qty Shipping Method - Auto Free Shipping
// *****

function custom_free_shipping_for_quantity($available_methods)
{
    // Get the total quantity in the cart
    $cart_quantity = WC()->cart->get_cart_contents_count();

    // If the cart quantity is 4 or more
    if ($cart_quantity >= 4) {
        // Loop through the available shipping methods
        foreach ($available_methods as $method_id => $method) {
            // If the method is not free_shipping, unset it
            if ('free_shipping' !== $method->method_id) {
                unset($available_methods[$method_id]);
            }
        }
        // Automatically select free shipping
        WC()->session->set('chosen_shipping_methods', array('free_shipping'));
    } else {
        // If the cart quantity is less than 4, remove free shipping method
        foreach ($available_methods as $method_id => $method) {
            if ('free_shipping' === $method->method_id) {
                unset($available_methods[$method_id]);
            }
        }
    }

    return $available_methods;
}
add_filter('woocommerce_package_rates', 'custom_free_shipping_for_quantity', 100);
