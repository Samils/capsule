<?php
/**
 * @version 2.0
 * @author Sammy
 *
 * @keywords Samils, ils, php framework
 * -----------------
 * @package App\View
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
namespace App\View {
  use Sammy\Packs\Samils\Capsule\ImageModuleParser;
  use Sammy\Packs\Samils\Capsule\NativeHTMLCapsule;
  /**
   * Make sure the module base internal class is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!class_exists('App\View\HTMLImageElement')){
  /**
   * @class Base
   * Base internal class for the
   * View module.
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
  class HTMLImageElement extends NativeHTMLCapsule {
    protected $tagName = 'img';

    public function render ($capsule, $capsuleArgs = []) {
      list ($_, $capsuleArgs) = call_user_func_array (
        [$this, 'getRenderDatas'], func_get_args ()
      );

      $src = $this->getAttribute ('src');

      if (is_object ($src) && get_class ($src) === self::class) {
        $this->setProps ($src->getProps ());
        $this->setAttributes ($src->getAttributes ());
      }

      $publicPath = $this->getProp ('__publicPath');

      $mayParseImage = ( boolean )(
        is_string ($publicPath) &&
        !empty ($publicPath) &&
        ImageModuleParser::MayParseImage (
          $publicPath
        )
      );

      if ( $mayParseImage ) {
        $image = ImageModuleParser::ParseImage (
          $publicPath
        );

        return ($image);
      }

      return call_user_func_array (
        [parent::class, 'render'], func_get_args ()
      );
    }
  }}
}
