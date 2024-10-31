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

require_once "external/OAuth.php";

/**
 * Authentication class that deals with the OAuth 2 web-server authentication flow
 *
 * @author Chris Chabot <chabotc@google.com>
 *
 */
class apiOAuth2 extends apiAuth {

  public $cacheKey;
  protected $developerKey;
  public $io;

  /**
   * Instantiates the class, but does not initiate the login flow, leaving it
   * to the discretion of the caller (which is done by calling authenticate()).
   *
   * @param apiCache $cache cache class to use (file,apc,memcache,mysql)
   */
  public function __construct() {
    global $apiConfig;
    if (! empty($apiConfig['developer_key'])) {
      $this->setDeveloperKey($apiConfig['developer_key']);
    }
    $this->ClientId = $apiConfig['oauth2_client_id'];
    $this->ClientSecret = $apiConfig['oauth2_client_secret'];
    $this->RedirectUri = $apiConfig['oauth2_redirect_uri'];
  }

  public function authenticate(apiCache $cache, apiIO $io, $service) {
    $this->io = $io;
    if (isset($_GET['code'])) {
      // We got here from the redirect from a successfull authorization grant, fetch the access token
      $request = $this->io->makeRequest(new apiHttpRequest('https://www.google.com/accounts/o8/oauth2/token', 'POST', array(), array(
          'code' => $_GET['code'],
          'grant_type' => 'authorization_code',
          'redirect_uri' => $this->RedirectUri,
          'client_id' => $this->ClientId,
          'client_secret' => $this->ClientSecret
      )));
      if ((int)$request->getResponseHttpCode() == 200) {
        $this->setAccessToken($request->getResponseBody());
        $this->accessToken['created'] = time();
        return $this->getAccessToken();
      } else {
        $response = $request->getResponseBody();
        $decodedResponse = json_decode($response, true);
        if ($decodedResponse != $response && $decodedResponse != null && $decodedResponse['error']) {
          $response = $decodedResponse['error'];
        }
        throw new apiAuthException("Error fetching OAuth2 access token, message: '" . $response . "'", $request->getResponseHttpCode());
      }
    }
    // This is the initial authenticate() call, go through the authorization flow
    $params = array(
    	'response_type=code',
    	'redirect_uri=' . urlencode($this->RedirectUri),
        'client_id=' . urlencode($this->ClientId),
        'scope=' . urlencode($service['scope'])
    );
    $params = implode('&', $params);
    $authenticationUrl = "https://www.google.com/accounts/o8/oauth2/authorization?$params";
    header('Location: ' . $authenticationUrl);
  }

  public function setAccessToken($accessToken) {
    $accessToken = json_decode($accessToken, true);
    if ($accessToken == null) {
      throw new apiAuthException("Could not json decode the access token");
    }
    if (! isset($accessToken['access_token']) || ! isset($accessToken['expires_in']) || ! isset($accessToken['refresh_token'])) {
      throw new apiAuthException("Invalid token format");
    }
    $this->accessToken = $accessToken;
  }

  public function getAccessToken() {
    return json_encode($this->accessToken);
  }

  public function setDeveloperKey($developerKey) {
    $this->developerKey = $developerKey;
  }

  public function sign(apiHttpRequest $request) {
    // add the developer key to the request before signing it
    if ($this->developerKey) {
      $request->setUrl($request->getUrl() . ((strpos($request->getUrl(), '?') === false) ? '?' : '&') . 'key=' . urlencode($this->developerKey));
    }

    if (($this->accessToken['created'] + ($this->accessToken['expires_in'] - 30)) < time()) {
      // if the token is set to expire in the next 30 seconds (or has already expired), refresh it and set the new token
      //FIXME this is mostly a copy and paste mashup from the authenticate and setAccessToken functions, should generalize them into a function instead of this mess
      $refreshRequest = $this->io->makeRequest(new apiHttpRequest('https://www.google.com/accounts/o8/oauth2/token', 'POST', array(), array(
          'client_id' => $this->ClientId,
          'client_secret' => $this->ClientSecret,
          'refresh_token' => $this->accessToken['refresh_token'],
          'grant_type' => 'refresh_token'
      )));
      if ((int)$refreshRequest->getResponseHttpCode() == 200) {
        $token = json_decode($refreshRequest->getResponseBody(), true);
        if ($token == null) {
          throw new apiAuthException("Could not json decode the access token");
        }
        if (! isset($token['access_token']) || ! isset($token['expires_in'])) {
          throw new apiAuthException("Invalid token format");
        }
        $this->accessToken['access_token'] = $token['access_token'];
        $this->accessToken['expires_in'] = $token['expires_in'];
        $this->accessToken['created'] = time();
      } else {
        $response = $refreshRequest->getResponseBody();
        $decodedResponse = json_decode($response, true);
        if ($decodedResponse != $response && $decodedResponse != null && $decodedResponse['error']) {
          $response = $decodedResponse['error'];
        }
        throw new apiAuthException("Error refreshing the OAuth2 token, message: '" . $response . "'", $refreshRequest->getResponseHttpCode());
      }
    }

    // Add the OAuth2 header to the request
    $headers = $request->getHeaders();
    $headers[] = "Authorization: OAuth " . $this->accessToken['access_token'];
    $request->setHeaders($headers);

    return $request;
  }

}
