<?php
/**
 * @version 2.0
 * @author Sammy
 *
 * @keywords Samils, ils, php framework
 * -----------------
 * @package Sammy\Packs\Samils\Capsule\CapsuleGlobalContext
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
namespace Sammy\Packs\Samils\Capsule\CapsuleGlobalContext {
  /**
   * Make sure the module base internal trait is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!trait_exists ('Sammy\Packs\Samils\Capsule\CapsuleGlobalContext\Base')) {
  /**
   * @trait Base
   * Base internal trait for the
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
  trait Base {
    /**
     * @var array
     *
     * The capsule global context store
     */
    private static $store = [];

    /**
     * @var array
     *
     * The capsule providers list
     */
    private static $providers = [];

    /**
     * @method string
     *
     * add a new provider to the capsule global context
     */
    public static function addProvider (object $provider, string $providerKey = null) {
      if (is_null ($providerKey)) {
        $providerKey = self::generateProviderKey ();
      }

      self::$providers [$providerKey] = $provider;

      return $providerKey;
    }

    /**
     * @method void
     *
     * remove a provider from the capsule global context by
     * the provider key
     */
    public static function removeProvider (string $providerKey) {
      if (isset (self::$providers [$providerKey])) {
        unset (self::$providers [$providerKey]);
      }
    }

    /**
     * @method void
     *
     * setter
     */
    public static function set (string $property, $value = null) {
      $property = self::formatPropertyName ($property);

      self::$store [$property] = $value;

      return $value;
    }

    /**
     * @method mixed
     *
     * getter
     */
    public static function get (string $property) {
      $formattedPropertyName = self::formatPropertyName ($property);

      if (isset (self::$store [$formattedPropertyName])) {
        return self::$store [$formattedPropertyName];
      }

      foreach (self::$providers as $key => $provider) {
        if (is_object ($provider) && isset ($provider->$property)) {
          return $provider->$property;
        }
      }
    }

    /**
     * @method boolean
     *
     * verify if a given property is set in the capsule global scope
     */
    public static function isset (string $property) {
      $formattedPropertyName = self::formatPropertyName ($property);

      if (isset (self::$store [$formattedPropertyName])) {
        return true;
      }

      foreach (self::$providers as $key => $provider) {
        if (is_object ($provider) && isset ($provider->$property)) {
          return true;
        }
      }

      return false;
    }

    /**
     * @method string
     *
     * format capsule global context store property name
     *
     * Todos:
     *  * convert to lowercase
     *  * remove whole the whitespaces
     */
    private static function formatPropertyName (string $propertyName) {
      $propertyName = preg_replace ('/\s+/', '', $propertyName);

      return strtolower ($propertyName);
    }

    /**
     * @method string
     *
     * generate a provider key
     */
    private static function generateProviderKey () {
      return base64_encode (random_bytes (12));
    }
  }}
}
