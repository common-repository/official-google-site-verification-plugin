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

require_once "io/apiHttpRequest.php";
require_once "io/apiCurlIO.php";
require_once "io/apiREST.php";
require_once "io/apiRPC.php";

/**
 * Abstract IO class
 *
 * @author Chris Chabot <chabotc@google.com>
 */
interface apiIO {

  /**
   * Called by the apiClient
   * @param $storage
   * @param $auth
   */
  public function __construct(apiCache $storage, apiAuth $auth);

  /**
   * An utility function that first calls $this->auth->sign($request) and then executes makeRequest()
   * on that signed request. Used for when a request should be authenticated
   * @param apiHttpRequest $request
   * @return apiHttpRequest $request
   */
  public function authenticatedRequest(apiHttpRequest $request);

  /**
   * Executes a apIHttpRequest and returns the resulting populated httpRequest
   * @param apiHttpRequest $request
   * @return apiHttpRequest $request
   */
  public function makeRequest(apiHttpRequest $request);

}
