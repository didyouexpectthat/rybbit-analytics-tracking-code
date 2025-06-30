<?php

/*
Plugin Name: Rybbit Analytics Tracking Code
Plugin URI: https://github.com/didyouexpectthat/rybbit-analytics-tracking-code
Description: Integrates Rybbit tracking code into your WordPress site.
Version: 1.4
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
define('RYBBIT_PLUGIN_VERSION', '1.4');
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
	add_option('rybbit_track_pgv', true);
	add_option('rybbit_track_spa', true);
	add_option('rybbit_track_query', true);
	add_option('rybbit_track_errors', false);
	add_option('rybbit_web_vitals', false);
	add_option('rybbit_session_replay', false);
	add_option('rybbit_skip_patterns', '');
	add_option('rybbit_mask_patterns', '');
	add_option('rybbit_debounce', 500);
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

	register_setting('rybbit_settings', 'rybbit_track_pgv', array(
		'sanitize_callback' => 'rest_sanitize_boolean',
		'default' => true
	));

	register_setting('rybbit_settings', 'rybbit_track_spa', array(
		'sanitize_callback' => 'rest_sanitize_boolean',
		'default' => true
	));

	register_setting('rybbit_settings', 'rybbit_track_query', array(
		'sanitize_callback' => 'rest_sanitize_boolean',
		'default' => true
	));

	register_setting('rybbit_settings', 'rybbit_skip_patterns', array(
		'sanitize_callback' => 'sanitize_textarea_field',
		'default' => ''
	));

	register_setting('rybbit_settings', 'rybbit_mask_patterns', array(
		'sanitize_callback' => 'sanitize_textarea_field',
		'default' => ''
	));

 register_setting('rybbit_settings', 'rybbit_debounce', array(
        'sanitize_callback' => 'rybbit_validate_debounce',
        'default' => 500
    ));

    register_setting('rybbit_settings', 'rybbit_track_errors', array(
        'sanitize_callback' => 'rest_sanitize_boolean',
        'default' => false
    ));

    register_setting('rybbit_settings', 'rybbit_web_vitals', array(
        'sanitize_callback' => 'rest_sanitize_boolean',
        'default' => false
    ));

    register_setting('rybbit_settings', 'rybbit_session_replay', array(
        'sanitize_callback' => 'rest_sanitize_boolean',
        'default' => false
    ));

    add_settings_section(
		'rybbit_settings_section',
		'Rybbit Settings',
		'rybbit_settings_section_callback',
		'rybbit-settings'
	);

	add_settings_section(
		'rybbit_advanced_section',
		'Advanced Settings',
		'rybbit_advanced_section_callback',
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

	add_settings_field(
		'rybbit_track_pgv',
		'Automatically track initial pageview',
		'rybbit_track_pgv_render',
		'rybbit-settings',
		'rybbit_advanced_section'
	);

	add_settings_field(
		'rybbit_track_spa',
		'Automatically track SPA navigation',
		'rybbit_track_spa_render',
		'rybbit-settings',
		'rybbit_advanced_section'
	);

	add_settings_field(
		'rybbit_track_query',
		'Track URL query parameters',
		'rybbit_track_query_render',
		'rybbit-settings',
		'rybbit_advanced_section'
	);

	add_settings_field(
		'rybbit_skip_patterns',
		'Skip Patterns',
		'rybbit_skip_patterns_render',
		'rybbit-settings',
		'rybbit_advanced_section'
	);

	add_settings_field(
		'rybbit_mask_patterns',
		'Mask Patterns',
		'rybbit_mask_patterns_render',
		'rybbit-settings',
		'rybbit_advanced_section'
	);

    add_settings_field(
        'rybbit_debounce',
        'Debounce Duration (ms)',
        'rybbit_debounce_render',
        'rybbit-settings',
        'rybbit_advanced_section'
    );

    add_settings_field(
        'rybbit_track_errors',
        'Track JavaScript errors',
        'rybbit_track_errors_render',
        'rybbit-settings',
        'rybbit_advanced_section'
    );

    add_settings_field(
        'rybbit_web_vitals',
        'Enable Web Vitals performance metrics',
        'rybbit_web_vitals_render',
        'rybbit-settings',
        'rybbit_advanced_section'
    );

    add_settings_field(
        'rybbit_session_replay',
        'Enable session replay',
        'rybbit_session_replay_render',
        'rybbit-settings',
        'rybbit_advanced_section'
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
 * Advanced settings section callback
 *
 */
function rybbit_advanced_section_callback() {
	echo '<p>Configure advanced tracking options for Rybbit.</p>';
}

/**
 * Render the SPA tracking toggle
 */
function rybbit_track_pgv_render() {
	$track_spa = get_option('rybbit_track_pgv', true);
	?>
    <label>
        <input type='checkbox' name='rybbit_track_spa' <?php checked($track_spa); ?>>
        For SPAs: track page views when URL changes (using History API)
    </label>
	<?php
}


/**
 * Render the SPA tracking toggle
 */
function rybbit_track_spa_render() {
	$track_spa = get_option('rybbit_track_spa', true);
	?>
    <label>
        <input type='checkbox' name='rybbit_track_spa' <?php checked($track_spa); ?>>
        For SPAs: track page views when URL changes (using History API)
    </label>
	<?php
}

/**
 * Render the query parameters tracking toggle
 */
function rybbit_track_query_render() {
	$track_query = get_option('rybbit_track_query', true);
	?>
    <label>
        <input type='checkbox' name='rybbit_track_query' <?php checked($track_query); ?>>
        Include query parameters in tracked URLs (may contain sensitive data)
    </label>
	<?php
}

/**
 * Render the skip patterns textarea
 */
function rybbit_skip_patterns_render() {
	$skip_patterns = get_option('rybbit_skip_patterns', '');
	?>
    <textarea name='rybbit_skip_patterns' rows='5' cols='50'><?php echo esc_textarea($skip_patterns); ?></textarea>
    <p class="description">URL patterns to exclude from tracking (one per line)<br>Use * for single segment wildcard, ** for multi-segment wildcard</p>
	<?php
}

/**
 * Render the mask patterns textarea
 */
function rybbit_mask_patterns_render() {
	$mask_patterns = get_option('rybbit_mask_patterns', '');
	?>
    <textarea name='rybbit_mask_patterns' rows='5' cols='50'><?php echo esc_textarea($mask_patterns); ?></textarea>
    <p class="description">URL patterns to anonymize in analytics (one per line)<br>E.g. /users/*/profile will hide usernames, /orders/** will hide order details</p>
	<?php
}

/**
 * Render the debounce duration field
 */
function rybbit_debounce_render() {
	$debounce = get_option('rybbit_debounce', 500);
	?>
    <input type='number' name='rybbit_debounce' value='<?php echo esc_attr($debounce); ?>' min='1' max='10000' step='1'>
	<?php
}

/**
 * Render the track JavaScript errors toggle
 */
function rybbit_track_errors_render() {
	$track_errors = get_option('rybbit_track_errors', false);
	?>
    <label>
        <input type='checkbox' name='rybbit_track_errors' <?php checked($track_errors); ?>>
        Automatically capture and track JavaScript errors on your site
    </label>
	<?php
}

/**
 * Render the Web Vitals performance metrics toggle
 */
function rybbit_web_vitals_render() {
	$web_vitals = get_option('rybbit_web_vitals', false);
	?>
    <label>
        <input type='checkbox' name='rybbit_web_vitals' <?php checked($web_vitals); ?>>
        Collect Core Web Vitals (LCP, CLS, INP) and additional metrics (FCP, TTFB)
    </label>
	<?php
}

/**
 * Render the session replay toggle
 */
function rybbit_session_replay_render() {
	$session_replay = get_option('rybbit_session_replay', false);
	?>
    <label>
        <input type='checkbox' name='rybbit_session_replay' <?php checked($session_replay); ?>>
        Record user interactions and DOM changes for debugging and UX analysis
    </label>
	<?php
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
 * Validate and sanitize the debounce duration
 *
 * Ensures the debounce value is within acceptable limits (1-10000ms)
 *
 * @param mixed $value The value to validate
 * @return int The sanitized debounce value
 */
function rybbit_validate_debounce($value) {
	// Convert to integer
	$value = absint($value);

	// Check minimum value
	if ($value < 1) {
		add_settings_error(
			'rybbit_debounce',
			'rybbit_debounce_too_low',
			'Debounce duration must be at least 1ms',
			'error'
		);
		// Return the default value
		return get_option('rybbit_debounce', 500);
	}

	// Check maximum value (10 seconds = 10000ms)
	if ($value > 10000) {
		add_settings_error(
			'rybbit_debounce',
			'rybbit_debounce_too_high',
			'Debounce duration cannot exceed 10000ms (10 seconds)',
			'error'
		);
		// Return the default value
		return get_option('rybbit_debounce', 500);
	}

	return $value;
}

/**
 * Process patterns from textarea to JSON array
 *
 * @param string $patterns_text Patterns from textarea (one per line)
 * @return string JSON encoded array of patterns or empty string on error
 */
function rybbit_process_patterns($patterns_text) {
	if (empty($patterns_text)) {
		return '';
	}

	// Split by newlines and filter out empty lines
	$patterns = preg_split('/\r\n|\r|\n/', $patterns_text);
	$patterns = array_filter($patterns, function($line) {
		return trim($line) !== '';
	});

	// Validate patterns - basic check for potentially problematic characters
	foreach ($patterns as $key => $pattern) {
		// Sanitize each pattern
		$patterns[$key] = sanitize_text_field($pattern);

		// Check for unbalanced wildcards or other potentially problematic patterns
		if (substr_count($pattern, '*') > 0 && strpos($pattern, '**') === false && substr_count($pattern, '*') % 2 !== 0) {
			// This is just a warning, not blocking submission
			add_settings_error(
				'rybbit_patterns',
				'rybbit_pattern_warning',
				'Warning: Pattern "' . esc_html($pattern) . '" may have unbalanced wildcards. Please verify your pattern syntax.',
				'warning'
			);
		}
	}

	// Attempt to JSON encode the patterns
	$json = json_encode(array_values($patterns));

	// Check for JSON encoding errors
	if ($json === false) {
		add_settings_error(
			'rybbit_patterns',
			'rybbit_json_error',
			'Error encoding patterns: ' . esc_html(json_last_error_msg()),
			'error'
		);
		return '';
	}

	return $json;
}

/**
 * Add the Rybbit tracking code to the site header
 */
function rybbit_add_tracking_code() {
	// Get options with defaults
	$script_url         = get_option( 'rybbit_script_url', 'https://tracking.example.com/api/script.js' );
	$site_id            = get_option( 'rybbit_site_id', '' );
	$track_pgv          = get_option( 'rybbit_track_pgv', true );
	$track_spa          = get_option( 'rybbit_track_spa', true );
	$track_query        = get_option( 'rybbit_track_query', true );
	$track_errors       = get_option( 'rybbit_track_errors', false );
	$web_vitals         = get_option( 'rybbit_web_vitals', false );
	$session_replay     = get_option( 'rybbit_session_replay', false );
	$skip_patterns_text = get_option( 'rybbit_skip_patterns', '' );
	$mask_patterns_text = get_option( 'rybbit_mask_patterns', '' );
	$debounce           = get_option( 'rybbit_debounce', 500 );

	// Validate script URL
	if ( empty( $script_url ) || ! filter_var( $script_url, FILTER_VALIDATE_URL ) ) {
		// Log error but don't output anything to the page
		error_log( 'Rybbit: Invalid script URL' );

		return;
	}

	// Validate site ID
	if ( empty( $site_id ) ) {
		// No site ID, don't output the script
		return;
	}

	// Validate debounce value
	$debounce = absint( $debounce );
	if ( $debounce < 1 ) {
		$debounce = 500; // Use default if invalid
	} else if ( $debounce > 10000 ) {
		$debounce = 10000; // Cap at maximum
	}

	// Process patterns from textarea to JSON arrays
	try {
		$skip_patterns_json = rybbit_process_patterns( $skip_patterns_text );
		$mask_patterns_json = rybbit_process_patterns( $mask_patterns_text );
	} catch ( Exception $e ) {
		// Log error but continue with empty patterns
		error_log( 'Rybbit: Error processing patterns: ' . $e->getMessage() );
		$skip_patterns_json = '';
		$mask_patterns_json = '';
	}

	// Output the script
	echo "<script\n";
	echo "    src=\"" . esc_url( $script_url ) . "\"\n";
	echo "    data-site-id=\"" . esc_attr( $site_id ) . "\"\n";

	// Add track inital pageview attribute if disabled (default is enabled)
	if ( ! $track_pgv ) {
		echo "    data-auto-track-pageview=\"false\"\n";
	}

	// Add SPA tracking attribute if disabled (default is enabled)
	if ( ! $track_spa ) {
		echo "    data-track-spa=\"false\"\n";
	}

	// Add query tracking attribute if disabled (default is enabled)
	if ( ! $track_query ) {
		echo "    data-track-query=\"false\"\n";
	}

	// Add skip patterns if set
	if ( ! empty( $skip_patterns_json ) ) {
		echo "    data-skip-patterns='" . esc_attr( $skip_patterns_json ) . "'\n";
	}

	// Add mask patterns if set
	if ( ! empty( $mask_patterns_json ) ) {
		echo "    data-mask-patterns='" . esc_attr( $mask_patterns_json ) . "'\n";
	}

	// Add debounce if different from default
	if ( $debounce != 500 ) {
		echo "    data-debounce=\"" . esc_attr( $debounce ) . "\"\n";
	}

	// Add track errors attribute if enabled (default is disabled)
	if ( $track_errors ) {
		echo "    data-track-errors=\"true\"\n";
	}

	// Add web vitals attribute if enabled (default is disabled)
	if ( $web_vitals ) {
		echo "    data-web-vitals=\"true\"\n";
	}

	// Add session replay attribute if enabled (default is disabled)
	if ( $session_replay ) {
		echo "    data-session-replay=\"true\"\n";
	}

	echo "    defer\n";
	echo "></script>\n";
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
    delete_option('rybbit_track_pgv');
    delete_option('rybbit_track_spa');
    delete_option('rybbit_track_query');
    delete_option('rybbit_track_errors');
    delete_option('rybbit_web_vitals');
    delete_option('rybbit_session_replay');
    delete_option('rybbit_skip_patterns');
    delete_option('rybbit_mask_patterns');
    delete_option('rybbit_debounce');
}
register_uninstall_hook(__FILE__, 'rybbit_uninstall');
