<?php
namespace Grafika;

/**
 * Interface EditorInterface
 * @package Grafika
 */
interface EditorInterface {

    /**
     * Apply a filter to the image. See Filters section for a list of available filters.
     *
     * @param ImageInterface $image
     * @param FilterInterface $filter
     *
     * @return EditorInterface An instance of image editor.
     */
    public function apply( &$image, $filter );

    /**
     * Blend two images together with the first image as the base and the second image on top. Supports several blend modes.
     *
     * @param ImageInterface $image1 The base image.
     * @param ImageInterface $image2 The image placed on top of the base image.
     * @param string $type The blend mode. Can be: normal, multiply, overlay or screen.
     * @param float $opacity The opacity of $image2. Possible values 0.0 to 1.0 where 0.0 is fully transparent and 1.0 is fully opaque. Defaults to 1.0.
     * @param string $position The position of $image2 on $image1. Possible values top-left, top-center, top-right, center-left, center, center-right, bottom-left, bottom-center, bottom-right and smart. Defaults to top-left.
     * @param int $offsetX Number of pixels to add to the X position of $image2.
     * @param int $offsetY Number of pixels to add to the Y position of $image2.
     *
     * @return EditorInterface An instance of image editor.
     */
    public function blend(&$image1, $image2, $type='normal', $opacity = 1.0, $position = 'top-left', $offsetX = 0, $offsetY = 0 );

    /**
     * Compare two images and returns a hamming distance. A value of 0 indicates a likely similar picture. A value between 1 and 10 is potentially a variation. A value greater than 10 is likely a different image.
     *
     * @param string|ImageInterface $image1 Can be an instance of Image or string containing the file system path to image.
     * @param string|ImageInterface $image2 Can be an instance of Image or string containing the file system path to image.
     *
     * @return int Hamming distance. Note: This breaks the chain if you are doing fluent api calls as it does not return an Editor.
     * @throws \Exception
     */
    public function compare( $image1, $image2 );

    /**
     * Crop the image to the given dimension and position.
     *
     * @param ImageInterface $image
     * @param int $cropWidth Crop width in pixels.
     * @param int $cropHeight Crop Height in pixels.
     * @param string $position The crop position. Possible values top-left, top-center, top-right, center-left, center, center-right, bottom-left, bottom-center, bottom-right and smart. Defaults to center.
     * @param int $offsetX Number of pixels to add to the X position of the crop.
     * @param int $offsetY Number of pixels to add to the Y position of the crop.
     *
     * @return EditorInterface An instance of image editor.
     */
    public function crop( &$image, $cropWidth, $cropHeight, $position = 'center', $offsetX = 0, $offsetY = 0 );

    /**
     * Draw a DrawingObject on the image. See Drawing Objects section.
     *
     * @param ImageInterface $image
     * @param DrawingObjectInterface $drawingObject
     *
     * @return EditorInterface An instance of image editor.
     */
    public function draw( &$image, $drawingObject );

    /**
     * Compare if two images are equal. It will compare if the two images are of the same width and height. If the dimensions differ, it will return false. If the dimensions are equal, it will loop through each pixels. If one of the pixel don't match, it will return false. The pixels are compared using their RGB (Red, Green, Blue) values.
     *
     * @param string|ImageInterface $image1 Can be an instance of Image or string containing the file system path to image.
     * @param string|ImageInterface $image2 Can be an instance of Image or string containing the file system path to image.
     *
     * @return bool True if equals false if not. Note: This breaks the chain if you are doing fluent api calls as it does not return an Editor.
     * @throws \Exception
     */
    public function equal( $image1, $image2 );
    
    /**
     * Fill entire image with color.
     *
     * @param ImageInterface $image
     * @param Color $color An instance of Grafika\Color class.
     * @param int $x X-coordinate of start point.
     * @param int $y Y-coordinate of start point.
     *
     * @return EditorInterface An instance of image editor.
     */
    public function fill( &$image, $color, $x = 0, $y = 0 );

    /**
     * Flatten if animated GIF. Do nothing otherwise.
     *
     * @param ImageInterface $image
     *
     * @return EditorInterface An instance of image editor.
     */
    public function flatten( &$image );

    /**
     * Flip an image.
     *
     * @param ImageInterface $image
     * @param string $mode The type of flip: 'h' for horizontal flip or 'v' for vertical.
     *
     * @return EditorInterface An instance of image editor.
     */
    public function flip( &$image, $mode);

    /**
     * Free the image clearing resources associated with it.
     *
     * @param ImageInterface $image
     *
     * @return EditorInterface An instance of image editor.
     */
    public function free( &$image );

    /**
     * Checks the PHP install if the editor is available.
     *
     * @return bool True if available false if not.
     */
    public function isAvailable();

    /**
     * Change the image opacity.
     *
     * @param ImageInterface $image
     * @param float $opacity The opacity level where 1.0 is fully opaque and 0.0 is fully transparent.
     *
     * @return EditorInterface An instance of image editor.
     */
    public function opacity( &$image, $opacity );

    /**
     * Open an image file and assign Image to first parameter.
     *
     * @param ImageInterface $image
     * @param string $imageFile
     *
     * @return EditorInterface An instance of image editor.
     */
    public function open( &$image, $imageFile );
    
    /**
     * Wrapper function for the resizeXXX family of functions. Resize an image to a given width, height and mode.
     *
     * @param ImageInterface $image
     * @param int $newWidth Width in pixels.
     * @param int $newHeight Height in pixels.
     * @param string $mode Resize mode. Possible values: "exact", "exactHeight", "exactWidth", "fill", "fit".
     *
     * @return EditorInterface An instance of image editor.
     */
    public function resize( &$image, $newWidth, $newHeight, $mode='fit' );

    /**
     * Resize image to exact dimensions ignoring aspect ratio. Useful if you want to force exact width and height.
     *
     * @param ImageInterface $image
     * @param int $newWidth Width in pixels.
     * @param int $newHeight Height in pixels.
     *
     * @return EditorInterface An instance of image editor.
     */
    public function resizeExact( &$image, $newWidth, $newHeight );

    /**
     * Resize image to exact height. Width is auto calculated. Useful for creating row of images with the same height.
     *
     * @param ImageInterface $image
     * @param int $newHeight Height in pixels.
     *
     * @return EditorInterface An instance of image editor.
     */
    public function resizeExactHeight( &$image, $newHeight );

    /**
     * Resize image to exact width. Height is auto calculated. Useful for creating column of images with the same width.
     *
     * @param ImageInterface $image
     * @param int $newWidth Width in pixels.
     *
     * @return EditorInterface An instance of image editor.
     */
    public function resizeExactWidth( &$image, $newWidth );

    /**
     * Resize image to fill all the space in the given dimension. Excess parts are cropped.
     *
     * @param ImageInterface $image
     * @param int $newWidth Width in pixels.
     * @param int $newHeight Height in pixels.
     *
     * @return EditorInterface An instance of image editor.
     */
    public function resizeFill( &$image, $newWidth, $newHeight );

    /**
     * Resize an image to fit within the given width and height. The re-sized image will not exceed the given dimension. Useful if you want to preserve the aspect ratio.
     *
     * @param ImageInterface $image
     * @param int $newWidth Width in pixels.
     * @param int $newHeight Width in pixels.
     *
     * @return EditorInterface An instance of image editor.
     */
    public function resizeFit( &$image, $newWidth, $newHeight );

    /**
     * Rotate an image counter-clockwise.
     *
     * @param ImageInterface $image
     * @param int $angle The angle in degrees.
     * @param Color|null $color The Color object containing the background color.
     *
     * @return EditorInterface An instance of image editor.
     */
    public function rotate( &$image, $angle, $color = null );

    /**
     * Save the image to an image format.
     *
     * @param ImageInterface $image
     * @param string $file File path where to save the image.
     * @param null|string $type Type of image. Can be null, "GIF", "PNG", or "JPEG". If null, an appropriate file type will be used.
     * @param null|string $quality Quality of image. Applies to JPEG only. Accepts number 0 - 100 where 0 is lowest and 100 is the highest quality. Or null for default.
     * @param bool $interlace Set to true for progressive JPEG. Applies to JPEG only.
     *
     * @return EditorInterface An instance of image editor.
     */
    public function save( $image, $file, $type = null, $quality = null, $interlace = false );

    /**
     * Write text to image.
     *
     * @param ImageInterface $image
     * @param string $text The text to be written.
     * @param int $size The font size. Defaults to 12.
     * @param int $x The distance from the left edge of the image to the left of the text. Defaults to 0.
     * @param int $y The distance from the top edge of the image to the baseline of the text. Defaults to 12 (equal to font size) so that the text is placed within the image.
     * @param Color $color The Color object. Default text color is black.
     * @param string $font Full path to font file. If blank, will default to Liberation Sans font.
     * @param int $angle Angle of text from 0 - 359. Defaults to 0.
     *
     * @return EditorInterface An instance of image editor.
     * @throws \Exception
     */
    public function text( &$image, $text, $size = 12, $x = 0, $y = 12, $color = null, $font = '', $angle = 0 );

}