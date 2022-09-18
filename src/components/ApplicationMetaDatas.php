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

  Capsule::Def ('ApplicationMetaDatas', function ($args, CapsuleScopeContext $scope) {
    $constName = 'Configure::ApplicationMetaDatas';

    $scope->appMetaDatas = !defined ($constName) ? [] : (
      constant ($constName)
    );

    Capsule::PartialRender ('Fragment', Capsule::CreateElement ('meta', ['charset' => "utf-8"]), Capsule::CreateElement ('meta', ['http-equiv' => "X-UA-Compatible", 'content' => "ie-edge"]), Capsule::CreateElement ('meta', ['name' => "viewport", 'content' => "width=device-width, initial-scale=1, user-scalable=no, minimum-scale=1, maximum-scale=1"]), function ($args, CapsuleScopeContext $scope) {

      $ref8120205307erence = $scope->appMetaDatas;

      if (isset ($ref8120205307erence) && is_array ($ref8120205307erence)) {
      for ($i = 0; $i < count ($ref8120205307erence); $i++) {
        $ref8120205307 = isset ($ref8120205307erence[$i]) ? $ref8120205307erence[$i] : null;
        if (in_array (strtolower(gettype($ref8120205307)), ['array', 'object'])) {
          $ref8120205307 = \Saml::Array2Object($ref8120205307);
          $ref8120205307_props = array_keys ((array)($ref8120205307));
          if (is_object ($ref8120205307) && in_array ('Sammy\Packs\Sami\Base\ILinable', class_implements (get_class ($ref8120205307)))) {
            $ref8120205307_props = array_keys ((array)($ref8120205307->lean ()));
          }
          foreach ($ref8120205307_props as $key) {
            if (is_right_var_name ($key)) { $scope->$key = is_object ($ref8120205307) ? $ref8120205307->$key : $ref8120205307[$key]; }
          }
        }



     Capsule::PartialRender ('Fragment', function ($args, CapsuleScopeContext $scope) {if (!(isset ($scope->name) && is_string ($scope->name))) {

    Capsule::PartialRender ('meta');


    } else {

    Capsule::PartialRender ('meta', ['name' => $scope->name, 'content' => is_array ($scope->content) ? join(',', $scope->content) : $scope->content]);

    }});

    }} });
  });

  Capsule::Export ('ApplicationMetaDatas');

  $module->exports = Capsule::Element ('ApplicationMetaDatas');
}
