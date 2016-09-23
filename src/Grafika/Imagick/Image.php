<?php
namespace Grafika\Imagick;

use Grafika\ImageInterface;
use Grafika\ImageType;

/**
 * Image class for Imagick.
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
     * @var bool True if image is an animated GIF.
     */
    private $animated;

    /**
     * Image constructor.
     *
     * @param \Imagick $imagick
     * @param string $imageFile
     * @param int $width
     * @param int $height
     * @param string $type
     * @param bool $animated
     */
    public function __construct( \Imagick $imagick, $imageFile, $width, $height, $type, $animated = false ) {
        $this->imagick   = $imagick;
        $this->imageFile = $imageFile;
        $this->width     = $width;
        $this->height    = $height;
        $this->type      = $type;
        $this->animated  = $animated;
    }

    public function __clone()
    {
        $copy = clone $this->imagick;

        $this->imagick = $copy;
    }

    /**
     * Output a binary raw dump of an image in a specified format.
     *
     * @param string|ImageType $type Image format of the dump.
     *
     * @throws \Exception When unsupported type.
     */
    public function blob( $type = 'PNG' ) {
        $this->imagick->setImageFormat($type);
        echo $this->imagick->getImageBlob();
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
        $animated = false;
        if ($imagick->getImageIterations() > 0) {
            $animated = true;
        }

        return new self(
            $imagick,
            $imageFile,
            $imagick->getImageWidth(),
            $imagick->getImageHeight(),
            $imagick->getImageFormat(),
            $animated
        );
    }

    /**
     * Create an Image from an instance of Imagick.
     *
     * @param \Imagick $imagick Instance of Imagick.
     *
     * @return Image
     */
    public static function createFromCore( $imagick ) {
        return new self( $imagick, '', $imagick->getImageWidth(), $imagick->getImageHeight(), $imagick->getImageFormat() );
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

    /**
     * Get histogram from an entire image or its sub-region.
     *
     * @param array|null $slice Array of slice information. array( array( 0,0), array(100,50)) means x,y is 0,0 and width,height is 100,50
     *
     * @return array Returns array containing RGBA bins array('r'=>array(), 'g'=>array(), 'b'=>array(), 'a'=>array())
     */
    public function histogram($slice = null)
    {

        if(null === $slice){
            $sliceX = 0;
            $sliceY = 0;
            $sliceW = $this->getWidth();
            $sliceH = $this->getHeight();
        } else {
            $sliceX = $slice[0][0];
            $sliceY = $slice[0][1];
            $sliceW = $slice[1][0];
            $sliceH = $slice[1][1];
        }

        $rBin = array();
        $gBin = array();
        $bBin = array();
        $aBin = array();

        // Loop using image
        $pixelIterator = $this->getCore()->getPixelIterator();
        foreach ($pixelIterator as $y => $rows) { /* Loop through pixel rows */
            if($y >= $sliceY and $y < $sliceY+$sliceH) {
                foreach ($rows as $x => $px) { /* Loop through the pixels in the row (columns) */
                    if($x >= $sliceX and $x < $sliceX+$sliceW) {
                        /**
                         * @var $px \ImagickPixel */
                        $pixel = $px->getColor();
                        $r = $pixel['r'];
                        $g = $pixel['g'];
                        $b = $pixel['b'];
                        $a = $pixel['a'];

                        if ( ! isset($rBin[$r])) {
                            $rBin[$r] = 1;
                        } else {
                            $rBin[$r]++;
                        }

                        if ( ! isset($gBin[$g])) {
                            $gBin[$g] = 1;
                        } else {
                            $gBin[$g]++;
                        }

                        if ( ! isset($bBin[$b])) {
                            $bBin[$b] = 1;
                        } else {
                            $bBin[$b]++;
                        }

                        if ( ! isset($aBin[$a])) {
                            $aBin[$a] = 1;
                        } else {
                            $aBin[$a]++;
                        }
                    }
                }
            }
        }
        return array(
            'r' => $rBin,
            'g' => $gBin,
            'b' => $bBin,
            'a' => $aBin
        );
    }

    /**
     * Returns animated flag.
     *
     * @return bool True if animated GIF.
     */
    public function isAnimated() {
        return $this->animated;
    }

}