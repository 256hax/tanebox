=== Custom Post Types UI Extended ===
Contributors:      WebDevStudios
Tags: custom post types
Requires at least: 3.9
Tested up to:      4.9.5
Stable tag:        1.5.2
License:           GPL-2.0+
License URI:       http://www.gnu.org/licenses/gpl-2.0.html


== Description ==

Custom Post Type UI Extended provides add-on functionality to the widely popular Custom Post Type UI plugin.

Current features include a shortcode builder to make it simple to display data from your custom post types in a page or post, multisite support to allow for easy creation of network wide post types and taxonomies.

== Installation ==

= Manual Installation =

1. Upload the CPTUI-Extended folder to the plugins directory in your WordPress installation
2. Activate the plugin.
3. Navigate to the "CPTUI Extended" Menu.

Upload Zip File

1. Navigate to Plugins -> Add New.
2. Click the "Upload Plugin" button.
3. Select the "cptui-extended.zip" file from your computer.
4. Click "Install Now".
5. Click the "Activate Plugin" link.
6. Navigate to the "CPTUI Extended" Menu.


== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

## 1.5.2 - TBD
* Added: Filter to remove taxonomies from consideration for modal popup. Example: Yoast SEO Prominent keywords
* Fixed: Some layouts were not having custom amount values respected and applied.
* Fixed: Addressed PHP errors for 5.3 and lower, coming from new 1.5.0 layouts.
* Fixed: Fixed issue with "Grid with Overlay" layout not respecting custom post type selection.
* Updated: Changed where to apply Pluginize license. License page no longer in Network admin for Multisite users. Please apply to just main site.
* Updated: Added note about AMP 0.6.0+ and AMP's dedicated options page.

## 1.5.1 - 2018-02-27
* Fixed: Resolved PHP error occurring for customers on older PHP versions.

## 1.5.0 - 2018-02-26
* Added: Allow filtering by taxonomy terms for the list template.
* Added: Checkbox setting support for adding post types to the primary RSS feed.
* Added: New display formats: Grid, Grid with Overlay, Post Cards, Featured Plus.
* Added: Include shortcode ID as available html class attribute in frontend output.
* Fixed: Display "post", "page", "attachment" WP core post types as available options for taxonomy editor screen in network admin.
* Fixed: Import issues going to network-wide settings when importing settings in single site.
* Fixed: Ensured compatibility with WooCommerce 3.x.
* Fixed: Issue with individual site imports going to network admin when they should not.
* Updated: Reworded "Shortcode to Embed" to "Choose a Layout".
* Updated: Clarified "Taxonomy" section of shortcode builder modal.
* Updated: CMB2 library to 2.3.0 for purposes of PHP 7.2.x compatibility.
* Updated: TGM-Plugin-Activation library.

## 1.4.4 - 2017-11-15
* Fixed: Conflict with with checkboxes and radio buttons on post editor screen getting unset when interacting with shortcode builder.
* Fixed: Re-added "amount" field to the default shortcode option, used to specify how many posts to show.

## 1.4.3 - 2017-3-20
* Fixed: Issue involving arrays and objects for error template when no template found.
* Fixed: Adjusted styles to expand CPTUI areas to 100% when no mini-sidebar displayed.

## 1.4.2 - 2017-02-23
* Fixed: Scroll issues when CPTUI-Extend shortcode builder is toggled from an already open modal toggle.

## 1.4.1 - 2017-02-02
* Fixed: Add missed code, required for network import/export to work. Sorry about that everyone.

## 1.4.0 - 2017-01-12
* Fixed: Potential broken HTML in list template.
* Updated: More WordPress hook inline documentation.
* Added: Convenience function for changing tax query relations.
* Added: Theme developers! You can now override CPTUI-Extended shortcode templates from your active theme. See Pluginize.com for more details.
* Added: Customize font colors, font sizes, and margins around shortcodes via Customizer controls.
* Added: Network-wide import/export support.
* Added: Ability to declare support for Divi Builder.

## 1.3.4 - 2016-12-07
* Fixed: Compatibility issue related to ksort and action hooks, with WordPress 4.7.0.

## 1.3.3 - 2016-11-29
* Updated: Moved from WooCommerce plugin updating to EasyDigitalDownloads.

## 1.3.2 - 2016-11-14
* Fixed: Fix issue with term name display in taxonomy list template. Term slug was showing instead, due to change in 1.3.0 release.

## 1.3.1 - 2016-09-28
* Fixed issue with enqueued JS that prevents metabox toggling in post editor.

## 1.3.0 - 2016-09-20
* Added: Options to add post type to WordPress category and tag archives.
* Added: Google AMP support via https://wordpress.org/plugins/amp/
* Added: New template for single custom post types.
* Added: Filter for the featured image size parameters in all shortcode templates.
* Added: Post IDs to template filters for more fine-tuned control of content.
* Fixed: Removed need for extra page refresh for network post types/taxonomies to appear.
* Fixed: Prevent possible empty term links.
* Fixed: Issues surrounding javascript and stylesheet paths on IIS hosting.
* Fixed: Proper use of term slug for shortcode attributes instead of term label.
* Fixed: More admin notices are dismissable.
* Removed: Plugin shop section from Dashboard widget.
* Updated: Latest version of CMB2 library.
* Updated: Moved from PHP's `rand()` function to `mt_rand()`.

== Upgrade Notice ==
