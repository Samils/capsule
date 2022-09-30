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
  use Sammy\Packs\Samils\Capsule\CapsuleAttributeParser;
  use Sammy\Packs\Samils\Capsule\NativeHTMLCapsule;
  /**
   * Make sure the module base internal trait is not
   * declared in the php global Base before creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!trait_exists ('App\View\CapsuleHelper\CapsuleHelper\HTMLHelper')) {
  /**
   * @trait HTMLHelper
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
  trait HTMLHelper {
    /**
     * @method string Array2HTMLAttrList
     *
     * Convert a given array to an html
     * attribute list.
     *
     */
    public static function Array2HTMLAttrList ($array = []) {
      $array = is_array ($array) ? $array : [];

      if (!$array) return '';

      $attributeListStr = '';

      $attributeParser = new CapsuleAttributeParser;

      foreach ($array as $attribute => $value) {
        list ($attributeName, $attributeValue) = ( array )(
          $attributeParser->parseAttribute (
            $attribute, $value
          )
        );

        $attributeValue = self::evaluateHTMLAttr (
          $attributeValue,
          $attributeName
        );

        if ( $attributeValue ) {
          $attributeValue = "={$attributeValue}";
        }

        $attributeListStr .= " {$attributeName}{$attributeValue}";
      }

      return $attributeListStr;
    }

    public static function GetNativeHTMLCapsuleClass ($ref) {
      $ref = strtolower ($ref);

      $headingRe = '/^h([1-6])$/';
      $particularCases = [
        'a' => 'anchor',
        'dl' => 'dlist',
        'dd' => '',
        'img' => 'image',
        'ol' => 'olist',
        'ul' => 'ulist',
        'tr' => 'tableRow',
        'td' => 'tableCell',
        'th' => 'tableCell',
        'thead' => 'tableSection',
        'tbody' => 'tableSection'
      ];

      if (isset ($particularCases [$ref])) {
        $ref = $particularCases [$ref];
      } elseif (preg_match ($headingRe, $ref)) {
        $ref = 'Heading';
      } else {
        $ref = 'Unknown';
      }

      # App\View\HTMLAnchorElement
      $classRef = join ('\\', [
        '\App', 'View', join ('', ['HTML', ucfirst ($ref), 'Element'])
      ]);

      return $classRef;
    }

    private static function isNativeHTMLElement ($data) {
      $nativeHTMLCapsuleClass = NativeHTMLCapsule::class;

      return ( boolean ) (
        is_object ($data) &&
        $data instanceof $nativeHTMLCapsuleClass
      );
    }

    private static function evaluateHTMLAttr ($value = null) {
      $valueType = strtolower (gettype($value));

      if (in_array ($valueType, ['array', 'object'])) {
        $decodedvalue = self::jsonObjectEncode ($value);

        return "'{$decodedvalue}'";
      }

      return "\"{$value}\"";
    }
  }}
}
