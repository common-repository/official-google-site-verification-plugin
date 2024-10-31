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

require_once "apiFileCache.php";
require_once "apiApcCache.php";
require_once "apiMemcacheCache.php";

/**
 * Abstract storage class
 *
 * @author Chris Chabot <chabotc@google.com>
 */
abstract class apiCache {

  /**
   * Retrieves the data for the given key, or false if they
   * key is unknown or expired
   *
   * @param String $key The key who's data to retrieve
   * @param int $expiration Experiration time in seconds
   */
  abstract function get($key, $expiration = false);

  /**
   * Store the key => $value set. The $value is serialized
   * by this function so can be of any type
   *
   * @param String $key Key of the data
   * @param Any-type $value the data
   */
  abstract function set($key, $value);

  /**
   * Removes the key/data pair for the given $key
   *
   * @param String $key
   */
  abstract function delete($key);
}


