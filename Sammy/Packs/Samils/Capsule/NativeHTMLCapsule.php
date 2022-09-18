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
  use App\View\CapsuleHelper\CapsuleHelper;
  /**
   * Make sure the module base internal class is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!class_exists('Sammy\Packs\Samils\Capsule\NativeHTMLCapsule')){
  /**
   * @class NativeHTMLCapsule
   * Base internal class for the
   * Samils module.
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
  class NativeHTMLCapsule {
    protected $tagName;

    private $props = [];
    private $attributes = [];

    public final function __construct (string $tagName = null) {
      $this->tagName = !$this->tagName ? $tagName : (
        (string) ($this->tagName)
      );

      $this->setTagName ($this->tagName);
    }

    public final function __get (string $prop = null) {
      $propValue = $this->getAttribute ($prop);

      return $propValue ? $propValue : (
        $this->getProp ($prop)
      );
    }

    public final function getProp (string $prop = null) {
      if (is_string ($prop) && isset ($this->props [$prop])) {
        return $this->props [$prop];
      }
    }

    public final function setProp (string $prop, $propValue) {
      $this->props [$prop] = $propValue;
    }

    public final function setProps (array $props = []) {
      /**
       * Map the props array and insert each
       * prop-value pair as a prop for the current
       * element
       */
      foreach ($props as $prop => $value) {
        $this->setProp ($prop, $value);
      }
    }

    public final function setProperty () {
      return call_user_func_array (
        [$this, 'setProp'], func_get_args ()
      );
    }

    public final function getProperty () {
      return call_user_func_array (
        [$this, 'getProp'], func_get_args ()
      );
    }

    public final function getProps () {
      return array_merge ([], $this->props);
    }

    public final function setAttribute ($attrName, $attrValue) {
      $this->attributes [$attrName] = $attrValue;
    }

    public final function setAttributes (array $attributes = []) {
      /**
       * Map the attributes array and insert each
       * prop-value pair as an attribute for the current
       * element.
       */
      foreach ($attributes as $attribute => $value) {
        $this->setAttribute ($attribute, $value);
      }
    }

    public final function getAttribute ($attrName) {
      if (isset ($this->attributes [$attrName])) {
        return $this->attributes [$attrName];
      }
    }

    public final function getAttributes () {
      return array_merge ([], $this->attributes);
    }

    private function setTagName ($tagName) {
      $this->tagName = strtolower ($tagName);

      $this->setProp ('tagName', $this->tagName);
    }

    private function isSelfClosed () {
      $selfClosedTags = preg_split (
        '/\s+/',
        'area base br col embed hr ' .
        'img input link meta param ' .
        'source track wbr menuitem ' .
        'keygen command'
      );

      return in_array ($this->tagName, $selfClosedTags);
    }

    protected final function getRenderDatas ($capsule, $capsuleArgs = []) {
      $capsuleArgsSent = (boolean)(is_array ($capsuleArgs));

      $capsuleArguments = $capsuleArgsSent ? $capsuleArgs : [];

      $this->attributes = array_merge (
        $this->attributes,
        $capsuleArguments
      );

      $capsuleChildrenOffset = $capsuleArgsSent ? 2 : 1;

      $capsuleChildren = array_slice (
        func_get_args (),
        $capsuleChildrenOffset,
        func_num_args ()
      );

      $capsuleArgumentsList = CapsuleHelper::Array2HTMLAttrList (
        $this->attributes
      );

      return [$capsuleChildren, $capsuleArgumentsList];
    }

    public function render ($capsule, $capsuleArgs = []) {
      list ($capsuleChildren, $capsuleArgumentsList) = call_user_func_array ([$this, 'getRenderDatas'], func_get_args ());

      $elementDataObject = [
        'element' => $this->tagName,
        'selfClosed' => $this->isSelfClosed (),
        'props' => $this->attributes,
        'propsTxt' => $capsuleArgumentsList,

        'children' => CapsuleHelper::RenderChildrenList ($capsuleChildren)
      ];

      return $elementDataObject;
      /**
      if ($this->isSelfClosed ()) {
        echo "<{$this->tagName}{$capsuleArgumentsList} />";
      } else {
        echo "<{$this->tagName}{$capsuleArgumentsList}>";
        CapsuleHelper::RenderChildrenList (
          $capsuleChildren
        );
        echo "</{$this->tagName}>";
      }
      */
    }
  }}
}
