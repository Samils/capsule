<?php
/**
 * @version 2.0
 * @author Sammy
 *
 * @keywords Samils, ils, php framework
 * -----------------
 * @package Sammy\Packs\Samils\Capsule\Config
 * - Autoload, application dependencies
 */
namespace Sammy\Packs\Samils\Capsule\Config {
  /**
   * Make sure the module base internal class is not
   * declared in the php global scope defore creating
   * it.
   */
  if (!trait_exists('Sammy\Packs\Samils\Capsule\Config\Base')){
  /**
   * @trait Base
   * Base internal trait for the
   * Capsule\Config module.
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
     * [$config]
     * @var array
     */
    private static $config = array ();
    /**
     * [Config description]
     * @param array $configDatas
     */
    public static function Config ($configDatas = []) {
      $configDatas = !is_array($configDatas) ? [] : (
        $configDatas
      );

      self::$config = array_merge (self::$config,
        $configDatas
      );
    }

    private static function viewsDir () {
      $viewsDirDefined = ( boolean ) (
        isset (self::$config ['viewsDir']) &&
        is_string (self::$config ['viewsDir']) &&
        is_dir (self::$config ['viewsDir'])
      );

      return !$viewsDirDefined ? __views__ : (
        self::$config ['viewsDir']
      );
    }

    private static function getBackTraceDir () {
      $backTrace = debug_backtrace ();

      #$traceDatas = $backTrace [1];

      return dirname ($backTrace [1]['file']);
    }

    private static function factory () {
      return array (
        'exports' => array (),
        'scope' => array (),
        'props' => array ()
      );
    }

    private static function register ($backTrace) {
      $trace0 = $backTrace [0];
      $file = $trace0['file'];

      if (!isset (self::$files[ $file ])) {
        self::$files[$file] = self::factory ();
      }

      return $file;
    }
  }}
}
