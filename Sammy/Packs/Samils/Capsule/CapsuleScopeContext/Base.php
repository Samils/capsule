<?php
/**
 * @version 2.0
 * @author Sammy
 *
 * @keywords Samils, ils, php framework
 * -----------------
 * @package Sammy\Packs\Samils\Capsule\CapsuleScopeContext
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
namespace Sammy\Packs\Samils\Capsule\CapsuleScopeContext {
  use App\View\Capsule;
  /**
   * Make sure the module base internal trait is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!trait_exists ('Sammy\Packs\Samils\Capsule\CapsuleScopeContext\Base')) {
  /**
   * @class Base
   * Base internal trait for the
   * Capsule module.
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
    private $scope = [];

    public function __construct ($initalProps = null) {
      if (is_array ($initalProps)) {
        $this->setDatas ($initalProps);
      }
    }

    public function __get (string $prop = null) {
      $prop = (string)($prop);

      if (isset ($this->scope [$prop])) {
        return $this->scope [ $prop ];
      }

      $className = self::class;
      $backTrace = debug_backtrace ();
      $backTraceCount = count ($backTrace);
      $capsuleGlobalContext = Capsule::getGlobalContext ();

      for ($i = 0; $i < $backTraceCount; $i++) {
        $currentTrace = $backTrace [ $i ];

        $validTrace = ( boolean ) (
          is_array ($currentTrace) &&
          isset ($currentTrace ['args']) &&
          is_array ($currentTrace ['args']) &&
          isset ($currentTrace ['args'][1]) &&
          $currentTrace ['args'][1] instanceof $className
        );

        if (!$validTrace) continue;

        $alternateScope = $currentTrace ['args'][1];

        if (isset ($alternateScope->$prop)) {
          return $alternateScope->$prop;
        }
      }

      if (is_object ($capsuleGlobalContext) && get_class ($capsuleGlobalContext) !== static::class) {
        if (isset ($capsuleGlobalContext->$prop)) {
          return $capsuleGlobalContext->$prop;
        }
      }

    }

    public function __set (string $prop, $value = null) {
      return call_user_func_array (
        [$this, 'setData'], [$prop, $value]
      );
    }

    public function __isset (string $prop) {
      $prop = (string)($prop);
      return ( boolean )(isset ($this->scope [$prop]));
    }

    public function setDatas ($datas = []) {
      foreach ($datas as $prop => $value) {
        $this->setData ($prop, $value);
      }
    }

    public function setData (string $prop, $value = null) {
      $prop = (string)($prop);

      $this->scope [ $prop ] = $value;
    }

  }}
}
