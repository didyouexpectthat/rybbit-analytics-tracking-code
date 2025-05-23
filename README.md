# Rybbit Plugin for WordPress

A WordPress plugin that integrates Rybbit tracking code into your WordPress site.

## Description

This plugin allows you to easily add Rybbit tracking code to your WordPress site by configuring the script URL and your site ID through the WordPress admin interface.

## Installation

1. Download the latest release zip file from the [Releases page](https://github.com/didyouexpectthat/rybbit-wordpress-plugin/releases)
2. In your WordPress admin, go to Plugins > Add New > Upload Plugin
3. Upload the zip file and activate the plugin

## Configuration

1. After activation, go to Settings > Rybbit
2. Enter your Rybbit Script URL (default: https://tracking.example.com/api/script.js)
3. Enter your Rybbit Site ID
4. Save changes

The tracking code will be automatically added to your site's header once you've configured your Site ID.

## Development

### Release Process

This plugin uses GitHub Actions to automatically create releases when a new version tag is pushed. The workflow:

1. Creates a zip file of the plugin
2. Creates a GitHub release
3. Uploads the zip file as an asset to the release

To create a new release:

1. Update the version number in `rybbit-plugin.php` (both in the plugin header and the `RYBBIT_PLUGIN_VERSION` constant)
2. Commit your changes
3. Create and push a new tag with the version number prefixed with 'v' (e.g., `v1.1`)
   ```
   git tag v1.1
   git push origin v1.1
   ```
4. GitHub Actions will automatically build the plugin zip and create a release

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Disclaimer

Rybbit is a trademark and copyright of Rybbit. This plugin is not affiliated with or endorsed by Rybbit.
