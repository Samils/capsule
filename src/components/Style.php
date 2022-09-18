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
  use Sammy\Packs\Samils\Capsule\CapsuleScopeContext;

  Capsule::Def ('Style', function ($args, CapsuleScopeContext $scope) {
    $scope->href = !isset ($args['href']) ? null : $args [ 'href' ];

    if (defined ('Configure::ApplicationAssetsRealPath') && is_dir (constant ('Configure::ApplicationAssetsRealPath'))) {
      $styleFilePath = join (DIRECTORY_SEPARATOR, [
        constant ('Configure::ApplicationAssetsRealPath'),
        'stylesheet', $scope->href
      ]);

      if (is_file ($styleFilePath)) {

        $styleFileContent = file_get_contents ($styleFilePath);

        $styleFileContent = preg_replace ('/\s+/', ' ', $styleFileContent);

        $re = '/\s*([,;:\[\]\(\)\{\}])\s*/';

        $styleFileContent = preg_replace_callback ($re, function ($match) {
          return trim ($match [1]);
        }, $styleFileContent);

        return Capsule::PartialRender ('style', ['type' => 'text/css', 'crossorigin' => 'annonimous'], $styleFileContent);
      }

    }

    Capsule::PartialRender ('link', [
      'rel' => "stylesheet",
      'type' => "text/css",
      'href' => (defined('Configure::ApplicationAssetsPath') ? Conf::ApplicationAssetsPath : '') . '/stylesheet/' . preg_replace ('/^\/+/', '', $scope->href),
      'crossorigin' => "annonimous"
    ]);
  });

  Capsule::Export ('Style');

  $module->exports = Capsule::Element ('Style');
}
