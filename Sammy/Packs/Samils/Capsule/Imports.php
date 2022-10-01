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
  /**
   * Make sure the module base internal trait is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!trait_exists ('Sammy\Packs\Samils\Capsule\Imports')) {
  /**
   * @trait Imports
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
  trait Imports {

    public static function Import ($capsule, $file = null) {
      $capsuleFile = self::register (debug_backtrace());

      $capsuleCore = \php\requires (str($file));

      #print_r($capsuleCore);
      #exit (0);

      if ( !(is_string ($capsule) && $capsule) ) {

        if (is_array ($capsule)) {
          /**
          * [$key description]
          * @var string
          */
          foreach ($capsule as $capsuleName => $as) {
            if (is_array ($as)) {
              /**
              * Import The given Capsule Collection
              * As the given context Name
              */
              self::CollectionImport2fromFile ($capsuleCore,
              $capsuleFile, $as, $capsuleName
              );
            } else {
              /**
              * Import The given Capsule Name
              */
              self::Import2FromFile (
                $capsuleCore,
                $capsuleFile,
                $capsuleName,
                $as
              );
            }
          }

          return;
        }

        exit ('Capsule name is not a string');
      }

      self::Import2FromFile ($capsuleCore, $capsuleFile, $capsule, $capsule);
    }


    private static function CollectionImport2fromFile (
    $capsuleCore, $capsuleFile, $capsuleNames, $as) {

      if (!self::isCapsule ($capsuleCore)) {
        return;
      }


      #exit (gettype($as) . ' => ' . gettype($capsuleNames));

      $capsuleExports = @self::$files [$capsuleCore->fileName][
        'exports'
      ];

      $finalContext = array ();

      foreach ($capsuleNames as $key => $value) {
        /**
        * Alternate
        */
        if (is_int($key)) {
          $value = str ($value);

          if (isset($capsuleExports[$value])) {
            $finalContext [$value] = $capsuleExports [$value];
          } else {
            exit ("Error, capsule $value not exportd from {$capsuleCore->fileName}");
          }

        } else {
          if (isset($capsuleExports[$key])) {
            $finalContext [$value] = $capsuleExports [$key];
          } else {
            exit ("Error, capsule $key not exportd from {$capsuleCore->fileName}");
          }
        }
      }



      self::$files[$capsuleFile]['scope'][ $as ] = (
        $finalContext
      );
    }

    private static function Import2FromFile ($capsuleCore, $capsuleFile, $capsule, $as) {

      if ( self::isCapsule ($capsuleCore) ) {

        $capsuleExports = @self::$files [$capsuleCore->fileName][
          'exports'
        ];

        $genericImport = ( boolean ) (
          in_array (trim($capsule), ['*']) || (
            preg_match ('/^\*([a-zA-Z0-9_]+)$/',
              $capsule,
              $match
            )
          )
        );

        if ( $genericImport ) {
          $capsule = trim ($capsule);
          $as = trim ($as);

          if ($as === $capsule) {

            if ('*' === $capsule) {
              /**
              * Merge the current exports array to the new
              */
              self::$files[$capsuleFile]['scope'] = array_merge (
                self::$files[$capsuleFile]['scope'],
                $capsuleExports
              );
            } else {

              $as = preg_replace ('/^\*/', '', $as);

              self::$files[$capsuleFile]['scope'][ $as ] = (
                $capsuleExports
              );
            }
          } elseif ($capsule === '*') {
            /**
            * Import * from CapsuleRef
            */
            self::$files[$capsuleFile]['scope'][ $as ] = (
              $capsuleExports
            );
          }

          return;
        }

        if (is_array($capsuleExports) && isset ($capsuleExports[$capsule])) {
          #$capsule2import = $capsuleDatas[$capsule];

          self::$files[$capsuleFile]['scope'][$as] = (
            $capsuleExports[$capsule]
          );
        } else {
          self::$files[$capsuleFile]['scope'][$as] = $capsuleCore;
        }

      } else {
        #exit ('Error: \'' . $capsule . '\' is not a Capsule');
        $re = '/^\$([a-zA-Z0-9_]+)/';

        #exit (gettype($capsuleCore));

        if (preg_match ($re, $capsule, $capsuleNameMatch)) {
          $propName = trim ($capsuleNameMatch [1]);

          #exit ('prop => ' . $propName);
          #print_r($capsuleCore); exit (0);
          self::setCapsuleProps ($capsuleFile);

          self::$files[$capsuleFile]['props'][ $propName ] = (
            $capsuleCore
          );
        }
      }
    }

  }}
}
