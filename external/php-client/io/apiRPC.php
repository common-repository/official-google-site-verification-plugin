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
 * This class implements the experimental JSON-RPC transport for executing apiServiceRequest'
 *
 * @author Chris Chabot <chabotc@google.com>
 */
class apiRPC {

  static public function execute($requests) {
    $jsonRpcRequest = array();
    foreach ($requests as $request) {
      $parameters = array();
      foreach ($request->getParameters() as $parameterName => $parameterVal) {
        $parameters[$parameterName] = $parameterVal['value'];
      }
      $jsonRpcRequest[] = array(
        'id' => $request->getBatchKey(),
        'method' => str_replace('buzz.', 'chili.', $request->getRpcName()),
        'params' => $parameters,
      	'apiVersion' => 'v1'
      );
    }
    $httpRequest = new apiHttpRequest($request->getRpcPath() . '?pp=1');
    $httpRequest->setHeaders(array('Content-Type: application/json'));
    $httpRequest->setMethod('POST');
    $httpRequest->setPostBody(json_encode($jsonRpcRequest));
    $httpRequest = $request->getIo()->authenticatedRequest($httpRequest);
    if (($decodedResponse = json_decode($httpRequest->getResponseBody(), true)) != false) {
      $ret = array();
      foreach ($decodedResponse as $response) {
        $ret[$response['id']] = self::checkNextLink($response['result']);
      }
      return $ret;
    } else {
      throw new apiServiceException("Invalid json returned by the json-rpc end-point");
    }
  }

  static private function checkNextLink($response) {
    if (isset($response['links']) && isset($response['links']['next'][0]['href'])) {
      parse_str($response['links']['next'][0]['href'], $params);
      if (isset($params['c'])) {
        $response['continuationToken'] = $params['c'];
      }
    }
    return $response;
  }
}
