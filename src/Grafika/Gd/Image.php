<?php
namespace Grafika\Gd;

use Grafika\Gd\Helper\GifByteStream;
use Grafika\Gd\Helper\GifHelper;
use Grafika\ImageType;
use Grafika\ImageInterface;

/**
 * Image class for GD.
 * @package Grafika\Gd
 */
final class Image implements ImageInterface {

    /**
     * @var resource GD resource ID.
     */
    private $gd;

    /**
     * @var string File path to image.
     */
    private $imageFile;

    /**
     * @var int Image width in pixels.
     */
    private $width;

    /**
     * @var int Image height in pixels.
     */
    private $height;

    /**
     * @var string Image type. See \Grafika\ImageType
     */
    private $type;

    /**
     * @var string Contains array of animated GIF data.
     */
    private $blocks;

    /**
     * @var bool True if animated GIF.
     */
    private $animated;

    /**
     * Image constructor.
     *
     * @param resource $gd Must use GD's imagecreate* family of functions to create a GD resource.
     * @param string $imageFile
     * @param int $width
     * @param int $height
     * @param string $type
     * @param string $blocks
     * @param bool $animated
     */
    public function __construct( $gd, $imageFile, $width, $height, $type, $blocks = '', $animated = false ) {
        $this->gd         = $gd;
        $this->imageFile  = $imageFile;
        $this->width      = $width;
        $this->height     = $height;
        $this->type       = $type;
        $this->blocks        = $blocks;
        $this->animated = $animated;
    }

    /**
     * Method called when 'clone' keyword is used.
     */
    public function __clone()
    {
        $original = $this->gd;
        $copy = imagecreatetruecolor($this->width, $this->height);

        imagecopy($copy, $original, 0, 0, 0, 0, $this->width, $this->height);

        $this->gd = $copy;
    }

    /**
     * @param $imageFile
     *
     * @return Image
     * @throws \Exception
     */
    public static function createFromFile( $imageFile ){
        if ( ! file_exists( $imageFile ) ) {
            throw new \Exception( sprintf('Could not open "%s". File does not exist.', $imageFile) );
        }

        $type = self::_guessType($imageFile);
        if ( ImageType::GIF == $type) {

            return self::_createGif($imageFile);

        } else if ( ImageType::JPEG == $type) {

            return self::_createJpeg($imageFile);

        } else if ( ImageType::PNG == $type) {

            return self::_createPng($imageFile);

        } else if ( ImageType::WBMP == $type) {

            return self::_createWbmp($imageFile);

        } else {
            throw new \Exception( sprintf('Could not open "%s". File type not supported.', $imageFile) );
        }
    }



    /**
     * Create a blank image.
     *
     * @param int $width Width in pixels.
     * @param int $height Height in pixels.
     *
     * @return Image
     */
    public static function createBlank($width = 1, $height = 1){

        return new self(imagecreatetruecolor($width, $height), '', $width, $height, ImageType::UNKNOWN);

    }

    /**
     * Set the blending mode for an image. Allows transparent overlays on top of an image.
     *
     * @param bool $flag True to enable blending mode.
     * @return self
     */
    public function alphaBlendingMode( $flag ){
        imagealphablending( $this->gd, $flag );

        return $this;
    }


    /**
     * Flatten if animated GIF. Do nothing otherwise.
     */
    public function flatten(){
        if($this->animated) {
            $gift = new GifHelper();
            $hex  = $gift->encode($this->blocks);
            $gd   = imagecreatefromstring(pack('H*', $hex)); // Recreate resource from blocks

            $this->animated = false;
            $this->gd       = $gd;
            $this->blocks   = '';
        }
    }

    /**
     * Enable/Disable transparency
     *
     * @param bool $flag True to enable alpha mode.
     * @return self
     */
    public function fullAlphaMode( $flag ){
        if( true === $flag ){
            $this->alphaBlendingMode( false ); // Must be false for full alpha mode to work
        }
        imagesavealpha( $this->gd, $flag );

        return $this;
    }

    /**
     * Returns animated flag.
     *
     * @return bool True if animated GIF.
     */
    public function isAnimated() {
        return $this->animated;
    }

    /**
     * Get GD resource ID.
     *
     * @return resource
     */
    public function getCore() {
        return $this->gd;
    }

    /**
     * Get image file path.
     *
     * @return string File path to image.
     */
    public function getImageFile() {
        return $this->imageFile;
    }

    /**
     * Get image width in pixels.
     *
     * @return int
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     * Get image height in pixels.
     *
     * @return int
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * Get image type.
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Get blocks.
     *
     * @return string.
     */
    public function getBlocks() {
        return $this->blocks;
    }

    /**
     * Load a GIF image.
     *
     * @param string $imageFile
     *
     * @return Image
     * @throws \Exception
     */
    private static function _createGif( $imageFile ){
        $gift = new GifHelper();
        $bytes = $gift->open($imageFile);
        $animated = $gift->isAnimated($bytes);
        $blocks = '';
        if($animated){
            $blocks = $gift->decode($bytes);
        }
        $gd = @imagecreatefromgif( $imageFile );

        if(!$gd){
            throw new \Exception( sprintf('Could not open "%s". Not a valid %s file.', $imageFile, ImageType::GIF) );
        }

        return new self(
            $gd,
            $imageFile,
            imagesx( $gd ),
            imagesy( $gd ),
            ImageType::GIF,
            $blocks,
            $animated
        );
    }

    /**
     * Load a JPEG image.
     *
     * @param string $imageFile File path to image.
     *
     * @return Image
     * @throws \Exception
     */
    private static function _createJpeg( $imageFile ){
        $gd = @imagecreatefromjpeg( $imageFile );

        if(!$gd){
            throw new \Exception( sprintf('Could not open "%s". Not a valid %s file.', $imageFile, ImageType::JPEG ) );
        }

        return new self( $gd, $imageFile, imagesx( $gd ), imagesy( $gd ), ImageType::JPEG );
    }

    /**
     * Load a PNG image.
     *
     * @param string $imageFile File path to image.
     *
     * @return Image
     * @throws \Exception
     */
    private static function _createPng( $imageFile ){
        $gd = @imagecreatefrompng( $imageFile );

        if(!$gd){
            throw new \Exception( sprintf('Could not open "%s". Not a valid %s file.', $imageFile, ImageType::PNG) );
        }

        $image = new self( $gd, $imageFile, imagesx( $gd ), imagesy( $gd ), ImageType::PNG );
        $image->fullAlphaMode( true );
        return $image;
    }

    /**
     * Load a WBMP image.
     *
     * @param string $imageFile
     *
     * @return Image
     * @throws \Exception
     */
    private static function _createWbmp( $imageFile ){
        $gd = @imagecreatefromwbmp( $imageFile );

        if(!$gd){
            throw new \Exception( sprintf('Could not open "%s". Not a valid %s file.', $imageFile, ImageType::WBMP) );
        }

        return new self( $gd, $imageFile, imagesx( $gd ), imagesy( $gd ), ImageType::WBMP );
    }

    /**
     * @param $imageFile
     *
     * @return string
     */
    private static function _guessType( $imageFile ){
        // Values from http://php.net/manual/en/image.constants.php starting with IMAGETYPE_GIF.
        // 0 - unknown,
        // 1 - GIF,
        // 2 - JPEG,
        // 3 - PNG
        // 15 - WBMP
        list($width, $height, $type) = getimagesize( $imageFile );

        unset($width, $height);

        if ( 1 == $type) {

            return ImageType::GIF;

        } else if ( 2 == $type) {

            return ImageType::JPEG;

        } else if ( 3 == $type) {

            return ImageType::PNG;

        } else if ( 15 == $type) {

            return ImageType::WBMP;

        }

        return ImageType::UNKNOWN;
    }
}