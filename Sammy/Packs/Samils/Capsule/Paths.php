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
  /**
   * Make sure the module base internal trait is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!trait_exists ('Sammy\Packs\Samils\Capsule\Paths')) {
  /**
   * @trait Paths
   * Base internal trait for the
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
  trait Paths {
    public static function ViewsPath () {
      return __views__;
    }

    protected static function matchPathAlternates ($path) {
      if (!is_file ($path)) {
        $alts = array (
          '.cache.php',
          DIRECTORY_SEPARATOR . 'index.cache.php',
          '.php', DIRECTORY_SEPARATOR . 'index.php'
        );

        $altsLen = count ($alts);

        for ($i = 0; $i < $altsLen; $i++) {
          $alt = $alts [$i];

          if (is_file (join ('', [$path, $alt]))) {
            return realpath (join ('', [$path, $alt]));
          }
        }
      }

      return $path;
    }

    protected static function StripPath ($targetPath, $path, $n = 0) {
      $n = is_int ($n) ? $n : 0;
      $paths = preg_split ('/(\/|\\\)+/', $targetPath);
      $pathsCount = count ($paths);

      for ($i = 0; $i < $pathsCount; $i++) {
        $currentPathSlice = join (DIRECTORY_SEPARATOR,
          array_slice ($paths, 0, $i)
        );

        if ($currentPathSlice === $path) {
          return $currentPathSlice = join (
            DIRECTORY_SEPARATOR,
            array_slice ($paths, $i + $n, $pathsCount)
          );
        }
      }

      return $targetPath;
    }

    public static function StripViewsPath ($path) {
      return self::StripPath ($path, self::ViewsPath (), 1);
    }

    public static function PathIsInViewsDir ($path) {
      $viewsPath = self::ViewsPath ();
      $path = self::StripPath ($path, $viewsPath);

      $pathFromViewsDir = join (DIRECTORY_SEPARATOR, [
        $viewsPath, $path
      ]);

      return file_exists ($pathFromViewsDir);
    }

    public static function StripRootPath ($path) {
      return self::StripPath ($path, __root__);
    }

    public static function FileAbsolutePath ($filePath) {
      $filePathSlices = preg_split ('/(\\\|\/)+/', $filePath);
      $filePathSlicesCount = count ($filePathSlices);
      $fileAbsolutePath = '';

      for ($i = 0; $i < $filePathSlicesCount; $i++) {
        $filePathSlice = $filePathSlices [ $i ];

        if (preg_match ('/^(\.+)$/', $filePathSlice)) {
          if ($filePathSlice === '..')
            $fileAbsolutePath = dirname ($fileAbsolutePath);
        } else {
          $fileAbsolutePath .= DIRECTORY_SEPARATOR . $filePathSlice;
        }
      }

      return preg_replace ('/^(\\\|\/)/', '', $fileAbsolutePath);
    }

    public static function RelativePathDecode ($path = '') {

      if (!is_file ($path)) {
        $re = '/^(<(.+)(\/)\s*\s*?>)$/';

        if (preg_match ( $re, $path, $match )) {
          return trim ($match [ 2 ]);
        } else {
          $path = self::matchPathAlternates ($path);
        }
      }

      $backTraceDir = self::getBackTraceDir ();

      if (preg_match ('/^\.+(\/|\\\)/', $path)) {
        $newFilePath = join (DIRECTORY_SEPARATOR, [
          $backTraceDir, $path
        ]);

        $newFilePath = self::matchPathAlternates ($newFilePath);

        #echo $newFilePath, " -> ", $path, " -> (RelativePathDecode)<br />";

        if (is_file ($newFilePath)) {
          return $newFilePath;
        }

        $newFilePath = self::StripViewsPath ($newFilePath);
        $newFilePath = join (DIRECTORY_SEPARATOR, [
          self::ViewsPath (), $newFilePath
        ]);

        $newFilePath = self::matchPathAlternates ($newFilePath);

        if (is_file ($newFilePath)) {
          return $newFilePath;
        }
      }

      return $path;
    }
  }}
}
