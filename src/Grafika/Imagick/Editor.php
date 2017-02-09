<?php

namespace Grafika\Imagick;

use Grafika\DrawingObjectInterface;
use Grafika\EditorInterface;
use Grafika\FilterInterface;
use Grafika\Grafika;
use Grafika\ImageInterface;
use Grafika\ImageType;
use Grafika\Color;
use Grafika\Imagick\ImageHash\DifferenceHash;
use Grafika\Position;

/**
 * Imagick Editor class. Uses the PHP Imagick library.
 * @package Grafika\Imagick
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
    public function apply(&$image, $filter)
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

        // Loop start Y
        $loopStartY = 0;
        $canvasStartY = $offsetY;
        if($canvasStartY < 0){
            $diff = 0 - $canvasStartY;
            $loopStartY += $diff;
        }

        if ( $opacity !== 1 ) {
            $this->opacity($image2, $opacity);
        }

        $type = strtolower( $type );
        if($type==='normal') {
            $image1->getCore()->compositeImage($image2->getCore(), \Imagick::COMPOSITE_OVER, $loopStartX + $offsetX, $loopStartY + $offsetY);
        } else if($type==='multiply'){
            $image1->getCore()->compositeImage($image2->getCore(), \Imagick::COMPOSITE_MULTIPLY, $loopStartX + $offsetX, $loopStartY + $offsetY);
        } else if($type==='overlay'){
            $image1->getCore()->compositeImage($image2->getCore(), \Imagick::COMPOSITE_OVERLAY, $loopStartX + $offsetX, $loopStartY + $offsetY);
        } else if($type==='screen'){
            $image1->getCore()->compositeImage($image2->getCore(), \Imagick::COMPOSITE_SCREEN, $loopStartX + $offsetX, $loopStartY + $offsetY);
        } else {
            throw new \Exception(sprintf('Invalid blend type "%s".', $type));
        }

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
    public function crop(&$image, $cropWidth, $cropHeight, $position = 'center', $offsetX = 0, $offsetY = 0)
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

        $image->getCore()->cropImage($cropWidth, $cropHeight, $x, $y);

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
    public function draw(&$image, $drawingObject)
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
            $pixelIterator = $image1->getCore()->getPixelIterator();
            foreach ($pixelIterator as $row => $pixels) { /* Loop through pixel rows */
                foreach ($pixels as $column => $pixel) { /* Loop through the pixels in the row (columns) */
                    /**
                     * Get image1 pixel
                     * @var $pixel \ImagickPixel
                     */
                    $rgba1 = $pixel->getColor();

                    // Get image2 pixel
                    $rgba2 = $image2->getCore()->getImagePixelColor($column, $row)->getColor();

                    // Compare pixel value
                    if (
                        $rgba1['r'] !== $rgba2['r'] or
                        $rgba1['g'] !== $rgba2['g'] or
                        $rgba1['b'] !== $rgba2['b']
                    ) {
                        return false;
                    }
                }
                $pixelIterator->syncIterator(); /* Sync the iterator, this is important to do on each iteration */
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
     * @return self
     */
    public function fill(&$image, $color, $x = 0, $y = 0)
    {

        if ($image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        $target = $image->getCore()->getImagePixelColor($x, $y);
        $image->getCore()->floodfillPaintImage($color->getHexString(), 1, $target, $x, $y, false);

        return $this;
    }

    /**
     * Flatten if animated GIF. Do nothing otherwise.
     *
     * @param Image $image
     *
     * @return self
     */
    public function flatten(&$image){
        if($image->isAnimated()){
            $imagick = $image->getCore()->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
            $image = new Image(
                $imagick,
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
        if ($mode === 'h') {
            $image->getCore()->flopImage();
        } else if ($mode === 'v') {
            $image->getCore()->flipImage();
        } else {
            throw new \Exception(sprintf('Unsupported mode "%s"', $mode));
        }
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
        $image->getCore()->clear();
        return $this;
    }

    /**
     * Checks if the editor is available on the current PHP install.
     *
     * @return bool True if available false if not.
     */
    public function isAvailable()
    {
        // First, test Imagick's extension and classes.
        if (false === extension_loaded('imagick') ||
            false === class_exists('Imagick') ||
            false === class_exists('ImagickDraw') ||
            false === class_exists('ImagickPixel') ||
            false === class_exists('ImagickPixelIterator')
        ) {
            return false;
        }

        return true;
    }

    /**
     * Sets the image to the specified opacity level where 1.0 is fully opaque and 0.0 is fully transparent.
     *
     * @param Image $image
     * @param float $opacity
     *
     * @return self
     * @throws \Exception
     */
    public function opacity(&$image, $opacity)
    {

        if ($image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        // Bounds checks
        $opacity = ($opacity > 1) ? 1 : $opacity;
        $opacity = ($opacity < 0) ? 0 : $opacity;

        $image->getCore()->setImageOpacity($opacity);

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
     * @return self
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
     * @return self
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
     * @return self
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
     * @return self
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
     * @return self
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
     */
    public function rotate(&$image, $angle, $color = null)
    {

        if ($image->isAnimated()) { // Ignore animated GIF for now
            return $this;
        }

        $color = ($color !== null) ? $color : new Color('#000000');
        list($r, $g, $b, $alpha) = $color->getRgba();

        $image->getCore()->rotateImage(new \ImagickPixel("rgba($r, $g, $b, $alpha)"), $angle * -1);

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
    public function save( $image, $file, $type = null, $quality = null, $interlace = false, $permission = 0755)
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
                $image->getCore()->writeImages($file, true); // Support animated image. Eg. GIF
                break;

            case ImageType::PNG :
                // PNG is lossless and does not need compression. Although GD allow values 0-9 (0 = no compression), we leave it alone.
                $image->getCore()->setImageFormat($type);
                $image->getCore()->writeImage($file);
                break;

            default: // Defaults to jpeg
                $quality = ($quality === null) ? 75 : $quality; // Default to 75 (GDs default) if null.
                $quality = ($quality > 100) ? 100 : $quality;
                $quality = ($quality <= 0) ? 1 : $quality; // Note: If 0 change it to 1. The lowest quality in Imagick is 1 whereas in GD its 0.

                if ($interlace) {
                    $image->getCore()->setImageInterlaceScheme(\Imagick::INTERLACE_JPEG);
                }
                $image->getCore()->setImageFormat($type);
                $image->getCore()->setImageCompression(\Imagick::COMPRESSION_JPEG);
                $image->getCore()->setImageCompressionQuality($quality);
                $image->getCore()->writeImage($file); // Single frame image. Eg. JPEG
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

        // Set up draw properties
        $draw = new \ImagickDraw();
        // Text color
        $draw->setFillColor(new \ImagickPixel("rgba($r, $g, $b, $alpha)"));
        // Font properties
        $draw->setFont($font);
        $draw->setFontSize($size);

        // Write text
        $image->getCore()->annotateImage(
            $draw,
            $x,
            $y,
            $angle,
            $text
        );

        return $this;
    }

    /**
     * Calculate entropy based on histogram.
     *
     * @param array $hist Histogram returned by Image->histogram
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

    /**
     * Resize helper function.
     *
     * @param Image $image
     * @param int $newWidth
     * @param int $newHeight
     *
     * @return self
     * @throws \Exception
     */
    private function _resize(&$image, $newWidth, $newHeight)
    {

        if ('GIF' == $image->getType()) { // Animated image. Eg. GIF

            $imagick = $image->getCore()->coalesceImages();

            foreach ($imagick as $frame) {
                $frame->resizeImage($newWidth, $newHeight, \Imagick::FILTER_BOX, 1, false);
                $frame->setImagePage($newWidth, $newHeight, 0, 0);
            }

            // Assign new image with frames
            $image = new Image($imagick->deconstructImages(), $image->getImageFile(), $newWidth, $newHeight,
                $image->getType());
        } else { // Single frame image. Eg. JPEG, PNG

            $image->getCore()->resizeImage($newWidth, $newHeight, \Imagick::FILTER_LANCZOS, 1, false);
            // Assign new image
            $image = new Image($image->getCore(), $image->getImageFile(), $newWidth, $newHeight,
                $image->getType());
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

        if ('jpg' == $ext or 'jpeg' == $ext) {
            return ImageType::JPEG;
        } else if ('gif' == $ext) {
            return ImageType::GIF;
        } else if ('png' == $ext) {
            return ImageType::PNG;
        } else {
            return ImageType::UNKNOWN;
        }
    }

}