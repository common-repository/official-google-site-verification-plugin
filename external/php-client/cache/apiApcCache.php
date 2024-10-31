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
 * A persistent storage class based on the APC cache, which is not
 * really very persistent, as soon as you restart your web server
 * the storage will be wiped, however for debugging and/or speed
 * it can be useful, kinda, and cache is a lot cheaper then storage.
 *
 * @author Chris Chabot <chabotc@google.com>
 */
class apiApcCache extends apiCache {

  public function __construct() {
    if (! function_exists('apc_add')) {
      throw new apiCacheException("Apc functions not available");
    }
  }

  private function isLocked($key) {
    if ((@apc_fetch($key . '.lock')) === false) {
      return false;
    }
    return true;
  }

  private function createLock($key) {
    // the interesting thing is that this could fail if the lock was created in the meantime..
    // but we'll ignore that out of convenience
    @apc_add($key . '.lock', '', 5);
  }

  private function removeLock($key) {
    // suppress all warnings, if some other process removed it that's ok too
    @apc_delete($key . '.lock');
  }

  private function waitForLock($key) {
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

   /**
   * @inheritDoc
   */
  public function get($key, $expiration = false) {

    if (($ret = @apc_fetch($key)) === false) {
      return false;
    }
    if (!$expiration || (time() - $ret['time'] > $expiration)) {
      $this->delete($key);
      return false;
    }
    return unserialize($ret['data']);
  }

  /**
   * @inheritDoc
   */
  public function set($key, $value) {
    if (@apc_store($key, array('time' => time(), 'data' => serialize($value))) == false) {
      throw new apiCacheException("Couldn't store data");
    }
  }

  /**
   * @inheritDoc
   */
  public function delete($key) {
    @apc_delete($key);
  }
}
