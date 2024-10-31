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
 * Internal representation of a Google API request, used by the apiServiceResource class to
 * construct API function calls and passing them to the IO layer who knows how to execute
 * the request
 *
 * @author Chris Chabot <chabotc@google.com>
 *
 */
class apiServiceRequest {

  protected $io;
  protected $restBasePath;
  protected $restPath;
  protected $rpcPath;
  protected $rpcName;
  protected $httpMethod;
  protected $parameters;
  protected $postBody;
  protected $batchKey;

  /**
   * Only used internally, so using a quick-and-dirty constuctor
   */
  public function __construct(apiIO $io, $restBasePath, $rpcPath, $restPath, $rpcName, $httpMethod, $parameters, $postBody = null) {
    global $apiConfig;
    $this->io = $io;
    $this->restBasePath = $apiConfig['basePath'] . $restBasePath;
    $this->restPath = $restPath;
    $this->rpcPath = $rpcPath;
    $this->rpcName = $rpcName;
    $this->httpMethod = $httpMethod;
    $this->parameters = $parameters;
    $this->postBody = $postBody;
  }

  /**
   * @return the $postBody
   */
  public function getPostBody() {
    return $this->postBody;
  }

  /**
   * @param $postBody the $postBody to set
   */
  public function setPostBody($postBody) {
    $this->postBody = $postBody;
  }

  /**
   * @return the $io
   */
  public function getIo() {
    return $this->io;
  }

  /**
   * @param $io the $io to set
   */
  public function setIo($io) {
    $this->io = $io;
  }

  /**
   * @param $baseUrl the $baseUrl to set
   */
  public function setBaseUrl($baseUrl) {
    $this->baseUrl = $baseUrl;
  }

  /**
   * @return the $restBasePath
   */
  public function getRestBasePath() {
    return $this->restBasePath;
  }

  /**
   * @return the restPath
   */
  public function getRestPath() {
    return $this->restPath;
  }

  /**
   * @return the $rpcPath
   */
  public function getRpcPath() {
    return $this->rpcPath;
  }

  /**
   * @return the $rpcName
   */
  public function getRpcName() {
    return $this->rpcName;
  }

  /**
   * @return the $httpMethod
   */
  public function getHttpMethod() {
    return $this->httpMethod;
  }

  /**
   * @return the $parameters
   */
  public function getParameters() {
    return $this->parameters;
  }

  /**
   * @param $restBasePath the $restBasePath to set
   */
  public function setRestBasePath($restBasePath) {
    $this->restBasePath = $restBasePath;
  }

  /**
   * @param $restPath the $restPath to set
   */
  public function setRestPath($restPath) {
    $this->restPath = $restPath;
  }

  /**
   * @param $rpcPath the $rpcPath to set
   */
  public function setRpcPath($rpcPath) {
    $this->rpcPath = $rpcPath;
  }

  /**
   * @param $rpcName the $rpcName to set
   */
  public function setRpcName($rpcName) {
    $this->rpcName = $rpcName;
  }

  /**
   * @param $httpMethod the $httpMethod to set
   */
  public function setHttpMethod($httpMethod) {
    $this->httpMethod = $httpMethod;
  }

  /**
   * @param $parameters the $parameters to set
   */
  public function setParameters($parameters) {
    $this->parameters = $parameters;
  }

  /**
   * @return the $batchKey
   */
  public function getBatchKey() {
    return $this->batchKey;
  }

  /**
   * @param $batchKey the $batchKey to set
   */
  public function setBatchKey($batchKey) {
    $this->batchKey = $batchKey;
  }

}
