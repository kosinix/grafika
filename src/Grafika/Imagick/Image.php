<?php
namespace Grafika\Imagick;

use Grafika\ImageInterface;

/**
 * Immutable image class for Imagick.
 * @package Grafika\Gd
 */
final class Image implements ImageInterface {

    /**
     * @var \Imagick Imagick instance
     */
    private $imagick;

    /**
     * @var string File path to image
     */
    private $imageFile;

    /**
     * @var int Image width in pixels
     */
    private $width;

    /**
     * @var int Image height in pixels
     */
    private $height;

    /**
     * @var string Image type. Return value of Imagick::queryFormats(). See http://phpimagick.com/Imagick/queryFormats
     * Sample values: JPEG, PNG, GIF, WBMP
     */
    private $type;

    /**
     * Image constructor.
     *
     * @param \Imagick $imagick
     * @param string $imageFile
     * @param int $width
     * @param int $height
     * @param string $type
     */
    public function __construct( \Imagick $imagick, $imageFile, $width, $height, $type ) {
        $this->imagick   = $imagick;
        $this->imageFile = $imageFile;
        $this->width     = $width;
        $this->height    = $height;
        $this->type      = $type;
    }

    public function __clone()
    {
        $copy = clone $this->imagick;

        $this->imagick = $copy;
    }
    
    /**
     * @param $imageFile
     *
     * @return Image
     * @throws \Exception
     */
    public static function createFromFile( $imageFile ){
        $imageFile = realpath( $imageFile );

        if ( ! file_exists( $imageFile ) ) {
            throw new \Exception( sprintf('Could not open image file "%s"', $imageFile) );
        }

        $imagick = new \Imagick( realpath($imageFile) );
        return new self( $imagick, $imageFile, $imagick->getImageWidth(), $imagick->getImageHeight(), $imagick->getImageFormat());
    }

    /**
     * Create a blank image.
     *
     * @param int $width Width in pixels.
     * @param int $height Height in pixels.
     *
     * @return self
     */
    public static function createBlank($width = 1, $height = 1){
        $imagick = new \Imagick();
        $imagick->newImage($width, $height, new \ImagickPixel('black'));
        $imagick->setImageFormat('png'); // Default to PNG.

        return new self( $imagick, '', $imagick->getImageWidth(), $imagick->getImageHeight(), $imagick->getImageFormat());

    }

    /**
     * Get Imagick instance
     *
     * @return \Imagick
     */
    public function getCore() {
        return $this->imagick;
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

}