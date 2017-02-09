<?php

namespace Grafika\Gd;

use Grafika\DrawingObjectInterface;
use Grafika\EditorInterface;
use Grafika\FilterInterface;
use Grafika\Gd\Helper\GifHelper;
use Grafika\Gd\ImageHash\DifferenceHash;
use Grafika\Grafika;
use Grafika\ImageInterface;
use Grafika\ImageType;
use Grafika\Color;
use Grafika\Position;

/**
 * GD Editor class. Uses the PHP GD library.
 * @package Grafika\Gd
 */
final class Editor implements EditorInterface
{

    /**
     * Apply a filter to the image. See Filters section for a list of available filters.
     *
     * @param Image $image
     * @param FilterInterface $filter
     *
     * @return Editor
     */
    public function apply( &$image, $filter)
    {

        if ($image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        $image = $filter->apply($image);

        return $this;
    }

    /**
     * Blend two images together with the first image as the base and the second image on top. Supports several blend modes.
     *
     * @param Image $image1 The base image.
     * @param Image $image2 The image placed on top of the base image.
     * @param string $type The blend mode. Can be: normal, multiply, overlay or screen.
     * @param float $opacity The opacity of $image2. Possible values 0.0 to 1.0 where 0.0 is fully transparent and 1.0 is fully opaque. Defaults to 1.0.
     * @param string $position The position of $image2 on $image1. Possible values top-left, top-center, top-right, center-left, center, center-right, bottom-left, bottom-center, bottom-right and smart. Defaults to top-left.
     * @param int $offsetX Number of pixels to add to the X position of $image2.
     * @param int $offsetY Number of pixels to add to the Y position of $image2.
     *
     * @return Editor
     * @throws \Exception When added image is outside of canvas or invalid blend type
     */
    public function blend(&$image1, $image2, $type='normal', $opacity = 1.0, $position = 'top-left', $offsetX = 0, $offsetY = 0 ){

        // Turn into position object
        $position = new Position($position, $offsetX, $offsetY);

        // Position is for $image2. $image1 is canvas.
        list($offsetX, $offsetY) = $position->getXY($image1->getWidth(), $image1->getHeight(), $image2->getWidth(), $image2->getHeight());

        // Check if it overlaps
        if( ($offsetX >= $image1->getWidth() ) or
            ($offsetX + $image2->getWidth() <= 0) or
            ($offsetY >= $image1->getHeight() ) or
            ($offsetY + $image2->getHeight() <= 0)){

            throw new \Exception('Invalid blending. Image 2 is outside the canvas.');
        }

        // Loop start X
        $loopStartX = 0;
        $canvasStartX = $offsetX;
        if($canvasStartX < 0){
            $diff = 0 - $canvasStartX;
            $loopStartX += $diff;
        }

        // Loop end X
        $loopEndX = $image2->getWidth();
        $canvasEndX = $offsetX + $image2->getWidth();
        if($canvasEndX > $image1->getWidth()){
            $diff = $canvasEndX - $image1->getWidth();
            $loopEndX -= $diff;
        }

        // Loop start Y
        $loopStartY = 0;
        $canvasStartY = $offsetY;
        if($canvasStartY < 0){
            $diff = 0 - $canvasStartY;
            $loopStartY += $diff;
        }

        // Loop end Y
        $loopEndY = $image2->getHeight();
        $canvasEndY = $offsetY + $image2->getHeight();
        if($canvasEndY > $image1->getHeight()){
            $diff = $canvasEndY - ($image1->getHeight());
            $loopEndY -= $diff;
        }

        $w   = $image1->getWidth();
        $h   = $image1->getHeight();
        $gd1 = $image1->getCore();
        $gd2 = $image2->getCore();

        $canvas = imagecreatetruecolor( $w, $h );
        imagecopy( $canvas, $gd1, 0, 0, 0, 0, $w, $h );

        $type = strtolower( $type );
        if($type==='normal') {
            if ( $opacity !== 1 ) {
                $this->opacity($image2, $opacity);
            }
            imagecopy( $canvas, $gd2, $loopStartX + $offsetX, $loopStartY + $offsetY, 0, 0, $image2->getWidth(), $image2->getHeight());
        } else if($type==='multiply'){
            $this->_blendMultiply( $canvas, $gd1, $gd2, $loopStartY, $loopEndY, $loopStartX, $loopEndX, $offsetX, $offsetY, $opacity );
        } else if($type==='overlay'){
            $this->_blendOverlay( $canvas, $gd1, $gd2, $loopStartY, $loopEndY, $loopStartX, $loopEndX, $offsetX, $offsetY, $opacity );
        } else if($type==='screen'){
            $this->_blendScreen( $canvas, $gd1, $gd2, $loopStartY, $loopEndY, $loopStartX, $loopEndX, $offsetX, $offsetY, $opacity );
        } else {
            throw new \Exception(sprintf('Invalid blend type "%s".', $type));
        }

        imagedestroy( $gd1 ); // Free resource

        $image1 = new Image(
            $canvas,
            $image1->getImageFile(),
            $w,
            $h,
            $image1->getType()
        );

        return $this;
    }

    /**
     * Compare two images and returns a hamming distance. A value of 0 indicates a likely similar picture. A value between 1 and 10 is potentially a variation. A value greater than 10 is likely a different image.
     *
     * @param ImageInterface|string $image1
     * @param ImageInterface|string $image2
     *
     * @return int Hamming distance. Note: This breaks the chain if you are doing fluent api calls as it does not return an Editor.
     * @throws \Exception
     */
    public function compare($image1, $image2)
    {

        if (is_string($image1)) { // If string passed, turn it into a Image object
            $image1 = Image::createFromFile($image1);
            $this->flatten( $image1 );
        }

        if (is_string($image2)) { // If string passed, turn it into a Image object
            $image2 = Image::createFromFile($image2);
            $this->flatten( $image2 );
        }

        $hash = new DifferenceHash();

        $bin1     = $hash->hash($image1, $this);
        $bin2     = $hash->hash($image2, $this);
        $str1     = str_split($bin1);
        $str2     = str_split($bin2);
        $distance = 0;
        foreach ($str1 as $i => $char) {
            if ($char !== $str2[$i]) {
                $distance++;
            }
        }

        return $distance;

    }

    /**
     * Crop the image to the given dimension and position.
     *
     * @param Image $image
     * @param int $cropWidth Crop width in pixels.
     * @param int $cropHeight Crop Height in pixels.
     * @param string $position The crop position. Possible values top-left, top-center, top-right, center-left, center, center-right, bottom-left, bottom-center, bottom-right and smart. Defaults to center.
     * @param int $offsetX Number of pixels to add to the X position of the crop.
     * @param int $offsetY Number of pixels to add to the Y position of the crop.
     *
     * @return Editor
     * @throws \Exception
     */
    public function crop( &$image, $cropWidth, $cropHeight, $position = 'center', $offsetX = 0, $offsetY = 0)
    {

        if ($image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        if ( 'smart' === $position ) { // Smart crop
            list( $x, $y ) = $this->_smartCrop( $image, $cropWidth, $cropHeight );
        } else {
            // Turn into an instance of Position
            $position = new Position( $position, $offsetX, $offsetY );

            // Crop position as x,y coordinates
            list( $x, $y ) = $position->getXY( $image->getWidth(), $image->getHeight(), $cropWidth, $cropHeight );

        }

        // Create blank image
        $newImageResource = imagecreatetruecolor($cropWidth, $cropHeight);

        // Now crop
        imagecopyresampled(
            $newImageResource, // Target image
            $image->getCore(), // Source image
            0, // Target x
            0, // Target y
            $x, // Src x
            $y, // Src y
            $cropWidth, // Target width
            $cropHeight, // Target height
            $cropWidth, // Src width
            $cropHeight // Src height
        );

        // Free memory of old resource
        imagedestroy($image->getCore());

        // Cropped image instance
        $image = new Image(
            $newImageResource,
            $image->getImageFile(),
            $cropWidth,
            $cropHeight,
            $image->getType()
        );

        return $this;
    }

    /**
     * Draw a DrawingObject on the image. See Drawing Objects section.
     *
     * @param Image $image
     * @param DrawingObjectInterface $drawingObject
     *
     * @return $this
     */
    public function draw( &$image, $drawingObject)
    {

        if ($image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        $image = $drawingObject->draw($image);

        return $this;
    }

    /**
     * Compare if two images are equal. It will compare if the two images are of the same width and height. If the dimensions differ, it will return false. If the dimensions are equal, it will loop through each pixels. If one of the pixel don't match, it will return false. The pixels are compared using their RGB (Red, Green, Blue) values.
     *
     * @param string|ImageInterface $image1 Can be an instance of Image or string containing the file system path to image.
     * @param string|ImageInterface $image2 Can be an instance of Image or string containing the file system path to image.
     *
     * @return bool True if equals false if not. Note: This breaks the chain if you are doing fluent api calls as it does not return an Editor.
     * @throws \Exception
     */
    public function equal($image1, $image2)
    {

        if (is_string($image1)) { // If string passed, turn it into a Image object
            $image1 = Image::createFromFile($image1);
            $this->flatten( $image1 );
        }

        if (is_string($image2)) { // If string passed, turn it into a Image object
            $image2 = Image::createFromFile($image2);
            $this->flatten( $image2 );
        }

        // Check if image dimensions are equal
        if ($image1->getWidth() !== $image2->getWidth() or $image1->getHeight() !== $image2->getHeight()) {

            return false;

        } else {

            // Loop using image1
            for ($y = 0; $y < $image1->getHeight(); $y++) {
                for ($x = 0; $x < $image1->getWidth(); $x++) {

                    // Get image1 pixel
                    $rgb1 = imagecolorat($image1->getCore(), $x, $y);
                    $r1   = ($rgb1 >> 16) & 0xFF;
                    $g1   = ($rgb1 >> 8) & 0xFF;
                    $b1   = $rgb1 & 0xFF;

                    // Get image2 pixel
                    $rgb2 = imagecolorat($image2->getCore(), $x, $y);
                    $r2   = ($rgb2 >> 16) & 0xFF;
                    $g2   = ($rgb2 >> 8) & 0xFF;
                    $b2   = $rgb2 & 0xFF;

                    // Compare pixel value
                    if (
                        $r1 !== $r2 or
                        $g1 !== $g2 or
                        $b1 !== $b2
                    ) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Fill entire image with color.
     *
     * @param Image $image
     * @param Color $color Color object
     * @param int $x X-coordinate of start point
     * @param int $y Y-coordinate of start point
     *
     * @return Editor
     */
    public function fill( &$image, $color, $x = 0, $y = 0)
    {

        if ($image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        list($r, $g, $b, $alpha) = $color->getRgba();

        $colorResource = imagecolorallocatealpha($image->getCore(), $r, $g, $b,
            $this->gdAlpha($alpha));
        imagefill($image->getCore(), $x, $y, $colorResource);

        return $this;
    }

    /**
     * Flatten if animated GIF. Do nothing otherwise.
     *
     * @param Image $image
     *
     * @return Editor
     */
    public function flatten(&$image){

        if($image->isAnimated()) {
            $old = $image->getCore();
            $gift = new GifHelper();
            $hex  = $gift->encode($image->getBlocks());
            $gd   = imagecreatefromstring(pack('H*', $hex)); // Recreate resource from blocks

            imagedestroy( $old ); // Free resource
            $image = new Image(
                $gd,
                $image->getImageFile(),
                $image->getWidth(),
                $image->getHeight(),
                $image->getType(),
                '', // blocks
                false // animated
            );
        }
        return $this;
    }

    /**
     * Flip or mirrors the image.
     *
     * @param Image $image
     * @param string $mode The type of flip: 'h' for horizontal flip or 'v' for vertical.
     *
     * @return Editor
     * @throws \Exception
     */
    public function flip(&$image, $mode){

        $image = $this->_flip($image, $mode);
        return $this;
    }

    /**
     * Free the image clearing resources associated with it.
     *
     * @param Image $image
     *
     * @return Editor
     */
    public function free( &$image )
    {
        imagedestroy($image->getCore());
        return $this;
    }

    /**
     * Convert alpha value of 0 - 1 to GD compatible alpha value of 0 - 127 where 0 is opaque and 127 is transparent
     *
     * @param float $alpha Alpha value of 0 - 1. Example: 0, 0.60, 0.9, 1
     *
     * @return int
     */
    public static function gdAlpha($alpha)
    {

        $scale = round(127 * $alpha);

        return $invert = 127 - $scale;
    }

    /**
     * Checks if the editor is available on the current PHP install.
     *
     * @return bool True if available false if not.
     */
    public function isAvailable()
    {
        if (false === extension_loaded('gd') || false === function_exists('gd_info')) {
            return false;
        }

        // On some setups GD library does not provide imagerotate()
        if ( ! function_exists('imagerotate')) {

            return false;
        }

        return true;
    }

    /**
     * Sets the image to the specified opacity level where 1.0 is fully opaque and 0.0 is fully transparent.
     * Warning: This function loops thru each pixel manually which can be slow. Use sparingly.
     *
     * @param Image $image
     * @param float $opacity
     *
     * @return Editor
     * @throws \Exception
     */
    public function opacity( &$image, $opacity )
    {

        if ($image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        // Bounds checks
        $opacity = ($opacity > 1) ? 1 : $opacity;
        $opacity = ($opacity < 0) ? 0 : $opacity;

        for ($y = 0; $y < $image->getHeight(); $y++) {
            for ($x = 0; $x < $image->getWidth(); $x++) {
                $rgb   = imagecolorat($image->getCore(), $x, $y);
                $alpha = ($rgb >> 24) & 0x7F; // 127 in hex. These are binary operations.
                $r     = ($rgb >> 16) & 0xFF;
                $g     = ($rgb >> 8) & 0xFF;
                $b     = $rgb & 0xFF;

                // Reverse alpha values from 127-0 (transparent to opaque) to 0-127 for easy math
                // Previously: 0 = opaque, 127 = transparent.
                // Now: 0 = transparent, 127 = opaque
                $reverse = 127 - $alpha;
                $reverse = round($reverse * $opacity);

                if ($alpha < 127) { // Process non transparent pixels only
                    imagesetpixel($image->getCore(), $x, $y,
                        imagecolorallocatealpha($image->getCore(), $r, $g, $b, 127 - $reverse));
                }
            }
        }

        return $this;
    }

    /**
     * Open an image file and assign Image to first parameter.
     *
     * @param Image $image
     * @param string $imageFile
     *
     * @return Editor
     */
    public function open(&$image, $imageFile){
        $image = Image::createFromFile( $imageFile );
        return $this;
    }

    /**
     * Wrapper function for the resizeXXX family of functions. Resize image given width, height and mode.
     *
     * @param Image $image
     * @param int $newWidth Width in pixels.
     * @param int $newHeight Height in pixels.
     * @param string $mode Resize mode. Possible values: "exact", "exactHeight", "exactWidth", "fill", "fit".
     *
     * @return Editor
     * @throws \Exception
     */
    public function resize(&$image, $newWidth, $newHeight, $mode = 'fit')
    {
        /*
         * Resize formula:
         * ratio = w / h
         * h = w / ratio
         * w = h * ratio
         */
        switch ($mode) {
            case 'exact':
                $this->resizeExact($image, $newWidth, $newHeight);
                break;
            case 'fill':
                $this->resizeFill($image, $newWidth, $newHeight);
                break;
            case 'exactWidth':
                $this->resizeExactWidth($image, $newWidth);
                break;
            case 'exactHeight':
                $this->resizeExactHeight($image, $newHeight);
                break;
            case 'fit':
                $this->resizeFit($image, $newWidth, $newHeight);
                break;
            default:
                throw new \Exception(sprintf('Invalid resize mode "%s".', $mode));
        }

        return $this;
    }

    /**
     * Resize image to exact dimensions ignoring aspect ratio. Useful if you want to force exact width and height.
     *
     * @param Image $image
     * @param int $newWidth Width in pixels.
     * @param int $newHeight Height in pixels.
     *
     * @return Editor
     */
    public function resizeExact(&$image, $newWidth, $newHeight)
    {

        $this->_resize($image, $newWidth, $newHeight);

        return $this;
    }

    /**
     * Resize image to exact height. Width is auto calculated. Useful for creating row of images with the same height.
     *
     * @param Image $image
     * @param int $newHeight Height in pixels.
     *
     * @return Editor
     */
    public function resizeExactHeight(&$image, $newHeight)
    {

        $width  = $image->getWidth();
        $height = $image->getHeight();
        $ratio  = $width / $height;

        $resizeHeight = $newHeight;
        $resizeWidth  = $newHeight * $ratio;

        $this->_resize($image, $resizeWidth, $resizeHeight);

        return $this;
    }

    /**
     * Resize image to exact width. Height is auto calculated. Useful for creating column of images with the same width.
     *
     * @param Image $image
     * @param int $newWidth Width in pixels.
     *
     * @return Editor
     */
    public function resizeExactWidth(&$image, $newWidth)
    {

        $width  = $image->getWidth();
        $height = $image->getHeight();
        $ratio  = $width / $height;

        $resizeWidth  = $newWidth;
        $resizeHeight = round($newWidth / $ratio);

        $this->_resize($image, $resizeWidth, $resizeHeight);

        return $this;
    }

    /**
     * Resize image to fill all the space in the given dimension. Excess parts are cropped.
     *
     * @param Image $image
     * @param int $newWidth Width in pixels.
     * @param int $newHeight Height in pixels.
     *
     * @return Editor
     */
    public function resizeFill(&$image, $newWidth, $newHeight)
    {
        $width  = $image->getWidth();
        $height = $image->getHeight();
        $ratio  = $width / $height;

        // Base optimum size on new width
        $optimumWidth  = $newWidth;
        $optimumHeight = round($newWidth / $ratio);

        if (($optimumWidth < $newWidth) or ($optimumHeight < $newHeight)) { // Oops, where trying to fill and there are blank areas
            // So base optimum size on height instead
            $optimumWidth  = $newHeight * $ratio;
            $optimumHeight = $newHeight;
        }

        $this->_resize($image, $optimumWidth, $optimumHeight);
        $this->crop($image, $newWidth, $newHeight); // Trim excess parts

        return $this;
    }

    /**
     * Resize image to fit inside the given dimension. No part of the image is lost.
     *
     * @param Image $image
     * @param int $newWidth Width in pixels.
     * @param int $newHeight Height in pixels.
     *
     * @return Editor
     */
    public function resizeFit(&$image, $newWidth, $newHeight)
    {

        $width  = $image->getWidth();
        $height = $image->getHeight();
        $ratio  = $width / $height;

        // Try basing it on width first
        $resizeWidth  = $newWidth;
        $resizeHeight = round($newWidth / $ratio);

        if (($resizeWidth > $newWidth) or ($resizeHeight > $newHeight)) { // Oops, either with or height does not fit
            // So base on height instead
            $resizeHeight = $newHeight;
            $resizeWidth  = $newHeight * $ratio;
        }

        $this->_resize($image, $resizeWidth, $resizeHeight);

        return $this;
    }

    /**
     * Rotate an image counter-clockwise.
     *
     * @param Image $image
     * @param int $angle The angle in degrees.
     * @param Color|null $color The Color object containing the background color.
     *
     * @return EditorInterface An instance of image editor.
     * @throws \Exception
     */
    public function rotate(&$image, $angle, $color = null)
    {

        if ($image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        $color = ($color !== null) ? $color : new Color('#000000');
        list($r, $g, $b, $alpha) = $color->getRgba();

        $old = $image->getCore();
        $new = imagerotate($old, $angle, imagecolorallocatealpha($old, $r, $g, $b, $alpha));

        if(false === $new){
            throw new \Exception('Error rotating image.');
        }

        imagedestroy( $old ); // Free resource
        $image = new Image( $new, $image->getImageFile(), $image->getWidth(), $image->getHeight(), $image->getType() );

        return $this;
    }

    /**
     * Save the image to an image format.
     *
     * @param Image $image
     * @param string $file File path where to save the image.
     * @param null|string $type Type of image. Can be null, "GIF", "PNG", or "JPEG".
     * @param null|string $quality Quality of image. Applies to JPEG only. Accepts number 0 - 100 where 0 is lowest and 100 is the highest quality. Or null for default.
     * @param bool|false $interlace Set to true for progressive JPEG. Applies to JPEG only.
     * @param int $permission Default permission when creating non-existing target directory.
     *
     * @return Editor
     * @throws \Exception
     */
    public function save($image, $file, $type = null, $quality = null, $interlace = false, $permission = 0755)
    {

        if (null === $type) {

            $type = $this->_getImageTypeFromFileName($file); // Null given, guess type from file extension
            if (ImageType::UNKNOWN === $type) {
                $type = $image->getType(); // 0 result, use original image type
            }
        }

        $targetDir = dirname($file); // $file's directory
        if (false === is_dir($targetDir)) { // Check if $file's directory exist
            // Create and set default perms to 0755
            if ( ! mkdir($targetDir, $permission, true)) {
                throw new \Exception(sprintf('Cannot create %s', $targetDir));
            }
        }

        switch (strtoupper($type)) {
            case ImageType::GIF :
                if($image->isAnimated()){
                    $blocks = $image->getBlocks();
                    $gift = new GifHelper();
                    $hex = $gift->encode($blocks);
                    file_put_contents($file, pack('H*', $hex));
                } else {
                    imagegif($image->getCore(), $file);
                }

                break;

            case ImageType::PNG :
                // PNG is lossless and does not need compression. Although GD allow values 0-9 (0 = no compression), we leave it alone.
                imagepng($image->getCore(), $file);
                break;

            default: // Defaults to jpeg
                $quality = ($quality === null) ? 75 : $quality; // Default to 75 (GDs default) if null.
                $quality = ($quality > 100) ? 100 : $quality;
                $quality = ($quality < 0) ? 0 : $quality;
                imageinterlace($image->getCore(), $interlace);
                imagejpeg($image->getCore(), $file, $quality);
        }

        return $this;
    }

    /**
     * Write text to image.
     *
     * @param Image $image
     * @param string $text The text to be written.
     * @param int $size The font size. Defaults to 12.
     * @param int $x The distance from the left edge of the image to the left of the text. Defaults to 0.
     * @param int $y The distance from the top edge of the image to the top of the text. Defaults to 12 (equal to font size) so that the text is placed within the image.
     * @param Color $color The Color object. Default text color is black.
     * @param string $font Full path to font file. If blank, will default to Liberation Sans font.
     * @param int $angle Angle of text from 0 - 359. Defaults to 0.
     *
     * @return EditorInterface
     * @throws \Exception
     */
    public function text(&$image, $text, $size = 12, $x = 0, $y = 0, $color = null, $font = '', $angle = 0)
    {

        if ($image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        $y += $size;

        $color = ($color !== null) ? $color : new Color('#000000');
        $font  = ($font !== '') ? $font : Grafika::fontsDir() . DIRECTORY_SEPARATOR . 'LiberationSans-Regular.ttf';

        list($r, $g, $b, $alpha) = $color->getRgba();

        $colorResource = imagecolorallocatealpha(
            $image->getCore(),
            $r, $g, $b,
            $this->gdAlpha($alpha)
        );

        imagettftext(
            $image->getCore(),
            $size,
            $angle,
            $x,
            $y,
            $colorResource,
            $font,
            $text
        );

        return $this;
    }

    /**
     * @param $canvas
     * @param $gd1
     * @param $gd2
     * @param $loopStartY
     * @param $loopEndY
     * @param $loopStartX
     * @param $loopEndX
     * @param $offsetX
     * @param $offsetY
     *
     * @param $opacity
     *
     * @return $this
     */
    private function _blendMultiply($canvas, $gd1, $gd2, $loopStartY, $loopEndY, $loopStartX, $loopEndX, $offsetX, $offsetY, $opacity){

        for ( $y = $loopStartY; $y < $loopEndY; $y++ ) {
            for ( $x = $loopStartX; $x < $loopEndX; $x++ ) {
                $canvasX    = $x + $offsetX;
                $canvasY    = $y + $offsetY;
                $argb1 = imagecolorat( $gd1, $canvasX, $canvasY );
                $r1    = ( $argb1 >> 16 ) & 0xFF;
                $g1    = ( $argb1 >> 8 ) & 0xFF;
                $b1    = $argb1 & 0xFF;

                $argb2 = imagecolorat( $gd2, $x, $y );
                $a2 = ($argb2 >> 24) & 0x7F; // 127 in hex. These are binary operations.
                $r2    = ( $argb2 >> 16 ) & 0xFF;
                $g2    = ( $argb2 >> 8 ) & 0xFF;
                $b2    = $argb2 & 0xFF;

                $r3 = round($r1 * $r2 / 255);
                $g3 = round($g1 * $g2 / 255);
                $b3 = round($b1 * $b2 / 255);

                $reverse = 127 - $a2;
                $reverse = round($reverse * $opacity);

                $argb3 = imagecolorallocatealpha( $canvas, $r3, $g3, $b3, 127 - $reverse );
                imagesetpixel( $canvas, $canvasX, $canvasY, $argb3 );
            }
        }
        return $canvas;
    }

    /**
     * @param $canvas
     * @param $gd1
     * @param $gd2
     * @param $loopStartY
     * @param $loopEndY
     * @param $loopStartX
     * @param $loopEndX
     * @param $offsetX
     * @param $offsetY
     *
     * @param $opacity
     *
     * @return $this
     */
    private function _blendOverlay($canvas, $gd1, $gd2, $loopStartY, $loopEndY, $loopStartX, $loopEndX, $offsetX, $offsetY, $opacity){

        for ( $y = $loopStartY; $y < $loopEndY; $y++ ) {
            for ( $x = $loopStartX; $x < $loopEndX; $x++ ) {
                $canvasX    = $x + $offsetX;
                $canvasY    = $y + $offsetY;
                $argb1 = imagecolorat( $gd1, $canvasX, $canvasY );
                $r1    = ( $argb1 >> 16 ) & 0xFF;
                $g1    = ( $argb1 >> 8 ) & 0xFF;
                $b1    = $argb1 & 0xFF;

                $argb2 = imagecolorat( $gd2, $x, $y );
                $a2 = ($argb2 >> 24) & 0x7F; // 127 in hex. These are binary operations.
                $r2    = ( $argb2 >> 16 ) & 0xFF;
                $g2    = ( $argb2 >> 8 ) & 0xFF;
                $b2    = $argb2 & 0xFF;

                $r1 /= 255;
                $r2 /= 255;
                if ($r1 < 0.5) {
                    $r3 = 2 * ($r1 * $r2);
                } else {
                    $r3 = (1 - (2 *(1-$r1)) * (1-$r2));
                }

                $g1 /= 255;
                $g2 /= 255;
                if ($g1 < 0.5) {
                    $g3 = 2 * ($g1 * $g2);
                } else {
                    $g3 = (1 - (2 *(1-$g1)) * (1-$g2));
                }

                $b1 /= 255;
                $b2 /= 255;
                if ($b1 < 0.5) {
                    $b3 = 2 * ($b1 * $b2);
                } else {
                    $b3 = (1 - (2 *(1-$b1)) * (1-$b2));
                }

                $reverse = 127 - $a2;
                $reverse = round($reverse * $opacity);

                $argb3 = imagecolorallocatealpha( $canvas, $r3*255, $g3*255, $b3*255, 127 - $reverse );
                imagesetpixel( $canvas, $canvasX, $canvasY, $argb3 );
            }
        }
        return $canvas;
    }

    /**
     * Calculate entropy based on histogram.
     *
     * @param $hist
     *
     * @return float|int
     */
    private function _entropy($hist){
        $entropy = 0;
        $hist_size = array_sum($hist['r']) + array_sum($hist['g']) + array_sum($hist['b']);
        foreach($hist['r'] as $p){
            $p = $p / $hist_size;
            $entropy += $p * log($p, 2);
        }
        foreach($hist['g'] as $p){
            $p = $p / $hist_size;
            $entropy += $p * log($p, 2);
        }
        foreach($hist['b'] as $p){
            $p = $p / $hist_size;
            $entropy += $p * log($p, 2);
        }
        return $entropy * -1;
    }

    /**
     * @param $canvas
     * @param $gd1
     * @param $gd2
     * @param $loopStartY
     * @param $loopEndY
     * @param $loopStartX
     * @param $loopEndX
     * @param $offsetX
     * @param $offsetY
     *
     * @param $opacity
     *
     * @return $this
     */
    private function _blendScreen($canvas, $gd1, $gd2, $loopStartY, $loopEndY, $loopStartX, $loopEndX, $offsetX, $offsetY, $opacity){

        for ( $y = $loopStartY; $y < $loopEndY; $y++ ) {
            for ( $x = $loopStartX; $x < $loopEndX; $x++ ) {
                $canvasX    = $x + $offsetX;
                $canvasY    = $y + $offsetY;
                $argb1 = imagecolorat( $gd1, $canvasX, $canvasY );
                $r1    = ( $argb1 >> 16 ) & 0xFF;
                $g1    = ( $argb1 >> 8 ) & 0xFF;
                $b1    = $argb1 & 0xFF;

                $argb2 = imagecolorat( $gd2, $x, $y );
                $a2 = ($argb2 >> 24) & 0x7F; // 127 in hex. These are binary operations.
                $r2    = ( $argb2 >> 16 ) & 0xFF;
                $g2    = ( $argb2 >> 8 ) & 0xFF;
                $b2    = $argb2 & 0xFF;

                $r3 = 255 - ( ( 255 - $r1 ) * ( 255 - $r2 ) ) / 255;
                $g3 = 255 - ( ( 255 - $g1 ) * ( 255 - $g2 ) ) / 255;
                $b3 = 255 - ( ( 255 - $b1 ) * ( 255 - $b2 ) ) / 255;

                $reverse = 127 - $a2;
                $reverse = round($reverse * $opacity);

                $argb3 = imagecolorallocatealpha( $canvas, $r3, $g3, $b3, 127 - $reverse );
                imagesetpixel( $canvas, $canvasX, $canvasY, $argb3 );
            }
        }
        return $canvas;
    }

    /**
     * Flips image.
     * @param Image $image
     * @param $mode
     *
     * @return Image
     * @throws \Exception
     */
    private function _flip($image, $mode)
    {
        $old = $image->getCore();
        $w   = $image->getWidth();
        $h   = $image->getHeight();
        if ($mode === 'h') {
            $new = imagecreatetruecolor($w, $h);
            for ($x = 0; $x < $w; $x++) {
                imagecopy($new, $old, $w - $x - 1, 0, $x, 0, 1, $h);
            }
            imagedestroy($old); // Free resource
            return new Image(
                $new,
                $image->getImageFile(),
                $w,
                $h,
                $image->getType(),
                $image->getBlocks(),
                $image->isAnimated()
            );
        } else if ($mode === 'v') {
            $new = imagecreatetruecolor($w, $h);
            for ($y = 0; $y < $h; $y++) {
                imagecopy($new, $old, 0, $h - $y - 1, 0, $y, $w, 1);
            }
            imagedestroy($old); // Free resource
            return new Image(
                $new,
                $image->getImageFile(),
                $w,
                $h,
                $image->getType(),
                $image->getBlocks(),
                $image->isAnimated()
            );
        } else {
            throw new \Exception(sprintf('Unsupported mode "%s"', $mode));
        }
    }

    /**
     * Get image type base on file extension.
     *
     * @param int $imageFile File path to image.
     *
     * @return ImageType string Type of image.
     */
    private function _getImageTypeFromFileName($imageFile)
    {
        $ext = strtolower((string)pathinfo($imageFile, PATHINFO_EXTENSION));

        if ('jpg' === $ext or 'jpeg' === $ext) {
            return ImageType::JPEG;
        } else if ('gif' === $ext) {
            return ImageType::GIF;
        } else if ('png' === $ext) {
            return ImageType::PNG;
        } else if ('wbm' === $ext or 'wbmp' === $ext) {
            return ImageType::WBMP;
        } else {
            return ImageType::UNKNOWN;
        }
    }

    /**
     * Resize helper function.
     *
     * @param Image $image
     * @param int $newWidth
     * @param int $newHeight
     * @param int $targetX
     * @param int $targetY
     * @param int $srcX
     * @param int $srcY
     *
     */
    private function _resize(&$image, $newWidth, $newHeight, $targetX = 0, $targetY = 0, $srcX = 0, $srcY = 0)
    {

//        $this->_imageCheck();

        if ($image->isAnimated()) { // Animated GIF
            $gift = new GifHelper();
            $blocks = $gift->resize($image->getBlocks(), $newWidth, $newHeight);
            // Resize image instance
            $image = new Image(
                $image->getCore(),
                $image->getImageFile(),
                $newWidth,
                $newHeight,
                $image->getType(),
                $blocks,
                true
            );
        } else {

            // Create blank image
            $newImage = Image::createBlank($newWidth, $newHeight);

            if (ImageType::PNG === $image->getType()) {
                // Preserve PNG transparency
                $newImage->fullAlphaMode(true);
            }

            imagecopyresampled(
                $newImage->getCore(),
                $image->getCore(),
                $targetX,
                $targetY,
                $srcX,
                $srcY,
                $newWidth,
                $newHeight,
                $image->getWidth(),
                $image->getHeight()
            );

            // Free memory of old resource
            imagedestroy($image->getCore());

            // Resize image instance
            $image = new Image(
                $newImage->getCore(),
                $image->getImageFile(),
                $newWidth,
                $newHeight,
                $image->getType()
            );

        }
    }

    /**
     * Crop based on entropy.
     *
     * @param Image $oldImage
     * @param $cropW
     * @param $cropH
     *
     * @return array
     */
    private function _smartCrop($oldImage, $cropW, $cropH){
        $image = clone $oldImage;

        $this->resizeFit($image, 30, 30);

        $origW = $oldImage->getWidth();
        $origH = $oldImage->getHeight();
        $resizeW = $image->getWidth();
        $resizeH = $image->getHeight();

        $smallCropW = round(($resizeW / $origW) * $cropW);
        $smallCropH = round(($resizeH / $origH) * $cropH);

        $step = 1;

        for($y = 0; $y < $resizeH-$smallCropH; $y+=$step){
            for($x = 0; $x < $resizeW-$smallCropW; $x+=$step){
                $hist[$x.'-'.$y] = $this->_entropy($image->histogram(array(array($x, $y), array($smallCropW, $smallCropH))));
            }
            if($resizeW-$smallCropW <= 0){
                $hist['0-'.$y] = $this->_entropy($image->histogram(array(array(0, 0), array($smallCropW, $smallCropH))));
            }
        }
        if($resizeH-$smallCropH <= 0){
            $hist['0-0'] = $this->_entropy($image->histogram(array(array(0, 0), array($smallCropW, $smallCropH))));
        }

        asort($hist);
        end($hist);
        $pos = key($hist); // last key
        list($x, $y) = explode('-', $pos);
        $x = round($x*($origW / $resizeW));
        $y = round($y*($origH / $resizeH));

        return array($x,$y);
    }
}