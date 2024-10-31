<?php
/**
 * This file is part of the Google Verification Wordpress Plugin.
 *
 * The Google Verification Wordpress Plugin is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation,
 * either version 2 of the License, or (at your option) any later version.

 * The Google Site Verification Wordpress Plugin is distributed in the hope
 * that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with the Google Site Verification Wordpress Plugin.
 * If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package Google_Site_Verification
 * @version 0.9.2
 */
/*
Plugin Name: Google Site Verification Plugin
Plugin URI: http://wordpress.org/extend/plugins/official-google-site-verification-plugin
Description: One-click Google verification for your website!
Author: Google
Version: 0.9.2
Author URI: https://www.google.com/webmasters/verification/home
*/

// Include Google API client library and verification service library.
require_once(dirname(__FILE__) . "/external/php-client/apiClient.php");
require_once("apiSiteVerificationService.php");

// Add Wordpress hooks
add_action('admin_menu', 'google_add_verify_page');
add_action('wp_head', 'google_add_meta_tag');
add_action('admin_init', 'google_admin_head');

global $api_key, $auth_token, $apiClient;
define("GOOGLE_META_OPTION_NAME", "google_verify_meta");

/**
 * Requests token from verification service, returns token as string.
 * @param service Service client object to get tokens from.
 * @return Verification token.
 */
function google_get_meta_token($service) {
  $url = get_home_url();
  $token = $service->getTokenWebResource($url, "SITE", "META");
  return $token["token"];
}

/**
 * Verifies a site (REST INSERT). Called after the meta token is put in place.
 * @param service Service client object.
 */
function google_execute_verify($service) {
  $url = get_site_url();
  // Ignore the output from a successful insert.
  // Verification failures will result in a thrown exception.
  $service->insertWebResource(
      array("site" => array("identifier" => $url, "type" => "SITE")),
      "META");
}

/**
 * Creates and configures verification API client objects in global scope.
 */
function google_create_verification_client() {
  global $apiClient, $verification, $api_key;
  $apiClient = new apiClient();
  $verification = new apiSiteVerificationService($apiClient);
}

/**
 * Checks if the user has requested verification by inspecting the GET
 * parameters. Called early in the request lifecycle so that the HTTP headers
 * are still mutable (required for redirection.)
 */
function google_admin_head() {
  global $auth_token, $verification, $apiClient;
  if (isset($_GET['verify'])) {
    // User clicked Verify - start the OAuth dance.
    $auth_token = $apiClient->authenticate();
  }
}

/**
 * Adds Google Verification choice to Settings menu.
 */
function google_add_verify_page() {
  add_options_page('Google Site Verification', 'Google Verification', 8, 'googleverification', 'google_verify_options');
  $plugin = plugin_basename(__FILE__);
  add_filter('plugin_action_links_' . $plugin, 'google_plugin_actions');
}

/**
 * Adds Verify link to plugin actions.
 */
function google_plugin_actions($links) {
  array_unshift($links, '<a href="' .
      admin_url( 'options-general.php?page=googleverification' ) .
      '">Verify</a>');
  return $links;
}

/**
 * Adds the meta verification token, if it exists, to the blog header.
 */
function google_add_meta_tag() {
  echo get_option(constant("GOOGLE_META_OPTION_NAME"));
}

/**
 * Executes sequence of calls required to perform verification:
 * 1. Get token from verification API.
 * 2. Place token inside blog HEAD tag.
 * 3. Call Insert method using verification API.
 */
function google_verify_site()
{
  global $auth_token, $apiClient, $verification;
  $apiClient->setAccessToken($auth_token);
    $token = google_get_meta_token($verification);
    update_option(constant("GOOGLE_META_OPTION_NAME"),
                  google_get_meta_token($verification));
    google_execute_verify($verification);
}

/**
 * Outputs the verification button.
 */
function google_output_verify_button($title) {
  echo '<form method="post" action="'  . $PHP_SELF . '?page=' .
    $_GET['page'] . '&verify=verify"><input type="hidden" name="page"' .
    'value="' . $_GET['page'] . '"/>' .
    '<input type="submit" name="verify" value="' . $title .
    '" class="button-primary" /></form>';
}

function output_image($name) {
  echo '<img src="' . WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)) .
      '/image/' . $name . '"/>';
}

function google_verify_options()
{
  global $auth_token, $apiClient, $verification;

  echo '<h3 class="title">Google Site Verification</h3>';

  if (!isset($auth_token) || !isset($apiClient)) {
    echo '<p>You\'re just a few simple clicks away from verifying this blog!</p><p>';
    output_image('alert.gif');
    echo '&nbsp; <em>Note:</em> If you have any caching plugins installed, you may want to disable them until verification is complete.</p><br/>';
    google_output_verify_button('Start Verification');
  } else {
    echo '<h4>Verifying your blog...</h4>';
    try {
      google_verify_site();
      echo '<h4>';
      output_image('ok.gif');
      echo '&nbsp;Congratulations! Your blog is now verified!</h4>' .
          '<p>You can now use verified Google services like ' .
          '<a href="http://www.google.com/webmasters/">Webmaster Tools</a>. ' .
          '(<a href="http://www.google.com/support/webmasters/bin/answer.py?answer=1144253">See a complete list of services</a>)</p>' .
          '<p>You can see your portfolio of verified sites by visiting ' .
          '<a href="http://www.google.com/webmasters/verification/">' .
           'Google Site Verification</a>.</p>' .
           '<p>If you had disabled any caching plugins for verification, those plugins can now be re-enabled.</p>';
    } catch (apiServiceException $ase) {
      output_image('alert.gif');
      echo '&nbsp;Oops, something went wrong during verification.</h4>' .
          '<p class="error">Error details:<br/><tt>' .
  	  htmlspecialchars($ase->getMessage()) . '</tt></p><br/>';
      google_output_verify_button('Try Again');
    }
  }
}

google_create_verification_client();
