<?php
/**
 * @version 2.0
 * @author Sammy
 *
 * @keywords Samils, ils, php framework
 * -----------------
 * @package Sammy\Packs\Samils\Capsule\CapsuleVirtualDOM
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
namespace Sammy\Packs\Samils\Capsule\CapsuleVirtualDOM {
  /**
   * Make sure the module base internal trait is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!trait_exists ('Sammy\Packs\Samils\Capsule\CapsuleVirtualDOM\Base')) {
  /**
   * @trait Base
   * Base internal trait for the
   * Samils\Capsule\CapsuleVirtualDOM module.
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
     * @var array virtualDomSekeleton
     */
    private $virtualDomSekeleton = [
      'element' => 'html',
      'props' => [],

      'children' => [
        [ # head
          'element' => 'head',
          'props' => [],

          'children' => [
            [ # title
              'element' => 'title',
              'props' => [],

              'children' => [
                [
                  'element' => 'Text',
                  'props' => [],

                  'content' => ""
                ]
              ]
            ]
          ]
        ],

        [
          'element' => 'body',
          'props' => [],

          'children' => []
        ]
      ]
    ];

    /**
     * @var array dom map
     */
    private $map;

    /**
     * @method void constructor
     */
    public function __construct (array $domMap) {
      $this->map = $domMap;

      $titleElements = $this->getElementsByTagName ('title');
      $this->map = $this->removeElementsByTagName ('title');

      $headElements = $this->getElementsByTagName ('head');
      $this->map = $this->removeElementsByTagName ('head');

      if (count ($titleElements) >= 1) {
        $lastTitleElement = $titleElements [-1 + count ($titleElements)];

        if (isset ($lastTitleElement ['children'])) {
          $this->virtualDomSekeleton ['children'][/*head*/0]['children'][/*title*/0]['children'] = $lastTitleElement ['children'];
        }
      }

      foreach ($headElements as $headElement) {
        $this->virtualDomSekeleton ['children'][/*head*/0]['children'] = array_merge (
          $this->virtualDomSekeleton ['children'][/*head*/0]['children'],
          $this->removeElementsByTagName ('head', $headElement ['children'])
        );
      }

      #echo '<pre>';
      echo json_encode($this->map);

      exit (0);
    }

    /**
     * @method array getElementsByTagName
     */
    function getElementsByTagName ($elementName, $map = null) {

      $map = is_array ($map) ? $map : $this->map;

      if (!(is_array ($map) && $map)) {
        return [];
      }

      $elements = [];
      $elementPropName = isset ($map ['component']) ? 'component' : (isset ($map ['element']) ? 'element' : null);

      if ($elementPropName === 'element' && $map [$elementPropName] === $elementName) {
        array_push ($elements, $map);
      }

      $elementChildren = (isset ($map ['children']) && is_array ($map ['children'])) ? $map ['children'] : $map;

      foreach ($elementChildren as $elementChild) {
        if (!(is_array ($elementChild) && $elementChild)) {
          continue;
        }

        $foundResltsInElementChild = $this->getElementsByTagName ($elementName, $elementChild);

        if (is_array ($foundResltsInElementChild)) {
          $elements = array_merge ($elements, $foundResltsInElementChild);
        }
      }

      return $elements;
    }

    /**
     * @method array removeElementsByTagName
     */
    function removeElementsByTagName ($elementName, $map = null) {
      $map = is_array ($map) ? $map : $this->map;

      if (!(is_array ($map) && $map)) {
        return [];
      }

      $refPropName = $this->getRefPropName ($map);

      if (isset ($map [$refPropName]) && $map [$refPropName] == $elementName) {
        return null;
      }

      if (isset ($map ['children'])) {
        foreach ($map ['children'] as $i => $child) {
          if (is_array ($child)) {
            $map ['children'][$i] = $this->removeElementsByTagName ($elementName, $child);
          } elseif (!is_null ($child)) {
            $map ['children'][$i] = $child;
          }
        }

        # $map ['children'] =
      } else {
        foreach ($map as $i => $component) {
          if (is_array ($component)) {
            $map [$i] = $this->removeElementsByTagName ($elementName, $component);
          } elseif (!is_null ($component)) {
            $map [$i] = $component;
          }
        }
      }

      return $map;
    }



    private function getElementContent ($map) {
      return (isset ($map ['children']) && is_array ($map ['children'])) ? $map ['children'] : $map;
    }

    private function getRefPropName ($map) {
      return isset ($map ['component']) ? 'component' : (isset ($map ['element']) ? 'element' : null);
    }
  }}
}
