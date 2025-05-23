<?php

/*
Plugin Name: Rybbit Analytics Tracking Code
Plugin URI: https://github.com/didyouexpectthat/rybbit-analytics-tracking-code
Description: Integrates Rybbit tracking code into your WordPress site.
Version: 1.1
Author: didyouexpectthat
Author URI: https://github.com/didyouexpectthat/
License: GNU General Public License v2.0

Rybbit is a trademark and copyright of Rybbit.
This plugin is not affiliated with or endorsed by Rybbit.

Copyright (c) 2025 didyouexpectthat

*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('RYBBIT_PLUGIN_VERSION', '1.0');
define('RYBBIT_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RYBBIT_PLUGIN_URL', plugin_dir_url(__FILE__));

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'rybbit_activate');
register_deactivation_hook(__FILE__, 'rybbit_deactivate');

/**
 * Plugin activation function
 *
 * Initializes default options for Rybbit integration.
 */
function rybbit_activate() {
    // Initialize default options
    add_option('rybbit_script_url', 'https://tracking.example.com/api/script.js');
    add_option('rybbit_site_id', '');
}

/**
 * Plugin deactivation function
 *
 * We intentionally preserve Rybbit settings upon deactivation so that
 * users can temporarily deactivate without losing their configuration.
 * For complete cleanup, use the uninstall hook.
 *
 */
function rybbit_deactivate() {
    // No cleanup on deactivation - settings are preserved
}

/**
 * Add settings link on plugin page
 *
 * Adds a convenient "Settings" link to the Rybbit plugin entry
 * on the WordPress plugins page.
 *

 */
function rybbit_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=rybbit-settings">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'rybbit_settings_link');

/**
 * Register the Rybbit settings page
 *
 * Adds a settings page for Rybbit configuration under the WordPress Settings menu.
 *

 */
function rybbit_add_admin_menu() {
    add_options_page(
        'Rybbit Settings',
        'Rybbit',
        'manage_options',
        'rybbit-settings',
        'rybbit_options_page'
    );
}
add_action('admin_menu', 'rybbit_add_admin_menu');

/**
 * Validate and sanitize the Rybbit script URL
 *
 * Ensures the URL ends with '/api/script.js' as required by Rybbit.
 *
 * @param string $url The URL to validate
 * @return string The sanitized URL or default URL if invalid
 */
function rybbit_validate_script_url($url) {
    // First sanitize the URL
    $url = esc_url_raw($url);

    // Check if the URL ends with '/api/script.js'
    if (!empty($url) && !preg_match('#/api/script\.js$#', $url)) {
        // URL doesn't end with the required suffix
        add_settings_error(
            'rybbit_script_url',
            'rybbit_script_url_error',
            'The Rybbit script URL must end with "/api/script.js"',
            'error'
        );
        // Return the previous valid value or default
        return get_option('rybbit_script_url', 'https://tracking.example.com/api/script.js');
    }

    return $url;
}

/**
 * Register Rybbit settings
 *
 * Registers the settings fields for Rybbit configuration.
 */
function rybbit_settings_init() {
    register_setting('rybbit_settings', 'rybbit_script_url', array(
        'sanitize_callback' => 'rybbit_validate_script_url',
        'default' => 'https://tracking.example.com/api/script.js'
    ));

    register_setting('rybbit_settings', 'rybbit_site_id', array(
        'sanitize_callback' => 'sanitize_text_field',
        'default' => ''
    ));

    add_settings_section(
        'rybbit_settings_section',
        'Rybbit Settings',
        'rybbit_settings_section_callback',
        'rybbit-settings'
    );

    add_settings_field(
        'rybbit_script_url',
        'Script URL',
        'rybbit_script_url_render',
        'rybbit-settings',
        'rybbit_settings_section'
    );

    add_settings_field(
        'rybbit_site_id',
        'Site ID',
        'rybbit_site_id_render',
        'rybbit-settings',
        'rybbit_settings_section'
    );
}
add_action('admin_init', 'rybbit_settings_init');

/**
 * Render the Rybbit script URL field
 *
 * Displays the input field for the Rybbit script URL.
 *

 */
function rybbit_script_url_render() {
    $script_url = get_option('rybbit_script_url', '');
    ?>
    <input type='url' class='regular-text' name='rybbit_script_url' value='<?php echo esc_attr($script_url); ?>'>
    <p class="description">The URL to the Rybbit script (default: https://tracking.example.com/api/script.js)</p>
    <?php
}

/**
 * Render the Rybbit site ID field
 *
 * Displays the input field for the Rybbit site ID.
 *

 */
function rybbit_site_id_render() {
    $site_id = get_option('rybbit_site_id', '');
    ?>
    <input type='text' class='regular-text' name='rybbit_site_id' value='<?php echo esc_attr($site_id); ?>'>
    <p class="description">Your Rybbit Site ID</p>
    <?php
}

/**
 * Settings section callback
 *
 */
function rybbit_settings_section_callback() {
    echo '<p>Configure your Rybbit integration. You need to provide the script URL and your site ID.</p>';
    echo '<p><small>Rybbit is a trademark and copyright of Rybbit. This plugin is not affiliated with or endorsed by Rybbit.</small></p>';
}

/**
 * Settings page content
 *
 * Displays the Rybbit configuration form.
 */
function rybbit_options_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action='options.php' method='post'>
            <?php
            settings_fields('rybbit_settings');
            do_settings_sections('rybbit-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Add the Rybbit tracking code to the site header
 */
function rybbit_add_tracking_code() {
    $script_url = get_option('rybbit_script_url', 'https://tracking.example.com/api/script.js');
    $site_id = get_option('rybbit_site_id', '');

    // Only output the script if site ID is set
    if (!empty($site_id)) {
        echo "<script\n";
        echo "    src=\"" . esc_url($script_url) . "\"\n";
        echo "    data-site-id=\"" . esc_attr($site_id) . "\"\n";
        echo "    defer\n";
        echo "></script>\n";
    }
}
add_action('wp_head', 'rybbit_add_tracking_code');

/**
 * Plugin uninstall hook - Clean up plugin data when it's deleted
 *
 * Removes configuration data from the database.
 */
function rybbit_uninstall() {
    // Remove all options created by the plugin
    delete_option('rybbit_script_url');
    delete_option('rybbit_site_id');
}
register_uninstall_hook(__FILE__, 'rybbit_uninstall');
