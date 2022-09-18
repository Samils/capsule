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
   * Make sure the module base internal trait is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!trait_exists ('Sammy\Packs\Samils\Capsule\Yields')) {
  /**
   * @trait Yields
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
  trait Yields {
    public static function RenderYieldContext ($yie, $capsuleArgs = null) {
      $yieldContextData = call_user_func_array (
        [self::class, 'Yield'],
        func_get_args ()
      );

      $capsuleArgsSent = ( boolean )( is_array ($capsuleArgs) );

      return CapsuleHelper::RenderChildrenList (
        [ $yieldContextData ],
        [
          'defaultProps' => $capsuleArgsSent ? $capsuleArgs : []
        ]
      );
    }

    public static function Yield ($yieldName, $capsuleArgs = null) {
      $capsuleArgsSent = ( boolean )( is_array ($capsuleArgs) );

      $capsuleArguments = $capsuleArgsSent ? $capsuleArgs : [];

      $capsuleChildrenOffset = $capsuleArgsSent ? 2 : 1;

      $yieldContent = null;
      $backTrace = debug_backtrace ();
      $backTraceCount = count ($backTrace);
      $capsuleChildren = array_slice (
        func_get_args (),
        $capsuleChildrenOffset,
        func_num_args ()
      );

      for ($i = 0; $i < $backTraceCount; $i++) {
        $trace = $backTrace [ $i ];

        if (CapsuleHelper::YieldContentGiven ($trace)) {
          $yieldContent = CapsuleHelper::YieldContent (
            $trace
          );

          break;
        }
      }

      return !$yieldContent ? null : new CapsuleYieldContext ([
        'arguments' => $capsuleArguments,
        'children' => $capsuleChildren,
        'yieldContent' => $yieldContent
      ]);
    }
  }}
}
