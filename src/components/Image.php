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
  use Configure as Conf;
  use App\View\CapsuleHelper\ArrayHelper;
  use Sammy\Packs\Samils\Capsule\CapsuleScopeContext;

  Capsule::Def ('Image', function ($args) {
    $imageRestArguments = [];
    $src = isset ($args ['src']) ? $args ['src'] : '';

    foreach ($args as $argKey => $argValue) {
      if (strtolower ($argKey) !== 'src') {
        $imageRestArguments [strtolower ($argKey)] = $argValue;
      }
    }

    if (!isset ($imageRestArguments ['alt'])) {
      $imageRestArguments ['alt'] = $src;
    }

    $imageRestArguments = ArrayHelper::PropsBeyond (['children'], $imageRestArguments);

    $relativePathRe = '/^\.+\//';

    $src = preg_replace ('/^\/+/', '', $src);

    if (preg_match ($relativePathRe, $src)) {
      $refFilePath = CapsuleHelper::traceComponentFilePath (debug_backtrace ());

      $imagePath = join (DIRECTORY_SEPARATOR, [dirname ($refFilePath), $src]);

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

    $src = (defined('Configure::ApplicationAssetsPath') ? Conf::ApplicationAssetsPath : '') . '/images/' . $src;

    Capsule::PartialRender ('img',
      array_merge ($imageRestArguments, [
        'src' => $isValidImageElement ? $img : $src,
        'crossorigin' => 'annonimous'
      ])
    );
  });

  Capsule::Export ('Image');

  $module->exports = Capsule::Element ('Image');
}
