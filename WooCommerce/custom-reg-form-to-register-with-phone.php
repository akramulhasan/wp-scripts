<?php

/****
 ** This code will create a custom registraton form shortcode which will allow user to get registrered with phone number or email
 ** This code need to be placed on theme's functions.php file
 ** Here is the shortcode -> [custom_woocommerce_registration_form]
 **/


// Register the custom shortcode for WooCommerce registration form
function custom_woocommerce_registration_form_shortcode()
{
    ob_start();
?>
    <form id="custom-register-form" method="post">
        <p class="form-row form-row-wide">
            <label for="reg_username"><?php _e('Username (Email or Phone)', 'woocommerce'); ?> <span class="required">*</span></label>
            <input type="text" class="input-text" name="username" id="reg_username" value="<?php echo isset($_POST['username']) ? esc_attr($_POST['username']) : ''; ?>" required />
        </p>

        <p class="form-row form-row-wide">
            <label for="reg_password"><?php _e('Password', 'woocommerce'); ?> <span class="required">*</span></label>
            <input type="password" class="input-text" name="password" id="reg_password" required />
            <span class="show-password-input"></span>
        </p>

        <p class="form-row">
            <button type="submit" class="woocommerce-Button button" name="register" value="Register"><?php _e('Register', 'woocommerce'); ?></button>
        </p>
        <?php
        // Display any registration errors
        if (isset($GLOBALS['registration_errors']) && is_wp_error($GLOBALS['registration_errors'])) {
            echo '<div class="woocommerce-error">' . $GLOBALS['registration_errors']->get_error_message() . '</div>';
        }
        ?>
    </form>
<?php
    return ob_get_clean();
}
add_shortcode('custom_woocommerce_registration_form', 'custom_woocommerce_registration_form_shortcode');

// Handle form submission and registration
function custom_handle_registration()
{
    if (isset($_POST['register'])) {
        $username = sanitize_user($_POST['username']);
        $password = sanitize_text_field($_POST['password']);

        if (empty($username) || empty($password)) {
            $GLOBALS['registration_errors'] = new WP_Error();
            if (empty($username)) {
                $GLOBALS['registration_errors']->add('username_error', __('Username is required.', 'woocommerce'));
            }
            if (empty($password)) {
                $GLOBALS['registration_errors']->add('password_error', __('Password is required.', 'woocommerce'));
            }
            return;
        }

        // Check if the username already exists
        if (username_exists($username)) {
            $GLOBALS['registration_errors'] = new WP_Error();
            $GLOBALS['registration_errors']->add('username_exists', __('Username already exists.', 'woocommerce'));
            return;
        }

        // Create the user
        $user_id = wp_create_user($username, $password);

        if (!is_wp_error($user_id)) {
            // Log in the user
            wp_set_auth_cookie($user_id);
            wp_redirect(home_url() . '/my-account');
            exit;
        } else {
            $GLOBALS['registration_errors'] = $user_id;
        }
    }
}
add_action('wp', 'custom_handle_registration');
