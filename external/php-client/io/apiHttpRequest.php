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
 * HTTP Request used to execute http requests using the apiIO classes. On execution the
 * responseHttpCode, responseHeaders and responseBody will be filled in.
 *
 * @author Chris Chabot <chabotc@google.com>
 *
 */
class apiHttpRequest {

  protected $url;
  protected $method;
  protected $headers;
  protected $postBody;

  protected $responseHttpCode;
  protected $responseHeaders;
  protected $responseBody;

  public function __construct($url, $method = 'GET', $headers = array(), $postBody = null) {
    $this->url = $url;
    // force the method name to always be upper case so we can do sane comparisons on it
    $this->method = strtoupper($method);
    $this->headers = $headers;
    $this->postBody = $postBody;
  }

  /**
   * Misc function that returns the base url component of the $url
   * used by the OAuth signing class to calculate the base string
   */
  public function getBaseUrl() {
    if ($pos = strpos($this->url, '?')) {
      return substr($this->url, 0, $pos);
    }
    return $this->url;
  }

  /**
   * Misc function that returns a hash array of the query parameters of the current url
   * used by the OAuth signing class to calculate the signature
   */
  public function getQueryParams() {
    if ($pos = strpos($this->url, '?')) {
      $queryStr = substr($this->url, $pos + 1);
      $params = array();
      parse_str($queryStr, $params);
      return $params;
    }
    return array();
  }

  /**
   * @return the $responseHttpCode
   */
  public function getResponseHttpCode() {
    return $this->responseHttpCode;
  }

  /**
   * @param $responseHttpCode the $responseHttpCode to set
   */
  public function setResponseHttpCode($responseHttpCode) {
    $this->responseHttpCode = $responseHttpCode;
  }

  /**
   * @return the $responseHeaders
   */
  public function getResponseHeaders() {
    return $this->responseHeaders;
  }

  /**
   * @return the $responseBody
   */
  public function getResponseBody() {
    return $this->responseBody;
  }

  /**
   * @param $responseHeaders the $responseHeaders to set
   */
  public function setResponseHeaders($responseHeaders) {
    $this->responseHeaders = $responseHeaders;
  }

  /**
   * @param $responseBody the $responseBody to set
   */
  public function setResponseBody($responseBody) {
    $this->responseBody = $responseBody;
  }

  /**
   * @return the $url
   */

  public function getUrl() {
    return $this->url;
  }

  /**
   * @return the $method
   */
  public function getMethod() {
    return $this->method;
  }

  /**
   * @return the $headers
   */
  public function getHeaders() {
    return $this->headers;
  }

  /**
   * @return the $postBody
   */
  public function getPostBody() {
    return $this->postBody;
  }

  /**
   * @param string $url the url to set
   */
  public function setUrl($url) {
    $this->url = $url;
  }

  /**
   * @param string $method the method to set
   */
  public function setMethod($method) {
    $this->method = $method;
  }

  /**
   * @param array $headers the headers to set
   */
  public function setHeaders($headers) {
    $this->headers = $headers;
  }

  /**
   * @param string $postBody the postBody to set
   */
  public function setPostBody($postBody) {
    $this->postBody = $postBody;
  }

}
