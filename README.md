# Rybbit Analytics Tracking Code for WordPress

A WordPress plugin that integrates Rybbit tracking code into your WordPress site.

## Description

This plugin allows you to easily add Rybbit tracking code to your WordPress site by configuring the script URL and your site ID through the WordPress admin interface.

## Installation

1. Download the latest release zip file from the [Releases page](https://github.com/didyouexpectthat/rybbit-analytics-tracking-code/releases)
2. In your WordPress admin, go to Plugins > Add New > Upload Plugin
3. Upload the zip file and activate the plugin

## Configuration

1. After activation, go to Settings > Rybbit
2. Enter your Rybbit Script URL (default: https://tracking.example.com/api/script.js)
3. Enter your Rybbit Site ID
4. Choose the API Version:
   - v1 (legacy): keeps all existing options and data attributes
   - v2 (current): only Skip Patterns, Mask Patterns, and Debounce are included in the script tag
5. Optionally, modify advanced settings (Skip/Mask Patterns, Debounce, Affiliate Link Prefixes).
6. Save changes
   
![image](https://github.com/user-attachments/assets/c049e1e0-e999-456d-80ee-d1acbab703c1)

The tracking code will be automatically added to your site's header once you've configured your Site ID.

Notes about API versions:
- v1 uses numeric Site IDs; v2 supports alphanumeric Site IDs. The plugin accepts both.
- When v2 is selected, only the following data attributes are injected: data-skip-patterns, data-mask-patterns, data-debounce, and data-affiliate-prefixes (if configured), along with src and data-site-id.

## Track shortened/cloaked affiliate links

If you use pretty links like:

- https://yourdomain.com/offer/
- https://yourdomain.com/go/

You can help Rybbit track these clicks:

1. In WordPress, go to Settings > Rybbit > Advanced Settings.
2. In "Affiliate Link Prefixes", enter the path prefixes you use, one per line or comma-separated (e.g., /offer, /go).
3. Save changes.
4. In your Rybbit project, enable affiliate/outbound link click tracking or configure a goal to count these clicks. If Rybbit provides a setting for affiliate link prefixes, add the same prefixes there so reports align.

Notes:
- The plugin passes your prefixes to the Rybbit script tag as data-affiliate-prefixes. Exact reporting behavior depends on your Rybbit configuration.

## License

This project is licensed under the GPLv2 License - see the [LICENSE](LICENSE) file for details.

## Disclaimer

Rybbit is a trademark and copyright of Rybbit. This plugin is not affiliated with or endorsed by Rybbit.
