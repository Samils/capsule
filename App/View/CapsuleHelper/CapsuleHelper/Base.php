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
  use Closure;
  use Capsule\Text;
  use App\View\Capsule;
  use Sammy\Packs\Samils\Capsule\CapsuleElement;
  use Sammy\Packs\Samils\Capsule\NativeHTMLCapsule;
  use Sammy\Packs\Samils\Capsule\Base as CapsuleBase;
  use Sammy\Packs\Samils\Capsule\CapsuleScopeContext;
  use Sammy\Packs\Samils\Capsule\CapsuleYieldContext;
  use Sammy\Packs\Samils\Capsule\CapsuleRenderContext;

  /**
   * Make sure the module base internal trait is not
   * declared in the php global Base defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!trait_exists ('App\View\CapsuleHelper\CapsuleHelper\Base')) {
  /**
   * @trait Base
   * Base internal trait for the
   * CapsuleHelper\CapsuleHelper module.
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
     * @method array Render a given children list as array
     */
    public static function RenderChildrenList ($children = []) {
      $children = !(is_array ($children) && $children) ? [] : (
        array_merge ($children, [])
      );

      $childrenCount = count ($children);
      $options = func_get_arg (-1 + func_num_args ());

      $defaultProps = self::childrenListDefaultProps ($options);

      $childrenList = [];

      for ($i = 0; $i < $childrenCount; $i++) {
        if (!isset ($children [$i])) {
          continue;
        }

        $child = $children [$i];

        $capsuleRenderContent = null;
        $childIsACapsuleElement = ( boolean )(
          is_object ($child) &&
          get_class ($child) === CapsuleElement::class
        );

        if ($childIsACapsuleElement) {
          $child = $child->getDatas ();
        }

        if (self::isCapsuleReference ($child)) {

          $childElement = $child ['_element'];

          $capsuleRenderContent = call_user_func_array (
            [$childElement, 'render'],
            array_merge (
              [
                null,
                array_merge (
                  $defaultProps,
                  $child ['properties']
                )
              ],
              $child ['children']
            )
          );
        } elseif (self::isCapsule ($child)) {
          $capsuleRenderContent = call_user_func_array (
            [$child, 'render'],
            array_merge (
              [
                null,
                $defaultProps
              ],
              []
            )
          );
        } elseif ($child instanceof Text) {
          $capsuleRenderContent = [
            'component' => 'Text',
            'props' => [],
            'content' => $child->getContent ()
          ];
        } elseif (is_array ($child)) {
          $childrenList = array_merge (
            $childrenList,
            self::RenderChildrenList ($child)
          );
        } elseif ($func = self::isFunction ($child)) {
          $renderContext = new CapsuleRenderContext ([
            'component' => 'Fragment',
            'props' => []
          ]);

          $funcData = call_user_func_array ($func, [
            $defaultProps, new CapsuleScopeContext, [
              'args' => [
                '_renderContext' => $renderContext
              ]
            ]
          ]);

          $renderChildrenList = self::RenderChildrenList ([$funcData]);

          if  ($renderContext->empty ()) {
            $capsuleRenderContent = $renderChildrenList;
          } else {
            $capsuleRenderContent = array_merge (
              [$renderContext->getChildren ()],
              [$renderChildrenList]
            );
          }
        } elseif (self::isCapsuleYieldContext ($child)) {
          $capsuleRenderContent = self::RenderChildrenList (
            $child->getContent (),
            [
              'defaultProps' => array_merge (
                $defaultProps,
                $child->getArguments ()
              )
            ]
          );
        } elseif (self::isPrintableCapsuleChild ($child)) {
          $capsuleRenderContent = self::printCapsuleChild ($child);
        }

        array_push ($childrenList, $capsuleRenderContent);
      }

      return $childrenList;
    }

    public static function Stringify ($data) {
      if (in_array (gettype ($data), ['array', 'object'])){
        return json_encode (self::LeanData ($data));
      } else {
        if (is_bool($data)) {
          return $data ? 'true' : 'false';
        } else {
          return ((string)($data));
        }
      }
    }

    public static function YieldContentGiven ($data) {
      return ( boolean ) (
        is_array ($data) &&
        isset ($data ['args']) &&
        is_array ($data ['args']) &&
        isset ($data ['args'][2]) &&
        is_array ($data ['args'][2]) &&
        isset ($data ['args'][2]['children']) &&
        is_array ($data ['args'][2]['children'])
      );
    }

    public static function YieldContent ($data) {
      if (self::YieldContentGiven ($data)) {
        return $data ['args'][2]['children'];
      }
    }

    public static function IsFragmentReference ($data) {
      return ( boolean ) (
        is_null ($data) ||
        (is_string ($data) && empty ($data)) ||
        preg_match ('/^fragment$/i', $data)
      );
    }

    public static function LeanData ($data = null) {
      if (self::IsLeanable ($data)) {
        return $data->lean ();
      } elseif (is_array ($data)) {
        foreach ($data as $key => $value) {
          $data [ $key ] = self::LeanData ($value);
        }
      }

      return $data;
    }

    public static function IsLeanable ($data) {
      $SamibaseILeanable = join ('\\', [
        'Sammy', 'Packs', 'Sami', 'Base',
        'ILeanable'
      ]);

      $ILeanable = join ('\\', [
        'Sammy', 'Packs', 'ILeanable'
      ]);

      if (is_object ($data)) {
        $classImplemets = class_implements (
          get_class ($data)
        );

        return ( boolean ) (
          in_array ($SamibaseILeanable, $classImplemets) ||
          in_array ($ILeanable, $classImplemets)
        );
      }

      return false;
    }

    private static function isFunction ($data) {
      $dataIsClosureObject = ( boolean )(
        is_object ($data) &&
        get_class ($data) === Closure::class
      );

      $globalContext = Capsule::getGlobalContext ();

      if (!$globalContext) {
        $globalContext = new CapsuleScopeContext;
      }

      if ($dataIsClosureObject) {
        return Closure::bind (
          $data,
          $globalContext,
          CapsuleScopeContext::class
        );
      }
    }

    private static function isPrintableCapsuleChild ($child) {
      return ( boolean ) (
        is_scalar ($child) ||
        is_object ($child) ||
        is_array ($child)
      );
    }

    private static function printCapsuleChild ($child) {
      $child = self::Stringify ($child);
      return [
        'component' => 'Text',
        'props' => [],
        'content' => (htmlentities ($child))
      ];
    }

    private static function isCapsuleReference ($data) {
      return ( boolean ) (
        is_array ($data) &&
        isset ($data ['_element']) &&
        self::isCapsule ($data ['_element'])
      );
    }

    private static function isCapsule ($data) {
      $baseClass = CapsuleBase::class;

      return ( boolean ) (
        is_object ($data) &&
        (
          $data instanceof $baseClass ||
          self::isNativeHTMLElement ($data)
        )
      );
    }

    private static function isCapsuleYieldContext ($data) {
      $capsuleYieldContextClass = CapsuleYieldContext::class;

      return ( boolean ) (
        is_object ($data) &&
        $data instanceof $capsuleYieldContextClass
      );
    }

    private static function jsonObjectEncode ($data) {
      return json_encode (self::LeanData ($data));
    }

    private static function childrenListDefaultProps ($options) {
      $childrenListDefaultPropsSet = ( boolean ) (
        is_array ($options) &&
        isset ($options ['defaultProps']) &&
        is_array ($options ['defaultProps'])
      );

      if ($childrenListDefaultPropsSet) {
        return $options ['defaultProps'];
      }

      return [];
    }
  }}
}
