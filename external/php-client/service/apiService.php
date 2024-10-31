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

require_once "service/apiServiceResource.php";
require_once "service/apiServiceRequest.php";
require_once "service/apiBatch.php";

/**
 * This class parses the service end points of the api discovery document and constructs
 * serviceResource variables for all of them.
 *
 * For instance when calling with the service document for Buzz, it will create apiServiceResource's
 * for $this->activities, $this->photos, $this->people, $this->search, etc
 *
 * @author Chris Chabot <chabotc@google.com>
 *
 */
class apiService {

  protected $io;
  protected $version = null;
  protected $restBasePath;
  protected $rpcPath;

  public function __construct($serviceName, $discoveryDocument, apiIO $io) {
    global $apiConfig;
    $this->io = $io;
    if (!isset($discoveryDocument['version']) || !isset($discoveryDocument['restBasePath']) || !isset($discoveryDocument['rpcPath'])) {
      throw new apiServiceException("Invalid discovery document");
    }
    $this->version = $discoveryDocument['version'];
    $this->restBasePath = $apiConfig['basePath'] . $discoveryDocument['restBasePath'];
    $this->rpcPath = $apiConfig['basePath'] . $discoveryDocument['rpcPath'];
    foreach ($discoveryDocument['resources'] as $resourceName => $resourceTypes) {
      $this->$resourceName = new apiServiceResource($this, $serviceName, $resourceName, $resourceTypes);
    }
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
   * @return the $version
   */
  public function getVersion() {
    return $this->version;
  }

  /**
   * @return the $restBasePath
   */
  public function getRestBasePath() {
    return $this->restBasePath;
  }

  /**
   * @return the $rpcPath
   */
  public function getRpcPath() {
    return $this->rpcPath;
  }

  /**
   * @param $version the $version to set
   */
  public function setVersion($version) {
    $this->version = $version;
  }

  /**
   * @param $restBasePath the $restBasePath to set
   */
  public function setRestBasePath($restBasePath) {
    $this->restBasePath = $restBasePath;
  }

  /**
   * @param $rpcPath the $rpcPath to set
   */
  public function setRpcPath($rpcPath) {
    $this->rpcPath = $rpcPath;
  }
}
