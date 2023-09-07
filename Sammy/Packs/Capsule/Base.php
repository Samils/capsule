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
     * @param  array $options
     * - View [Engine] Datas
     */
    public static function RenderDOM ($view = '', $options = []) {
      if (!(is_string ($view) && is_file ($view))) {
        return;
      }

      ob_start ();

      $options = !is_array ($options) ? [] : $options;
      $responseData = !isset ($options ['responseData']) ? [] : [
        'data' => $options [ 'responseData' ]
      ];

      $viewInitialProps = ArrayHelper::PropsBeyond (
        [
          'layout',
          'responseData',
          'viewsDir',
          'action'
        ],
        $options
      );

      $templateRelativePath = $options ['templateRelativePath'];

      $responseData = array_merge (
        $viewInitialProps,
        $responseData
      );

      $viewsDir = !isset ($options ['viewsDir']) ? __views__ : (
        $options ['viewsDir']
      );

      # ob_flush ();

      if (!(ob_get_length () >= 1)) {
        header ('content-type: text/html');
      }

      if (isset ($options ['layoutPath'])
        && is_string ($options ['layoutPath'])
        && is_file ($options ['layoutPath'])) {
        $layoutPath = $options ['layoutPath'];
      }

      if (!isset ($layoutPath)) {
        $layoutsDir = (string)($viewsDir . '/layouts/');

        $layoutPath = self::getTemplateLayoutName (dirname ($templateRelativePath), $layoutsDir);
        $viewCore = \php\requires ($view);

        if (!$layoutPath && isset ($options ['layout']) && is_string ($options ['layout']) && !empty ($options ['layout'])) {
          $layoutPath = $layoutsDir . str ($options ['layout']) . '.cache.php';
        }

        # end layout resolve

        # $layoutPath = !isset ($options ['layout']) ? 'application' : (
        #   str ($options ['layout'])
        # );
      }

      $layoutCore = requires ($layoutPath);

      #exit ($viewsDir.'/layouts/'.$layoutPath.'.cache.php');

      Capsule::Config ($options);

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
        exit ('Error => No Layout for ' . $layoutPath);
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
