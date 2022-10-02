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
  use Closure;
  use Sammy\Packs\HTTP\Request;
  use App\View\Capsule;
  use App\View\CapsuleHelper\CapsuleHelper;
  /**
   * Make sure the module base internal class is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!class_exists ('Sammy\Packs\Samils\Capsule\Base')) {
  /**
   * @class Base
   * Base internal class for the
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
  class Base {
    use Paths;
    use Yields;
    use Imports;
    use Exports;
    use Config\Base;
    use CapsuleRenderContext\Component\Helper;

    #private $capsuleRenderingTree = [];

    private $capsuleName;
    private $yieldDone = false;

    #private static $currentRenderingComponent;

    /**
    * [$capsules description]
    * @var array
    */
    private static $capsules = array ();

    /**
    * [$files description]
    * @var array
    */
    private static $files = array ();

    private $body;
    public $fileName;

    private static $globalContext;

    public function __construct ($name, $body) {
      $req = new Request;
      self::setCapsuleProps ($this->fileName);

      if (!self::$globalContext) {
        self::$globalContext = $req->controller;
      }

      $this->capsuleName = $name;
      $this->body = Closure::bind (
        $body,
        self::$globalContext,
        get_class (self::$globalContext)
      );
    }

    public function render ($body = null) {

      $args = func_get_args ();

      $capsuleArgsSent = ( boolean ) (
        isset ($args [1]) &&
        is_array ($args [1])
      );

      $capsuleArgs = !$capsuleArgsSent ? [] : (
        $args [ 1 ]
      );

      $capsuleChildrenOffset = $capsuleArgsSent ? 2 : 1;

      $children = array_slice (
        $args,
        $capsuleChildrenOffset,
        count ($args)
      );

      $capsuleInitialProps = $this->_getCapsuleProps ();

      #echo $this->fileName, "<br /><pre>";
      #print_r($capsuleInitialProps);
      #echo '</pre><br /><br /><br /><br /><br />';

      $capsuleScopeContext = new CapsuleScopeContext (
        $capsuleInitialProps
      );

      $capsuleRenderContext = new CapsuleRenderContext ([
        'component' => $this->capsuleName,
        'props' => array_merge ($capsuleArgs, [
          # 'children' => $children
        ])
      ]);

      $capsuleMapContext = new CapsuleMapContext ();

      call_user_func_array ($this->body, [
        array_merge ($capsuleArgs, [
          'children' => $children
        ]),
        $capsuleScopeContext,
        [
          'args' => [
            'yie' => $body,
            '_name' => $this->capsuleName,
            # The capsule class object context.
            # This should provide a way for getting some
            # information from the capsule when rendering a
            # children inside it<The capsule rendering Tree>
            '_context' => $this,
            '_mapContext' => $capsuleMapContext,
            '_renderContext' => $capsuleRenderContext
          ],
          'children' => $children
        ]
      ]);

      return $capsuleRenderContext->getChildren ();
    }

    /**
    * [Create description]
    * @param string|mixed $capsuleName
    */
    public static function Create ($capsuleName_ = '') {
      $file = self::register (debug_backtrace());

      $capsuleName = !is_string ($capsuleName_) ? null : (
        $capsuleName_
      );

      $args = func_get_args ();
      $capsuleBody = $args [-1 + count ($args)];

      $capsuleCore = new static ($capsuleName, $capsuleBody);

      $capsuleCore->fileName = $file;

      if ( is_string ($capsuleName) && $capsuleName ) {
        self::$files[$file]['scope'][$capsuleName] = (
          $capsuleCore
        );

        self::$capsules[ $capsuleName ] = (
          $capsuleCore
        );
      }

      return $capsuleCore;
    }

    public static function CreateElement ($capsuleElement, $capsuleArgs = []) {
      $file = self::register (debug_backtrace());

      $capsuleArgsSent = ( boolean )( is_array ($capsuleArgs) );

      $capsuleArguments = $capsuleArgsSent ? $capsuleArgs : [];

      $capsuleChildrenOffset = $capsuleArgsSent ? 2 : 1;

      $capsuleChildren = array_slice (
        func_get_args (),
        $capsuleChildrenOffset,
        func_num_args ()
      );

      #echo "Create Element => ", $capsuleElement, "<br />";

      $capsuleCore = self::useCapsule ($capsuleElement, $file, [
        'arguments' => $capsuleArguments,
        'children' => $capsuleChildren
      ]);

      return new CapsuleElement ([
        '_element' => $capsuleCore,
        '_elementName' => $capsuleElement,
        'properties' => $capsuleArguments,
        'children' => $capsuleChildren
      ]);
    }

    public static function Def ($capsuleName) {
      $file = self::register (debug_backtrace());

      $class = self::class;
      $capsuleCore = null;
      $args = func_get_args ();
      $capsuleBody = $args [-1 + count ($args)];

      if ($capsuleBody instanceof \Closure) {
        $capsuleCore = new static (
          $capsuleName, $capsuleBody
        );
        $capsuleCore->fileName = $file;
      } elseif ($capsuleBody instanceof $class) {
        $capsuleCore = $capsuleBody;
      }

      if ($capsuleCore instanceof $class) {
        self::$files[$file]['scope'][$capsuleName] = (
          $capsuleCore
        );
      }
    }

    public static function Element ($capsule) {
      $file = self::register (debug_backtrace());

      if ( !(is_string($capsule) && $capsule) ) {
        exit ('Capsule name is not a string');
      }

      if (!isset(self::$files[$file]['scope'][$capsule])) {

        echo '<pre>';
        print_r (array_slice(debug_backtrace(), 0, 1));
        echo '</pre>';
        exit ('Error: The '.$capsule.' capsule can not be got because it does not exists');
      }

      return self::$files[$file]['scope'][$capsule];
    }

    private static function isCapsule ( $capsuleCore ) {
      $class = self::class;

      return ( boolean ) (
        is_object ($capsuleCore) &&
        $capsuleCore instanceof $class
      );
    }

    private static function setCapsuleProps ($capsuleFile) {
      $capsulePropsSet = ( boolean )(
        isset (self::$files[$capsuleFile]) &&
        is_array (self::$files[$capsuleFile]) &&
        isset (self::$files[$capsuleFile]['props']) &&
        is_array (self::$files[$capsuleFile]['props'])
      );

      if (!$capsulePropsSet) {
        self::$files[$capsuleFile]['props'] = [];
      }
    }

    private static function isNativeHtmlCapsule ($capsule) {
      return ( boolean ) (
        is_string ($capsule) &&
        !empty ($capsule) &&
        preg_match ('/^([a-zA-Z0-9]+)$/', $capsule) &&
        preg_match ('/^([a-z])/', $capsule)
      );
    }

    /**
     * @method Sammy\Packs\Samils\Capsule useCapsule
     *
     * @return Sammy\Packs\Samils\Capsule
     */
    private static function useCapsule ($capsule, $file = null) {
      if (is_string ($capsule) && !empty ($capsule)) {

        if (CapsuleHelper::IsFragmentReference ($capsule)) {
          return new static ('Fragment', function ($args) {

            $children = isset ($args ['children']) ? $args ['children'] : [];

            return Capsule::PartialRender ('Fragment', [], $children);
            # Base::RenderYieldContext (null, $args);
            # return isset ($args ['children']) ? $args ['children'] : ['null'];
          });
        }

        if (self::isNativeHtmlCapsule ($capsule)) {

          $capsuleClassName = CapsuleHelper::GetNativeHTMLCapsuleClass ($capsule);

          # $lowerCapsuleName = strtolower ($capsule);

          return new $capsuleClassName ($capsule);
        }

        $capsuleStrSlices = preg_split ('/\./', $capsule);

        $currentArray = self::$files [$file]['scope'];

        # isset([$capsule])

        foreach ($capsuleStrSlices as $slice) {
          $currentKeyExists = ( boolean ) (
            is_array ($currentArray) &&
            isset ($currentArray [$slice])
          );

          if ( $currentKeyExists ) {
            $currentArray = $currentArray [$slice];
          } else {
            return;
          }
        }

        if (self::isCapsule ($currentArray)) {
          return $currentArray;
        }
      }

      if (self::isCapsule ($capsule)) {
        return $capsule;
      }
    }

    public static function PartialRender ($capsule = null, $capsuleArgs = []) {
      $backTrace = debug_backtrace ();
      $file = self::register ($backTrace);

      $capsuleArgsSent = ( boolean )( is_array ($capsuleArgs) );


      $renderContextObject = self::GetCurrentRenderContext ($backTrace);

      if (!$renderContextObject) {
        return;
      }

      $capsuleArguments = $capsuleArgsSent ? $capsuleArgs : [];

      $capsuleChildrenOffset = $capsuleArgsSent ? 2 : 1;

      $capsuleChildren = array_slice (
        func_get_args (),
        $capsuleChildrenOffset,
        func_num_args ()
      );

      $partialRenderContent = null;

      if (CapsuleHelper::IsFragmentReference ($capsule)) {
        $partialRenderContent = CapsuleHelper::RenderChildrenList (
          [ $capsuleChildren ]
        );

        return $renderContextObject->render ($partialRenderContent);
      }

      $capsuleCore = self::useCapsule ($capsule, $file, [
        'arguments' => $capsuleArguments,
        'children' => $capsuleChildren
      ]);

      if ( $capsuleCore ) {
        # ****
        $partialRenderContent = call_user_func_array (
          [$capsuleCore, 'render'],
          array_merge (
            [$capsule, $capsuleArguments],
            $capsuleChildren
          )
        );
      }

      # include the partial render content
      # inside the current render context
      # ...
      return $renderContextObject->render ($partialRenderContent);
    }

    public function _getCapsuleProps () {
      return self::$files [$this->fileName]['props'];
    }

    public static function getGlobalContext () {
      return self::$globalContext;
    }

  }}
}
