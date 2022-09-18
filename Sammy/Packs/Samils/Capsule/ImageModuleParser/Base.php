<?php
/**
 * @version 2.0
 * @author Sammy
 *
 * @keywords Samils, ils, php framework
 * -----------------
 * @package Sammy\Packs\Samils\Capsule\ImageModuleParser
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
namespace Sammy\Packs\Samils\Capsule\ImageModuleParser {
  use FileSystem\Folder as Directory;
  use App\View\Capsule;
  /**
   * Make sure the module base internal class is not
   * declared in the php global scope defore creating
   * it.
   * It ensures that the script flux is not interrupted
   * when trying to run the current command by the cli
   * API.
   */
  if (!class_exists('Sammy\Packs\Samils\Capsule\ImageModuleParser\Base')){
  /**
   * @class Base
   * Base internal class for the
   * Samils\Capsule\ImageModuleParser module.
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
  abstract class Base {
    /**
     * @method array description
     *
     */
    public static function GetFilePublicPaths (string $file) {
      $filePath = Capsule::FileAbsolutePath (
        Capsule::StripRootPath ($file)
      );

      $filePublicPath = join (DIRECTORY_SEPARATOR, [
        __public__, 'assets', 'cache', 'images',
        $filePath
      ]);

      $filePublicUrl = join ('/', array_merge (
        ['', 'assets', 'cache', 'images'],
        preg_split ('/(\/|\\\)+/', $filePath)
      ));

      return [
        $filePublicPath,
        self::OptimizeImageFromPath (
          $file, $filePublicUrl
        )
      ];
    }

    /**
     * @method void CopyFileToPublicPath
     *
     */
    public static function CopyFileToPublicPath ($file) {

      list ($filePublicPath, $filePublicUrl) = ( array )(
        self::GetFilePublicPaths ($file)
      );

      # copy image to public/assets/cache/images
      if (is_file ($filePublicPath)) {
        unlink ($filePublicPath);
      }

      Directory::Create (dirname ($filePublicPath));

      @copy ($file, $filePublicPath);
      # end

      return (array) ([$filePublicUrl, $filePublicPath]);
    }

    /**
     * @method string OptimizeImageFromPath
     *
     */
    public static function OptimizeImageFromPath ($path, $alternatePath = null) {

      $alternatePathGiven = ( boolean ) (
        is_string ($alternatePath) &&
        !empty ($alternatePath)
      );

      if ( !$alternatePathGiven ) {
        $alternatePath = $path;
      }

      if (!(is_file ($path) && $imageType = self::isImage ($path))) {
        return ( string )( $alternatePath );
      }

      #exit ('type => ' . $imageType);

      if (is_string ($imageType) && in_array ($imageType, self::EmbedImageTypes ())) {
        return $alternatePath;
      }

      $fs = requires ('fs');

      $imageExtension = pathinfo ($path, 4);
      # verify image size before to compress it
      # end

      return join (',', [
        'data:image/'.$imageExtension.';base64',
        base64_encode ($fs->readFile ($path))
      ]);
    }

    /**
     * @method array EmbedImageTypes
     */
    public static function EmbedImageTypes () {
      return ['svg'];
    }

    /**
     * @method boolean|string MayParseImageType
     */
    public static function MayParseImageType ($imageExtension = '') {
      if (!(is_string ($imageExtension) && $imageExtension)) {
        return false;
      }

      $imageParserName = join ('', [
        'Parse', strtoupper ($imageExtension), 'Image'
      ]);

      if (method_exists (new static, $imageParserName)) {
        return ( string )( $imageParserName );
      }

      return false;
    }

    /**
     * @method boolean|string MayParseImage
     */
    public static function MayParseImage ($imagePath = '') {
      $imageExtension = pathinfo ($imagePath, 4);
      return self::MayParseImageType ($imageExtension);
    }

    /**
     * @method string ParseImage
     */
    public static function ParseImage ($imagePath = null) {
      if (!$parser = self::MayParseImage ($imagePath)) {
        return;
      }

      return call_user_func_array (
        [new static, $parser], [$imagePath]
      );
    }

    /**
     * @method boolean isImage
     *
     * Verify if a given imagePath refers to an image.
     *
     */
    private static function isImage ($imagePath = null) {
      if (!(is_string ($imagePath) && is_file ($imagePath))) {
        return false;
      }

      $isAnyOtherSupportedImageFile = function ($imagePath) {
        $imageExtension = strtolower (pathinfo ($imagePath, 4));
        $supportedExtensions = ['svg'];

        if (in_array ($imageExtension, $supportedExtensions)) {
          return $imageExtension;
        }

        return false;
      };

      $imageType = exif_imagetype ($imagePath);

      if ((is_int ($imageType) && $imageType >= 1)) {
        return true;
      }

      return $isAnyOtherSupportedImageFile ($imagePath);
    }
  }}
}
