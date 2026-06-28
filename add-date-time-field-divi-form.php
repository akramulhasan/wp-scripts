<?php

/**
 * Divi Contact Form - Date & Time Field Fix
 *
 * This snippet converts normal Divi Contact Form text input fields into native
 * browser Date and Time fields.
 *
 * Why this is needed:
 * Divi Contact Form does not provide native date/time field types. This code lets
 * you create two normal input fields in the Divi Contact Form module and converts
 * them into date/time picker fields on the frontend.
 *
 * Where to place:
 * - Theme/child theme functions.php file; or
 * - Code Snippets / WPCode plugin as a PHP snippet.
 *
 * Divi Contact Form field IDs required:
 * - appointment_date
 * - appointment_time
 *
 * In Divi, create two normal Input Field fields:
 * - Field ID: appointment_date
 * - Field ID: appointment_time
 *
 * If you use different field IDs, update the values in the JavaScript selectors below.
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Add frontend JavaScript to convert Divi text inputs into date/time fields.
 */

add_action('wp_footer', function () {
    if (is_admin()) {
        return;
    }
?>
    <script>
        (function() {
            const dateSelector = 'input[name^="et_pb_contact_appointment_date_"], input[id^="et_pb_contact_appointment_date_"]';
            const timeSelector = 'input[name^="et_pb_contact_appointment_time_"], input[id^="et_pb_contact_appointment_time_"]';

            function convertToDateTimeFields(context) {
                context = context || document;

                const dateFields = context.querySelectorAll(dateSelector);
                const timeFields = context.querySelectorAll(timeSelector);

                dateFields.forEach(function(field) {
                    field.setAttribute('type', 'date');
                    field.setAttribute('min', new Date().toISOString().split('T')[0]);
                    field.setAttribute('autocomplete', 'off');
                });

                timeFields.forEach(function(field) {
                    field.setAttribute('type', 'time');
                    field.setAttribute('step', '900'); // 15-minute interval
                    field.setAttribute('autocomplete', 'off');
                });
            }

            function convertBackToTextFields(form) {
                form.querySelectorAll(dateSelector).forEach(function(field) {
                    field.setAttribute('type', 'text');
                });

                form.querySelectorAll(timeSelector).forEach(function(field) {
                    field.setAttribute('type', 'text');
                });
            }

            function initDiviDateTimeFix() {
                convertToDateTimeFields(document);

                document.querySelectorAll('.et_pb_contact_form').forEach(function(form) {
                    if (form.dataset.diviDateTimeFixed === 'yes') {
                        return;
                    }

                    form.dataset.diviDateTimeFixed = 'yes';

                    // Native form submit fallback
                    form.addEventListener('submit', function() {
                        convertBackToTextFields(form);
                    }, true);

                    // Divi usually handles contact form submission via button click/AJAX
                    const submitButtons = form.querySelectorAll(
                        'button[type="submit"], input[type="submit"], .et_pb_contact_submit'
                    );

                    submitButtons.forEach(function(button) {
                        ['mousedown', 'touchstart', 'click'].forEach(function(eventName) {
                            button.addEventListener(eventName, function() {
                                convertBackToTextFields(form);

                                // If submission fails, restore date/time picker again
                                setTimeout(function() {
                                    convertToDateTimeFields(form);
                                }, 3000);
                            }, true);
                        });
                    });
                });
            }

            document.addEventListener('DOMContentLoaded', initDiviDateTimeFix);
            window.addEventListener('load', initDiviDateTimeFix);
            setTimeout(initDiviDateTimeFix, 1000);
        })();
    </script>
<?php
});
