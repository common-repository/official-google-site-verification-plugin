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

global $apiConfig;

$apiConfig = array(

    // Site name to show in the Google's oauth authentication screen
    'site_name' => 'www.example.org',

    // OAuth2 Setting, you can get these keys at https://code.google.com/apis/console
    'oauth2_client_id' => '',
    'oauth2_client_secret' => '',
    'oauth2_redirect_uri' => '',

    // The developer key, you get this from the developer console at
    'developer_key' => '',

    // If you're using the apiOAuth auth class, it will use these values for the oauth consumer key and secret.
    // See http://code.google.com/apis/accounts/docs/RegistrationForWebAppsAuto.html for info on how to obtain those
    'oauth_consumer_key'    => 'anonymous',
    'oauth_consumer_secret' => 'anonymous',

    // Which Authentication, Storage and HTTP IO classes to use.
    'authClass'    => 'apiOAuth',
    'ioClass'      => 'apiCurlIO',
	'cacheClass'   => 'apiFileCache',

    // If you want to run the test suite (by running # phpunit AllTests.php in the tests/ directory), fill in the settings below
    'oauth_test_token' => '', // the oauth access token to use (which you can get by runing authenticate() as the test user and copying the token value), ie '{"key":"foo","secret":"bar","callback_url":null}'
    'oauth_test_user' => '', // and the user ID to use, this can either be a vanity name 'testuser' or a numberic ID '123456'

    // Don't change these unless you're working against a special development or testing envirionment
    'basePath' => 'https://www.googleapis.com',

    // IO Class dependent configuration, you only have to configure the values for the class that was configured as the ioClass above
    'ioFileCache_directory'  =>
        (function_exists('sys_get_temp_dir') ?
            sys_get_temp_dir() . '/apiClient' :
        '/tmp/apiClient'),
    'ioMemCacheStorage_host' => '127.0.0.1',
    'ioMemcacheStorage_port' => '11211',

    // Definition of service specific values like scopes, oauth token url's, etc
    'services' => array(
    	'buzz' => array('scope' => 'https://www.googleapis.com/auth/buzz', 'authorization_token_url' => 'https://www.google.com/buzz/api/auth/OAuthAuthorizeToken'),
    	'latitude' => array('scope' => 'https://www.googleapis.com/auth/latitude', 'authorization_token_url' => 'https://www.google.com/latitude/apps/OAuthAuthorizeToken'),
        'moderator' => array('scope' => 'https://www.googleapis.com/auth/moderator'),
        'easyhybrid' => array('scope' => 'https://www.googleapis.com/auth/userinfo#email'),
        'siteVerification' => array('scope' => 'https://www.googleapis.com/auth/siteverification')
    )
);
