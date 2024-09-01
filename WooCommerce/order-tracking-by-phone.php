<?php

/****
 ** By default the WC Tracking Form only allow to track down the order details by  email only. But this custom tracking form will allow user to track down either Phone or Email

 ** This code need to be placed on functions.php file
 ** To show the form use the shortcode [custom_order_tracking_form]
 **/




// ****
// * Custom Tracking BY PHONE or EMAIL
// ****


function custom_order_tracking_form_shortcode()
{
    ob_start();

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_order_tracking'])) {
        $order_id = sanitize_text_field($_POST['order_id']);
        $contact_info = sanitize_text_field($_POST['contact_info']);

        // Query to fetch the order by ID
        $order = wc_get_order($order_id);

        if ($order) {
            // Get the billing email and phone number
            $billing_email = $order->get_billing_email();
            $billing_phone = $order->get_billing_phone();

            // Check if the provided contact info matches either the email or phone
            if ($contact_info === $billing_email || $contact_info === $billing_phone) {
                // Collect order details
                $order_data = [
                    'order_id' => $order->get_id(),
                    'order_date' => $order->get_date_created()->date('Y-m-d H:i:s'),
                    'order_status' => $order->get_status(),
                    'total' => wc_price($order->get_total()), // Use wc_price() to format total
                    'items' => []
                ];

                foreach ($order->get_items() as $item_id => $item) {
                    $product_name = $item->get_name();
                    $quantity = $item->get_quantity();
                    $item_total = wc_price($item->get_total());

                    $order_data['items'][] = [
                        'product_name' => $product_name,
                        'quantity' => $quantity,
                        'item_total' => $item_total,
                    ];
                }

                // Encode the order data to pass to the front-end
                echo '<script>var orderData = ' . json_encode($order_data) . ';</script>';
            } else {
                echo '<p>' . __('The contact information does not match our records. Please check your email or phone number.', 'woocommerce') . '</p>';
            }
        } else {
            echo '<p>' . __('Order not found. Please check the order ID.', 'woocommerce') . '</p>';
        }
    }

    // Display the form
?>
    <form method="post" class="custom_order_tracking_form">
        <p class="form-row form-row-first">
            <label for="order_id"><?php _e('Order ID', 'woocommerce'); ?></label>
            <input type="text" name="order_id" id="order_id" required />
        </p>
        <p class="form-row form-row-last">
            <label for="contact_info"><?php _e('Email or Phone', 'woocommerce'); ?></label>
            <input type="text" name="contact_info" id="contact_info" required />
        </p>
        <p class="form-row">
            <button type="submit" name="custom_order_tracking" class="button"><?php _e('Track Order', 'woocommerce'); ?></button>
        </p>
    </form>

    <!-- Placeholder for the order details -->
    <div id="order-details" style="display:none;">
        <h2><?php _e('Order Details', 'woocommerce'); ?></h2>
        <table>
            <tr>
                <th><?php _e('Order ID', 'woocommerce'); ?></th>
                <td id="order-id"></td>
            </tr>
            <tr>
                <th><?php _e('Order Date', 'woocommerce'); ?></th>
                <td id="order-date"></td>
            </tr>
            <tr>
                <th><?php _e('Order Status', 'woocommerce'); ?></th>
                <td id="order-status"></td>
            </tr>
            <tr>
                <th><?php _e('Total', 'woocommerce'); ?></th>
                <td id="order-total"></td>
            </tr>
            <tr>
                <th><?php _e('Items', 'woocommerce'); ?></th>
                <td id="order-items"></td>
            </tr>
        </table>
    </div>

    <script>
        jQuery(document).ready(function($) {
            if (typeof orderData !== 'undefined') {
                // Populate the order details
                $('#order-id').text(orderData.order_id);
                $('#order-date').text(orderData.order_date);
                $('#order-status').text(orderData.order_status);
                $('#order-total').html(orderData.total); // Render total with HTML

                var itemsHtml = '<ul>';
                $.each(orderData.items, function(index, item) {
                    itemsHtml += '<li>' + item.product_name + ' (' + item.quantity + ') - ' + item.item_total + '</li>';
                });
                itemsHtml += '</ul>';

                $('#order-items').html(itemsHtml);

                // Show the order details section
                $('#order-details').show();
            }
        });
    </script>
<?php

    return ob_get_clean();
}

// Register the custom shortcode
add_shortcode('custom_order_tracking_form', 'custom_order_tracking_form_shortcode');


?>