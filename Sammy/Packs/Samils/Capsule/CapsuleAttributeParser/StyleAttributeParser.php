<?php
/**
 * @version 2.0
 * @author Sammy
 *
 * @keywords Samils, ils, php framework
 * -----------------
 * @package Sammy\Packs\Samils\Capsule\CapsuleAttributeParser
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
namespace Sammy\Packs\Samils\Capsule\CapsuleAttributeParser {
  /**
   * Make sure the module base internal class is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!class_exists('Sammy\Packs\Samils\Capsule\CapsuleAttributeParser\StyleAttributeParser')){
  /**
   * @class StyleAttributeParser
   * Base internal class for the
   * Samils\Capsule\CapsuleAttributeParser module.
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
  abstract class StyleAttributeParser {
    protected static function rewriteStylePropKey ($key) {
      $re = '/[A-Z]/';
      $key = preg_replace_callback (
        $re,
        function ($match) {
          # background-Color
          return '-' . $match [0];
        },
        (string)($key)
      );

      return preg_replace ('/^(-*)/', '',
        strtolower ($key)
      );
    }
    /**
     * @method string ParseStyleAttribute
     *
     */
    public static function ParseStyleAttribute ($styles = null) {
      if (is_string ($styles)) {
        return $styles;
      }

      if (is_array ($styles)) {
        $styleString = '';

        foreach ($styles as $prop => $value) {
          $prop = self::rewriteStylePropKey ($prop);

          if (!!is_string ($value)) {
            $styleString .= join ('', [
              $prop, ':', $value, ';'
            ]);
          }
        }

        return $styleString;
      }
    }
  }}
}
