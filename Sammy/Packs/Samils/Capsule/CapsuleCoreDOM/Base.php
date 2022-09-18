<?php
/**
 * @version 2.0
 * @author Sammy
 *
 * @keywords Samils, ils, php framework
 * -----------------
 * @package Sammy\Packs\Samils\Capsule\CapsuleCoreDOM
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
namespace Sammy\Packs\Samils\Capsule\CapsuleCoreDOM {
  use App\View\CapsuleHelper\CapsuleHelper;
  use Sammy\Packs\Samils\Capsule\CapsuleVirtualDOM;
  /**
   * Make sure the module base internal trait is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!trait_exists ('Sammy\Packs\Samils\Capsule\CapsuleCoreDOM\Base')) {
  /**
   * @trait Base
   * Base internal trait for the
   * Samils\Capsule\CapsuleCoreDOM module.
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
  trait Base {
    /**
     * @method void Render Virtual DOM to Real DOM
     */
    public static function Render (CapsuleVirtualDOM $virtualDom) {
      return print (self::PreRender ($virtualDom));
    }

    /**
     * @method string Prerender Virtual DOM to Real DOM
     */
    public static function PreRender (CapsuleVirtualDOM $virtualDom) {
      $virtualDomContent = $virtualDom->getSkeleton ();

      if (!isset ($virtualDomContent ['children'])) {
        $virtualDomContent ['children'] = null;
      }

      self::renderPreContent ($virtualDomContent);

      if (self::isCapsule ($virtualDomContent)) {
        return (self::printChildrenList ($virtualDomContent ['children']));
      }

      return self::printHtmlElement ($virtualDomContent);
    }

    /**
     * @method void renderPreContent
     */
    private static function renderPreContent ($element) {
      if (is_array ($element) && isset ($element ['_preContent'])) {
        print ((string)($element ['_preContent']));
      }
    }

    /**
     * @method bool isNativeHtmlElement
     */
    private static function isNativeHtmlElement ($element) {
      return is_array ($element) && isset ($element ['element']);
    }

    /**
     * @method bool isCapsule
     */
    private static function isCapsule ($element) {
      return is_array ($element) && isset ($element ['component']);
    }

    /**
     * @method string printChildrenList
     */
    private static function printChildrenList ($childrenList) {
      if (!(is_array ($childrenList) && $childrenList)) {
        return;
      }

      $rawChildrenList = [];

      foreach ($childrenList as $child) {
        if (self::isCapsule ($child)
          || self::isNativeHtmlElement ($child)) {
          array_push ($rawChildrenList, self::printHtml ($child));
        } elseif (is_array ($child)) {
          array_push ($rawChildrenList, self::printChildrenList ($child));
        } elseif (is_string ($child) && !empty ($child)) {
          array_push ($rawChildrenList, $child);
        }
      }

      return join ('', $rawChildrenList);
    }

    /**
     * @method string printHtmlElement
     */
    private static function printHtmlElement (array $htmlElement) {

      $elementChildren = $htmlElement ['children'];
      $elementTagName = $htmlElement ['element'];

      if (!isset ($htmlElement ['props'])) {
        $htmlElement ['props'] = [];
      }

      $elementProps = CapsuleHelper::Array2HTMLAttrList ($htmlElement ['props']);

      if (self::isSelfClosedElement ($htmlElement)) {
        return join ('', [
          "<$elementTagName{$elementProps}/>"
        ]);
      }

      return join ('', [
        "<$elementTagName{$elementProps}>",
        self::printChildrenList ($elementChildren),
        "</$elementTagName>"
      ]);
    }

    /**
     * @method string printHtml
     */
    private static function printHtml (array $virtualDomContent) {
      # = $virtualDom->getSkeleton ();

      if (isset ($virtualDomContent ['content']) && is_string ($virtualDomContent ['content'])) {
        $virtualDomContent ['children'] = [$virtualDomContent ['content']];
      } elseif (!isset ($virtualDomContent ['children'])) {
        $virtualDomContent ['children'] = null;
      }

      self::renderPreContent ($virtualDomContent);

      if (self::isCapsule ($virtualDomContent)) {
        return (self::printChildrenList ($virtualDomContent ['children']));
      }

      return (self::printHtmlElement ($virtualDomContent));
    }

    /**
     * @method bool isSelfClosedElement
     */
    private static function isSelfClosedElement (array $element) {
      return isset ($element ['selfClosed']) && is_bool ($element ['selfClosed']) && $element ['selfClosed'];
    }
  }}
}
