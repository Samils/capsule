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

  include_once dirname (__DIR__) . '/autoload.php';
	/**
	 * Make sure the module base internal class is not
	 * declared in the php global scope defore creating
	 * it.
	 */
	if (!class_exists ('Sammy\Packs\Capsule\Base')) {
	/**
	 * @class Base
	 * Base internal class for the
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
	class Base {
		/**
		 * [RenderDOM description]
		 * @param string $view
		 * - Absolute path for the current view
     * @param  array $datas
     * - View [Engine] Datas
		 */
		public static final function RenderDOM ($view = '', $datas = []) {
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

      $responseData = array_merge (
        $viewInitialProps,
        $responseData
      );

      $viewsDir = !isset ($datas ['viewsDir']) ? __views__ : (
        $datas ['viewsDir']
      );

      # ob_flush ();

			if (!(ob_get_length () >= 1)) {
        header ('content-type: text/html');
      }

			$viewCore = \php\requires ($view);

      $layoutName = !isset ($datas ['layout']) ? 'application' : (
        str ($datas ['layout'])
      );

			$layoutCore = \php\requires (
        $viewsDir.'/layouts/'.$layoutName.'.cache.php'
      );

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
	}}

	$module->exports = (
		new Base
	);
}
