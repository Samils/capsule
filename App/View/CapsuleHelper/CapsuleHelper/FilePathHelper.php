<?php
/**
 * @version 2.0
 * @author Sammy
 *
 * @keywords Samils, ils, php framework
 * -----------------
 * @package App\View\CapsuleHelper\CapsuleHelper
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
namespace App\View\CapsuleHelper\CapsuleHelper {
  use php\module;
  /**
   * Make sure the module base internal trait is not
   * declared in the php global Base before creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!trait_exists ('App\View\CapsuleHelper\CapsuleHelper\FilePathHelper')) {
  /**
   * @trait FilePathHelper
   * Base internal trait for the
   * CapsuleHelper\CapsuleHelper module.
   * -
   * This is (in the ils environment)
   * an instance of the php module,
   * which should contain the module
   * core functionalities that should
   * be extended.
   * -
   * For extending the module, just create
   * an 'exts' directory in the module directory
   * and boot it by using the ils directory boot.
   * -
   */
  trait FilePathHelper {
    /**
     * @method string traceComponentFilePath
     */
    public static function traceComponentFilePath (array $backTrace) {
      $moduleRootDirRe = self::moduleRootDirRe ();
      /**
       * map the $backTrace array that should have each item as
       * array and try matching the momment the reference
       * file directory path is not the same as the current
       * module root is.
       */
      if (!$backTrace) {
        return;
      }

      foreach ($backTrace as $trace) {
        if (!(is_array ($trace)
          && isset ($trace ['file']))) {
          continue;
        }

        $filePath = $trace ['file'];

        if (!preg_match ($moduleRootDirRe, $filePath)) {
          return $filePath;
        }
      }
    }

    /**
     * @method string moduleRootDirRe
     */
    private static function moduleRootDirRe () {
      $specialCharsList = '/[\/\^\$\[\]\{\}\(\)\\\\.]/';

      $moduleRootDir = preg_replace_callback (
        $specialCharsList, function ($match) {
          return '\\' . $match [0];
      }, module::getModuleRootDir (__DIR__));

      return join ('', ['/^(', $moduleRootDir, ')/']);
    }
  }}
}
