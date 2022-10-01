<?php
/**
 * @version 2.0
 * @author Sammy
 *
 * @keywords Samils, ils, php framework
 * -----------------
 * @package Sammy\Packs\Samils\Capsule
 * - Autoload, application dependencies
 *
 * MIT License
 *
 * Copyright (c) 2020 Ysare
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace Sammy\Packs\Samils\Capsule {
  use Clinter\Console;
  use App\View\Capsule;
  use Sammy\Packs\XSami;
  /**
   * Make sure the module base internal class is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!class_exists ('Sammy\Packs\Samils\Capsule\Watcher')) {
  /**
   * @class Watcher
   * Base internal class for the
   * Samils\Capsule module.
   * -
   * This is (in the ils environment)
   * an instance of the php module,
   * wich should contain the module
   * core functionalities that should
   * be extended.
   * -
   * For extending the module, just create
   * an 'exts' directory in the module directory
   * and boot it by using the ils directory boot.
   * -
   */
  class Watcher {
    /**
     * @var array $cacheFiles
     */
    private static $cacheFiles = [];

    /**
     * @method void invoke
     */
    public function __invoke (XSami $file) {
      $phpCacheFilePath = $file->path;

      $phpCacheFileExtension = pathinfo ($phpCacheFilePath, PATHINFO_EXTENSION);

      if (in_array ($phpCacheFileExtension, ['cap'])) {
        return;
      }

      $cacheFilePathFromViewsDir = join (DIRECTORY_SEPARATOR, [
        Capsule::ViewsPath (),
        Capsule::StripViewsPath ($phpCacheFilePath)
      ]);

      $re = '/(\.cache\.php)$/';

      if (!(Capsule::PathIsInViewsDir ($phpCacheFilePath) && preg_match ($re, $phpCacheFilePath))) {
        return;
      }

      $cacheFilePathFromViewsDir = preg_replace ($re, '', $cacheFilePathFromViewsDir);

      $filePathFromViewsDirArr = [$cacheFilePathFromViewsDir];

      $phpCacheFileExtension = pathinfo ($cacheFilePathFromViewsDir, PATHINFO_EXTENSION);

      if (!in_array ($phpCacheFileExtension, ['css'])) {
        array_push ($filePathFromViewsDirArr, 'cap');
      }

      $filePathFromViewsDir = join ('.', $filePathFromViewsDirArr);

      if (!isset (self::$cacheFiles [$phpCacheFilePath])) {
        self::$cacheFiles [$phpCacheFilePath] = $filePathFromViewsDir;
      }

      foreach (self::$cacheFiles as $cacheFilePath => $componentFilePath) {
        if (!is_file ($componentFilePath) && is_file ($cacheFilePath)) {
          @unlink ($cacheFilePath);

          Console::error ("\nDeleted: {$cacheFilePath}\n");

          $cacheFileDirPath = dirname ($cacheFilePath);

          unset (self::$cacheFiles [$cacheFilePath]);
        }
      }
    }

    protected static function readDir ($dir) {
      $files = [];

      if (is_dir ($dir)) {
        if ($dh = opendir ($dir)) {
          while (($file = readdir ($dh)) !== false) {
            if (!in_array ($file, ['.', '..'])) {
              array_push ($files, realpath ($dir . '/' . $file));
            }
          }

          closedir ($dh);
        }
      }

      return $files;
    }
  }}
}
