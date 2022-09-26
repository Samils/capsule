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
  use Capsule\Text;
  use Configure as Conf;
  use App\View\CapsuleHelper\ArrayHelper;
  use App\View\CapsuleHelper\CapsuleHelper;
  use Sammy\Packs\Samils\Capsule\CapsuleScopeContext;

  Capsule::Def ('FlatImage', function ($args) {
    $imageRestArguments = [];
    $src = isset ($args ['src']) ? $args ['src'] : '';

    foreach ($args as $argKey => $argValue) {
      if (strtolower ($argKey) !== 'src') {
        $imageRestArguments [strtolower ($argKey)] = $argValue;
      }
    }

    $imageRestArguments = ArrayHelper::PropsBeyond (['children']);

    $relativePathRe = '/^\.\//';

    $src = preg_replace ('/^\/+/', '', $src);

    if (preg_match ($relativePathRe, $src)) {
      $refFilePath = CapsuleHelper::traceComponentFilePath (debug_backtrace ());

      $imagePath = preg_replace ($relativePathRe, '', $src);

      $imagePath = join (DIRECTORY_SEPARATOR, [dirname ($refFilePath), $imagePath]);

      $imagePath = join (DIRECTORY_SEPARATOR, [
        (Capsule::ViewsPath ()),
        Capsule::StripViewsPath ($imagePath)
      ]);

      if (is_file ($imagePath)) {
        $img = requires ($imagePath);
      }

    } else {
      $path = requires ('path');

      $imagePath = $path->join ('~', 'app', 'assets', 'images', $src);

      if (is_file ($imagePath)) {
        $img = requires ($imagePath);
      }
    }

    $isValidImageElement = (boolean)(
      isset ($img)
      && is_object ($img)
      && $img instanceof HTMLImageElement
    );

    $src = (defined ('Configure::ApplicationAssetsPath') ? Conf::ApplicationAssetsPath : '') . '/images/' . $src;

    $imageSrc = $isValidImageElement ? $img->getAttribute ('src') : $src;

    $selector = CapsuleHelper::generateComponentSelectorRef ('FlatImage');

    if (isset ($imageRestArguments ['class']) && is_string ($imageRestArguments ['class'])) {
      $selector = join (' ', [$selector, $imageRestArguments ['class']]);
    }

    Capsule::PartialRender (null, [],
      Capsule::PartialRender ('head',
        Capsule::PartialRender ('style', ['type' => 'text/css'],
          new Text ('.'.$selector.'{background-image: url('.$imageSrc.')}')
        )
      ),

      Capsule::PartialRender ('div', array_merge ($imageRestArguments, ['class' => $selector]),
        Capsule::Yield ([])
      )
    );
  });

  Capsule::Export ('FlatImage');

  $module->exports = Capsule::Element ('FlatImage');
}
