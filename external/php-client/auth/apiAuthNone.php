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
 * Do-nothing authentication implementation, use this if you want to make un-authenticated calls
 * @author Chris Chabot <chabotc@google.com>
 *
 */
class apiAuthNone extends apiAuth {

  public $developerKey = null;

  public function __construct() {
    global $apiConfig;
    if (!empty($apiConfig['developer_key'])) {
      $this->setDeveloperKey($apiConfig['developer_key']);
    }
  }

  public function authenticate(apiCache $cache, apiIO $io, $service) {
    // noop
  }

  public function setAccessToken($accessToken) {
    // noop
  }

  public function getAccessToken() {
    // noop
    return null;
  }

  /**
   * Set the developer key to use, these are obtained through the API Console
   */
  public function setDeveloperKey($developerKey) {
    $this->developerKey = $developerKey;
  }

  public function sign(apiHttpRequest $request) {
    if ($this->developerKey) {
      $request->setUrl($request->getUrl() . ((strpos($request->getUrl(), '?') === false) ? '?' : '&') . 'key='.urlencode($this->developerKey));
    }
    return $request;
  }
}
