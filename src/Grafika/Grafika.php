<?php

namespace Grafika;

use Grafika\Gd\DrawingObject\CubicBezier as GdCubicBezier;
use Grafika\Gd\DrawingObject\Ellipse as GdEllipse;
use Grafika\Gd\DrawingObject\Line as GdLine;
use Grafika\Gd\DrawingObject\Polygon as GdPolygon;
use Grafika\Gd\DrawingObject\QuadraticBezier as GdQuadraticBezier;
use Grafika\Gd\DrawingObject\Rectangle as GdRectangle;
use Grafika\Gd\Editor as GdEditor;
use Grafika\Gd\Filter\Dither as GdDither;
use Grafika\Gd\Filter\Blur as GdBlur;
use Grafika\Gd\Filter\Brightness as GdBrightness;
use Grafika\Gd\Filter\Colorize as GdColorize;
use Grafika\Gd\Filter\Contrast as GdContrast;
use Grafika\Gd\Filter\Gamma as GdGamma;
use Grafika\Gd\Filter\Grayscale as GdGrayscale;
use Grafika\Gd\Filter\Invert as GdInvert;
use Grafika\Gd\Filter\Pixelate as GdPixelate;
use Grafika\Gd\Filter\Sharpen as GdSharpen;
use Grafika\Gd\Filter\Sobel as GdSobel;
use Grafika\Gd\Image as GdImage;
use Grafika\Imagick\DrawingObject\CubicBezier as ImagickCubicBezier;
use Grafika\Imagick\DrawingObject\Ellipse as ImagickEllipse;
use Grafika\Imagick\DrawingObject\Line as ImagickLine;
use Grafika\Imagick\DrawingObject\Polygon as ImagickPolygon;
use Grafika\Imagick\DrawingObject\QuadraticBezier as ImagickQuadraticBezier;
use Grafika\Imagick\DrawingObject\Rectangle as ImagickRectangle;
use Grafika\Imagick\Editor as ImagickEditor;
use Grafika\Imagick\Filter\Blur as ImagickBlur;
use Grafika\Imagick\Filter\Brightness as ImagickBrightness;
use Grafika\Imagick\Filter\Colorize as ImagickColorize;
use Grafika\Imagick\Filter\Contrast as ImagickContrast;
use Grafika\Imagick\Filter\Gamma as ImagickGamma;
use Grafika\Imagick\Filter\Dither as ImagickDither;
use Grafika\Imagick\Filter\Grayscale as ImagickGrayscale;
use Grafika\Imagick\Filter\Invert as ImagickInvert;
use Grafika\Imagick\Filter\Pixelate as ImagickPixelate;
use Grafika\Imagick\Filter\Sharpen as ImagickSharpen;
use Grafika\Imagick\Filter\Sobel as ImagickSobel;
use Grafika\Imagick\Image as ImagickImage;

/**
 * Contains factory methods for detecting editors, creating editors and images.
 * @package Grafika
 */
class Grafika
{

    /**
     * Grafika root directory
     */
    const DIR = __DIR__;

    /**
     * @var array $editorList List of editors to evaluate.
     */
    private static $editorList = array('Imagick', 'Gd');

    /**
     * Return path to directory containing fonts used in text operations.
     *
     * @return string
     */
    public static function fontsDir()
    {
        $ds = DIRECTORY_SEPARATOR;
        return realpath(self::DIR . $ds . '..' . $ds . '..') . $ds . 'fonts';
    }


    /**
     * Change the editor list order of evaluation globally.
     *
     * @param array $editorList
     *
     * @throws \Exception
     */
    public static function setEditorList($editorList){
        if(!is_array($editorList)){
            throw new \Exception('$editorList must be an array.');
        }
        self::$editorList = $editorList;
    }

    /**
     * Detects and return the name of the first supported editor which can either be "Imagick" or "Gd".
     *
     * @param array $editorList Array of editor list names. Use this to change the order of evaluation for editors for this function call only. Default order of evaluation is Imagick then GD.
     *
     * @return string Name of available editor.
     * @throws \Exception Throws exception if there are no supported editors.
     */
    public static function detectAvailableEditor($editorList = null)
    {

        if(null === $editorList){
            $editorList = self::$editorList;
        }

        /* Get first supported editor instance. Order of editorList matter. */
        foreach ($editorList as $editorName) {
            if ('Imagick' === $editorName) {
                $editorInstance = new ImagickEditor();
            } else {
                $editorInstance = new GdEditor();
            }
            /** @var EditorInterface $editorInstance */
            if (true === $editorInstance->isAvailable()) {
                return $editorName;
            }
        }

        throw new \Exception('No supported editor.');
    }

    /**
     * Creates the first available editor.
     *
     * @param array $editorList Array of editor list names. Use this to change the order of evaluation for editors. Default order of evaluation is Imagick then GD.
     *
     * @return EditorInterface
     * @throws \Exception
     */
    public static function createEditor($editorList = array('Imagick', 'Gd'))
    {
        $editorName = self::detectAvailableEditor($editorList);
        if ('Imagick' === $editorName) {
            return new ImagickEditor();
        } else {
            return new GdEditor();
        }
    }

    /**
     * Create an image.
     * @param string $imageFile Path to image file.
     *
     * @return ImageInterface
     * @throws \Exception
     */
    public static function createImage($imageFile)
    {
        $editorName = self::detectAvailableEditor();
        if ('Imagick' === $editorName) {
            return ImagickImage::createFromFile($imageFile);
        } else {
            return GdImage::createFromFile($imageFile);
        }
    }


    /**
     * Create a blank image.
     *
     * @param int $width Width of image in pixels.
     * @param int $height Height of image in pixels.
     *
     * @return ImageInterface
     * @throws \Exception
     */
    public static function createBlankImage($width = 1, $height = 1)
    {
        $editorName = self::detectAvailableEditor();
        if ('Imagick' === $editorName) {
            return ImagickImage::createBlank($width, $height);
        } else {
            return GdImage::createBlank($width, $height);
        }
    }


    /**
     * Create a filter. Detects available editor to use.
     *
     * @param string $filterName The name of the filter.
     *
     * @return FilterInterface
     * @throws \Exception
     */
    public static function createFilter($filterName)
    {
        $editorName = self::detectAvailableEditor();
        $p = func_get_args();
        if ('Imagick' === $editorName) {
            switch ($filterName){
                case 'Blur':
                    return new ImagickBlur(
                        (array_key_exists(1,$p) ? $p[1] : 1)
                    );
                case 'Brightness':
                    return new ImagickBrightness(
                        $p[1]
                    );
                case 'Colorize':
                    return new ImagickColorize(
                        $p[1], $p[2], $p[3]
                    );
                case 'Contrast':
                    return new ImagickContrast(
                        $p[1]
                    );
                case 'Dither':
                    return new ImagickDither(
                        $p[1]
                    );
                case 'Gamma':
                    return new ImagickGamma(
                        $p[1]
                    );
                case 'Grayscale':
                    return new ImagickGrayscale();
                case 'Invert':
                    return new ImagickInvert();
                case 'Pixelate':
                    return new ImagickPixelate(
                        $p[1]
                    );
                case 'Sharpen':
                    return new ImagickSharpen(
                        $p[1]
                    );
                case 'Sobel':
                    return new ImagickSobel();
            }
            throw new \Exception('Invalid filter name.');
        } else {
            switch ($filterName){
                case 'Blur':
                    return new GdBlur(
                        (array_key_exists(1,$p) ? $p[1] : 1)
                    );
                case 'Brightness':
                    return new GdBrightness(
                        $p[1]
                    );
                case 'Colorize':
                    return new GdColorize(
                        $p[1], $p[2], $p[3]
                    );
                case 'Contrast':
                    return new GdContrast(
                        $p[1]
                    );
                case 'Dither':
                    return new GdDither(
                        $p[1]
                    );
                case 'Gamma':
                    return new GdGamma(
                        $p[1]
                    );
                case 'Grayscale':
                    return new GdGrayscale();
                case 'Invert':
                    return new GdInvert();
                case 'Pixelate':
                    return new GdPixelate(
                        $p[1]
                    );
                case 'Sharpen':
                    return new GdSharpen(
                        $p[1]
                    );
                case 'Sobel':
                    return new GdSobel();
            }
            throw new \Exception('Invalid filter name.');
        }
    }

    /**
     * Draws an object. Detects available editor to use.
     *
     * @param string $drawingObjectName The name of the DrawingObject.
     *
     * @return DrawingObjectInterface
     * @throws \Exception
     *
     * We use array_key_exist() instead of isset() to be able to detect a parameter with a NULL value.
     */
    public static function createDrawingObject($drawingObjectName)
    {
        $editorName = self::detectAvailableEditor();
        $p = func_get_args();
        if ('Imagick' === $editorName) {
            switch ($drawingObjectName){
                case 'CubicBezier':
                    return new ImagickCubicBezier(
                        $p[1],
                        $p[2],
                        $p[3],
                        $p[4],
                        (array_key_exists(5,$p) ? $p[5] : '#000000')
                    );
                case 'Ellipse':
                    return new ImagickEllipse(
                        $p[1],
                        $p[2],
                        (array_key_exists(3,$p) ? $p[3] : array(0,0)),
                        (array_key_exists(4,$p) ? $p[4] : 1),
                        (array_key_exists(5,$p) ? $p[5] : '#000000'),
                        (array_key_exists(6,$p) ? $p[6] : '#FFFFFF')
                    );
                case 'Line':
                    return new ImagickLine(
                        $p[1],
                        $p[2],
                        (array_key_exists(3,$p) ? $p[3] : 1),
                        (array_key_exists(4,$p) ? $p[4] : '#000000')
                    );
                case 'Polygon':
                    return new ImagickPolygon(
                        $p[1],
                        (array_key_exists(2,$p) ? $p[2] : 1),
                        (array_key_exists(3,$p) ? $p[3] : '#000000'),
                        (array_key_exists(4,$p) ? $p[4] : '#FFFFFF')
                    );
                case 'Rectangle':
                    return new ImagickRectangle(
                        $p[1],
                        $p[2],
                        (array_key_exists(3,$p) ? $p[3] : array(0,0)),
                        (array_key_exists(4,$p) ? $p[4] : 1),
                        (array_key_exists(5,$p) ? $p[5] : '#000000'),
                        (array_key_exists(6,$p) ? $p[6] : '#FFFFFF')
                    );
                case 'QuadraticBezier':
                    return new ImagickQuadraticBezier(
                        $p[1],
                        $p[2],
                        $p[3],
                        (array_key_exists(4,$p) ? $p[4] : '#000000')
                    );

            }
            throw new \Exception('Invalid drawing object name.');
        } else {
            switch ($drawingObjectName) {
                case 'CubicBezier':
                    return new GdCubicBezier(
                        $p[1],
                        $p[2],
                        $p[3],
                        $p[4],
                        (array_key_exists(5,$p) ? $p[5] : '#000000')
                    );
                case 'Ellipse':
                    return new GdEllipse(
                        $p[1],
                        $p[2],
                        (array_key_exists(3,$p) ? $p[3] : array(0,0)),
                        (array_key_exists(4,$p) ? $p[4] : 1),
                        (array_key_exists(5,$p) ? $p[5] : '#000000'),
                        (array_key_exists(6,$p) ? $p[6] : '#FFFFFF')
                    );
                case 'Line':
                    return new GdLine(
                        $p[1],
                        $p[2],
                        (array_key_exists(3,$p) ? $p[3] : 1),
                        (array_key_exists(4,$p) ? $p[4] : '#000000')
                    );
                case 'Polygon':
                    return new GdPolygon(
                        $p[1],
                        (array_key_exists(2,$p) ? $p[2] : 1),
                        (array_key_exists(3,$p) ? $p[3] : '#000000'),
                        (array_key_exists(4,$p) ? $p[4] : '#FFFFFF')
                    );
                case 'Rectangle':
                    return new GdRectangle(
                        $p[1],
                        $p[2],
                        (array_key_exists(3,$p) ? $p[3] : array(0,0)),
                        (array_key_exists(4,$p) ? $p[4] : 1),
                        (array_key_exists(5,$p) ? $p[5] : '#000000'),
                        (array_key_exists(6,$p) ? $p[6] : '#FFFFFF')
                    );
                case 'QuadraticBezier':
                    return new GdQuadraticBezier(
                        $p[1],
                        $p[2],
                        $p[3],
                        (array_key_exists(4,$p) ? $p[4] : '#000000')
                    );
            }
            throw new \Exception('Invalid drawing object name.');
        }
    }


}