<?php
/*
Plugin Name: Maintenance Mode
Plugin URI: https://saurabhkg.netlify.app/
Description: A plugin to enable maintenance mode with custom styling and a countdown timer.
Version: 2.0
Author: Saurabh
Author URI: https://saurabhkg.netlify.app/
License: GPL2
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Function to check if maintenance mode is enabled
function is_maintenance_enabled()
{
    return get_option('maintenance_mode_status', 'disabled') === 'enabled';
}

// Function to show maintenance mode page
function enable_maintenance_mode()
{
    if (is_maintenance_enabled() && !current_user_can('manage_options')) {
?>
        <html>

        <head>
            <title>Website Under Maintenance</title>
            <style>
                body {
                    text-align: center;
                    padding: 50px;
                    font-family: Arial, sans-serif;
                    background: #f8f9fa;
                }

                .container {
                    max-width: 600px;
                    margin: auto;
                    padding: 20px;
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                }

                h1 {
                    color: #d9534f;
                }

                #countdown {
                    font-size: 24px;
                    font-weight: bold;
                    color: #5bc0de;
                }
            </style>
            <script>
                function startCountdown(duration) {
                    let timer = duration,
                        minutes, seconds;
                    let countdownElement = document.getElementById('countdown');
                    let interval = setInterval(function() {
                        minutes = parseInt(timer / 60, 10);
                        seconds = parseInt(timer % 60, 10);
                        minutes = minutes < 10 ? "0" + minutes : minutes;
                        seconds = seconds < 10 ? "0" + seconds : seconds;
                        countdownElement.textContent = minutes + ":" + seconds;
                        if (--timer < 0) {
                            clearInterval(interval);
                            countdownElement.textContent = "We are back!";
                        }
                    }, 1000);
                }
                window.onload = function() {
                    let countdownDuration = 5 * 60; // 5 minutes countdown
                    startCountdown(countdownDuration);
                };
            </script>
        </head>

        <body>
            <div class="container">
                <h1>Website Under Maintenance</h1>
                <p>We are making improvements. Please check back soon!</p>
                <p>Estimated time remaining: <span id="countdown">05:00</span></p>
            </div>
        </body>

        </html>
    <?php
        exit();
    }
}
add_action('template_redirect', 'enable_maintenance_mode');

// Add Settings Menu in WordPress Admin
function maintenance_mode_menu()
{
    add_menu_page(
        'Maintenance Mode Settings',
        'Maintenance Mode',
        'manage_options',
        'maintenance-mode-settings',
        'maintenance_mode_settings_page',
        'dashicons-hammer',
        100
    );
}
add_action('admin_menu', 'maintenance_mode_menu');

// Settings Page Content
function maintenance_mode_settings_page()
{
    ?>
    <div class="wrap">
        <h1>Maintenance Mode Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('maintenance_mode_settings');
            do_settings_sections('maintenance-mode-settings');
            submit_button();
            ?>
        </form>
    </div>
<?php
}

// Register Settings
function maintenance_mode_register_settings()
{
    register_setting('maintenance_mode_settings', 'maintenance_mode_status');
    add_settings_section('maintenance_mode_section', 'Settings', null, 'maintenance-mode-settings');
    add_settings_field(
        'maintenance_mode_status',
        'Enable Maintenance Mode',
        'maintenance_mode_status_callback',
        'maintenance-mode-settings',
        'maintenance_mode_section'
    );
}
add_action('admin_init', 'maintenance_mode_register_settings');

// Callback Function for Checkbox
function maintenance_mode_status_callback()
{
    $status = get_option('maintenance_mode_status', 'disabled');
?>
    <input type="checkbox" name="maintenance_mode_status" value="enabled" <?php checked($status, 'enabled'); ?> />
    <label>Check to enable maintenance mode</label>
<?php
}
