<?php
/**
 * @version 2.0
 * @author Sammy
 *
 * @keywords Samils, ils, php framework
 * -----------------
 * @package Sammy\Packs\Capsule
 * - Autoload, application dependencies
 */
namespace Sammy\Packs\Capsule {
  use App\View\Capsule;
  use Sammy\Packs\Sami\Debugger;
  use Sammy\Packs\CapsuleHelper\ArrayHelper;
  use Sammy\Packs\Samils\Capsule\CapsuleCoreDOM;
  use Sammy\Packs\Samils\Capsule\CapsuleVirtualDOM;
  /**
   * Make sure the module base internal trait is not
   * declared in the php global scope defore creating
   * it.
   */
  if (!trait_exists ('Sammy\Packs\Capsule\Base')) {
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
     * [RenderDOM description]
     * @param string $view
     * - Absolute path for the current view
     * @param  array $datas
     * - View [Engine] Datas
     */
    public static function RenderDOM ($view = '', $datas = []) {
      if (!(is_string ($view) && is_file ($view))) {
        return;
      }

      ob_start ();

      $datas = !is_array ($datas) ? [] : $datas;
      $responseData = !isset ($datas ['responseData']) ? [] : [
        'data' => $datas [ 'responseData' ]
      ];

      $viewInitialProps = ArrayHelper::PropsBeyond (
        [
          'layout',
          'responseData',
          'viewsDir',
          'action'
        ],
        $datas
      );

      $templateRelativePath = $datas ['templateRelativePath'];

      $responseData = array_merge (
        $viewInitialProps,
        $responseData
      );

      $viewsDir = !isset ($datas ['viewsDir']) ? __views__ : (
        $datas ['viewsDir']
      );

      $layoutsDir = (string)($viewsDir . '/layouts/');

      # ob_flush ();

      if (!(ob_get_length () >= 1)) {
        header ('content-type: text/html');
      }

      $layoutName = self::getTemplateLayoutName (dirname ($templateRelativePath), $layoutsDir);
      $viewCore = \php\requires ($view);

      if (!$layoutName && isset ($datas ['layout']) && is_string ($datas ['layout']) && !empty ($datas ['layout'])) {
        $layoutName = $layoutsDir . str ($datas ['layout']) . '.cache.php';
      }

      # $layoutName = !isset ($datas ['layout']) ? 'application' : (
      #   str ($datas ['layout'])
      # );

      $layoutCore = requires ($layoutName);

      #exit ($viewsDir.'/layouts/'.$layoutName.'.cache.php');

      Capsule::Config ($datas);

      if ($layoutCore instanceof Capsule) {
        # Get the Rendering Map of the current component
        # in order getting the complete dom tree for the
        # current context.
        #$componentRenderMap = $layoutCore->render (
        #  null, $responseData, $viewCore
        #);
        $mainComponentDataObject = call_user_func_array (
          [$layoutCore, 'render'],
          [
            null,
            $responseData,
            $viewCore
          ]
        );

        $bufferData = ob_get_clean ();

        Debugger::log (trim (preg_replace ("/\n+/", "\n", $bufferData)));

        ## $res = new \Sammy\Packs\HTTP\Response;
        ## $data = $mainComponentDataObject;
        #print_r ();
        # exit ('</pre><br><br><br><br><br><br>UA');
        ## $res->end ($data);
        # exit ();

        $virtualDom = new CapsuleVirtualDOM ($mainComponentDataObject);

        CapsuleCoreDOM::Render ($virtualDom);
      } else {
        exit ('Error => No Layout for ' . $layoutName);
      }

      exit (0);
    }

    /**
     * @method string
     *
     * getTemplateLayoutName
     */
    protected static function getTemplateLayoutName ($templateDirRelativePath, $layoutsDir) {
      $templateDirRelativePath = join (DIRECTORY_SEPARATOR, [$templateDirRelativePath, 'index']);
      $templateDirRelativePathSlices = preg_split ('/[\/\\\\]+/', $templateDirRelativePath);
      $templateDirRelativePathSlicesCount = -1 + count ($templateDirRelativePathSlices);

      for (; $templateDirRelativePathSlicesCount >= 0; $templateDirRelativePathSlicesCount--) {
        $currentRelativePath = join (DIRECTORY_SEPARATOR, array_slice ($templateDirRelativePathSlices, 0, $templateDirRelativePathSlicesCount + 1));

        $alternateLayoutPath = join (DIRECTORY_SEPARATOR, [$layoutsDir, $currentRelativePath]);

        if (is_file ($alternateLayoutFilePath = join ('.', [$alternateLayoutPath, 'cache.php']))) {
          return realpath ($alternateLayoutFilePath);
        }
      }

    }
  }}
}
