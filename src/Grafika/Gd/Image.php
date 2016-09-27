<?php
namespace Grafika\Gd;

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
     * Output a binary raw dump of an image in a specified format.
     *
     * @param string|ImageType $type Image format of the dump.
     *
     * @throws \Exception When unsupported type.
     */
    public function blob( $type = 'PNG' ) {

        $type = strtoupper($type);
        if ( ImageType::GIF == $type ) {

            imagegif( $this->gd );

        } else if ( ImageType::JPEG == $type ) {

            imagejpeg( $this->gd );

        } else if ( ImageType::PNG == $type ) {

            imagepng( $this->gd );

        } else if ( ImageType::WBMP == $type ) {

            imagewbmp( $this->gd );

        } else {
            throw new \Exception( sprintf( 'File type "%s" not supported.', $type ) );
        }
    }

    /**
     * Create Image from image file.
     *
     * @param string $imageFile Path to image.
     *
     * @return Image
     * @throws \Exception
     */
    public static function createFromFile( $imageFile ) {
        if ( ! file_exists( $imageFile ) ) {
            throw new \Exception( sprintf( 'Could not open "%s". File does not exist.', $imageFile ) );
        }

        $type = self::_guessType( $imageFile );
        if ( ImageType::GIF == $type ) {

            return self::_createGif( $imageFile );

        } else if ( ImageType::JPEG == $type ) {

            return self::_createJpeg( $imageFile );

        } else if ( ImageType::PNG == $type ) {

            return self::_createPng( $imageFile );

        } else if ( ImageType::WBMP == $type ) {

            return self::_createWbmp( $imageFile );

        } else {
            throw new \Exception( sprintf( 'Could not open "%s". File type not supported.', $imageFile ) );
        }
    }

    /**
     * Create an Image from a GD resource. The file type defaults to unknown.
     *
     * @param resource $gd GD resource.
     *
     * @return Image
     */
    public static function createFromCore( $gd ) {
        return new self( $gd, '', imagesx( $gd ), imagesy( $gd ), ImageType::UNKNOWN );
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
     * Get histogram from an entire image or its sub-region.
     *
     * @param array|null $slice Array of slice information. array( array( 0,0), array(100,50)) means x,y is 0,0 and width,height is 100,50
     *
     * @return array Returns array containing RGBA bins array('r'=>array(), 'g'=>array(), 'b'=>array(), 'a'=>array())
     */
    public function histogram($slice = null)
    {
        $gd = $this->getCore();

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
        for ($y = $sliceY; $y < $sliceY+$sliceH; $y++) {
            for ($x = $sliceX; $x < $sliceX+$sliceW; $x++) {
                $rgb = imagecolorat($gd, $x, $y);
                $a   = ($rgb >> 24) & 0x7F; // 127 in hex. These are binary operations.
                $r   = ($rgb >> 16) & 0xFF;
                $g   = ($rgb >> 8) & 0xFF;
                $b   = $rgb & 0xFF;

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
        return array(
            'r' => $rBin,
            'g' => $gBin,
            'b' => $bBin,
            'a' => $aBin
        );
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