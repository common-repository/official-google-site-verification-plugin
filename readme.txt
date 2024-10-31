=== Plugin Name ===
Contributors: kbm
Tags: google, verification, api, webmaster tools
Requires at least: 3.0
Tested up to: 3.1.1
Stable tag: trunk

Verify your blog with Google automatically and in seconds with this plugin!

== Description ==

Verify your blog with Google with only a couple clicks! No need to copy and paste tokens anymore; this plugin makes calls to the Google Site Verification API to automate the entire verification process.

Once your blog is verified, you can then view search statistics and diagnostic information for your blog in Google Webmaster Tools or use a [growing number of other Google services](http://www.google.com/support/webmasters/bin/answer.py?answer=1144253 "List of verification-enabled Google services").

**This plugin needs WordPress 3.0 (or higher), PHP 5.2.0 (or higher), the JSON PHP extensions, and the CURL PHP extensions. If you are using caching plugins like WP-Cache or WP Super Cache, you may need to temporarily clear or disable your cache for this plugin to work.**


If you are experiencing issues while using this plugin, we would appreciate your assistance by posting to the [support forum](http://wordpress.org/tags/official-google-site-verification-plugin "Support forum link"). Please include the version numbers of PHP and WordPress, and any relevant error messages you might find on your browser or server log files.

Caching plugins for Wordpress may interfere with the proper working order of this blog. If you are experiencing issues with verification, please include the names of the caching systems with your report.

== Installation ==
1. Activate the Google Verification plugin on the Plugins control panel.
1. Navigate to Google Verification under Settings on the left sidebar.
1. Click "Start Verification".
1. Follow the authentication prompts. Click "Grant access" when you are prompted to give your blog access to the Google Site Verification API.
1. Congratulations, your blog is now verified!

== Frequently Asked Questions ==

= I'm getting an error that says "the necessary verification token could not be found on your site." =
If you are using a caching plugin, you might want to flushing the cache or temporarily disabling the plugin. This plugin works by placing a special tag in your blog's <head> tag, and requesting Google's servers to inspect the head tag. This process won't work if Google's fetching system is served a stale cached page.

Check if your theme's header.php file contains a call to wp_head(). This plugin won't work if the wp_head() hook isn't called.

= I'm not able to meet the platform requirements, or the plugin just won't work for me. =
For whatever reason, your use of the plugin might be blocked -- perhaps your system administrator won't upgrade your version of PHP, or enable CURL, for example -- but don't fret! You can still verify your site the traditional way using the [Site Verification web interface](http://www.google.com/webmasters/verification "Link to Google Site Verification service"). The process isn't automatic as it is with the plugin, but once your site is verified, the end results are identical.

== Changelog ==
= 0.9.2 =
Version-specific workaround for PHP 5.2.0's lack of the sys_get_temp_dir() function.
"Verify" link added to plugin action list.

= 0.9.1 =
Some identifiers changed to avoid namespace collisions with other popular Wordpress plugins.
Fixes for nonstandard temp directory locations. Additional troubleshooting information added to readme.txt.

= 0.9 =
* Initial version of the verification plugin.
