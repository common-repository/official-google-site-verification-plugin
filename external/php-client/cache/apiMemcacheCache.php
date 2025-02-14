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
 * A persistent storage class based on the memcache, which is not
 * really very persistent, as soon as you restart your memcache daemon
 * the storage will be wiped, however for debugging and/or speed
 * it can be useful, kinda, and cache is a lot cheaper then storage.
 *
 * @author Chris Chabot <chabotc@google.com>
 */
class apiMemcacheCache extends apiCache {
  private $connection = false;

  public function __construct() {
    global $apiConfig;
    if (! function_exists('memcache_connect')) {
      throw new apiCacheException("Memcache functions not available");
    }
    $this->host = $apiConfig['ioMemCacheCache_host'];
    $this->port = $apiConfig['ioMemCacheCache_port'];
    if (is_empty($this->host) || is_empty($this->port)) {
      throw new apiCacheException("You need to supply a valid memcache host and port");
    }
  }

  private function isLocked($key) {
    $this->check();
    if ((@memcache_get($this->connection, $key . '.lock')) === false) {
      return false;
    }
    return true;
  }

  private function createLock($key) {
    $this->check();
    // the interesting thing is that this could fail if the lock was created in the meantime..
    // but we'll ignore that out of convenience
    @memcache_add($this->connection, $key . '.lock', '', 0, 5);
  }

  private function removeLock($key) {
    $this->check();
    // suppress all warnings, if some other process removed it that's ok too
    @memcache_delete($this->connection, $key . '.lock');
  }

  private function waitForLock($key) {
    $this->check();
    // 20 x 250 = 5 seconds
    $tries = 20;
    $cnt = 0;
    do {
      // 250 ms is a long time to sleep, but it does stop the server from burning all resources on polling locks..
      usleep(250);
      $cnt ++;
    } while ($cnt <= $tries && $this->isLocked());
    if ($this->isLocked()) {
      // 5 seconds passed, assume the owning process died off and remove it
      $this->removeLock($key);
    }
  }

  // I prefer lazy initalization since the cache isn't used every request
  // so this potentially saves a lot of overhead
  private function connect() {
    if (! $this->connection = @memcache_pconnect($this->host, $this->port)) {
      throw new apiCacheException("Couldn't connect to memcache server");
    }
  }

  private function check() {
    if (! $this->connection) {
      $this->connect();
    }
  }

  /**
   * @inheritDoc
   */
  public function get($key, $expiration = false) {
    $this->check();
    if (($ret = @memcache_get($this->connection, $key)) === false) {
      return false;
    }
    if (! $expiration || (time() - $ret['time'] > $expiration)) {
      $this->delete($key);
      return false;
    }
    return $ret['data'];
  }

  /**
   * @inheritDoc
   */
  public function set($key, $value) {
    $this->check();
    // we store it with the cache_time default expiration so objects will atleast get cleaned eventually.
    if (@memcache_set($this->connection, $key, array('time' => time(),
        'data' => $value), false) == false) {
      throw new apiCacheException("Couldn't store data in cache");
    }
  }

  /**
   * @inheritDoc
   */
  public function delete($key) {
    $this->check();
    @memcache_delete($this->connection, $key);
  }
}
