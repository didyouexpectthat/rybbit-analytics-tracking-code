=== Rybbit Analytics Tracking Code ===
Contributors: didyouexpectthat
Tags: analytics, tracking, rybbit
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.5.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integrates Rybbit tracking code into your WordPress site.

== Description ==

This plugin allows you to easily add Rybbit tracking code to your WordPress site by configuring the script URL and your site ID through the WordPress admin interface.

= Features =

* Simple configuration through WordPress admin
* Customizable script URL
* Site ID configuration
* Automatic script insertion in site header
* API version toggle (v1/v2). v2 restricts the injected tag to only Skip Patterns, Mask Patterns, and Debounce
* Affiliate link prefixes: track clicks on shortened/cloaked links like /offer and /go

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/rybbit-for-wordpress` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings -> Rybbit screen to configure the plugin

== Frequently Asked Questions ==

= What is Rybbit? =

Rybbit is a tracking and analytics service. This plugin helps you integrate it with your WordPress site.

= Do I need a Rybbit account to use this plugin? =

Yes, you'll need a Rybbit account to get your Site ID.

= How do I track shortened/cloaked affiliate links like /offer or /go? =

1. In WordPress, go to Settings -> Rybbit -> Advanced Settings.
2. In "Affiliate Link Prefixes", enter the path prefixes you use for your affiliate redirects. Use one per line or separate with commas. Examples:
   - /offer
   - /go
3. Save changes.
4. In your Rybbit project, ensure affiliate/outbound link click tracking is enabled or create a goal that matches link clicks. If Rybbit provides a setting for "Affiliate link prefixes" or similar, add the same prefixes there. If it uses custom events, map the incoming affiliate link clicks accordingly.

Notes:
- The plugin passes your prefixes to the Rybbit script tag. Exact reporting depends on your Rybbit configuration and plan.
- Prefixes are matched against the beginning of the link pathname on your site.

== Screenshots ==

1. The plugin settings page where you can configure your Rybbit integration.

== Changelog ==

= 1.5.0 =
* Add Affiliate Link Prefixes option to help track shortened/cloaked affiliate links (e.g., /offer, /go)
* Bump plugin version

= 1.4.2 =
* Add API Version toggle (v1/v2)
* Update injection logic to support v2 (only Skip Patterns, Mask Patterns, Debounce)
* Support alphanumeric Site IDs for v2

= 1.3 =
* Add Automatically track inital pageview feature.

= 1.2 =
* Add Advanced Settings to match the Rybbit UI.

= 1.1 =
* Add URL validation to Script URL

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.3 =
* Add Automatically track inital pageview feature.

== Disclaimer ==

Rybbit is a trademark and copyright of Rybbit. This plugin is not affiliated with or endorsed by Rybbit.
