<?php

namespace Grafika\Gd\Filter;

use Grafika\FilterInterface;
use Grafika\Gd\Image;

/**
 * Change the values for red, green and blue in an image.
 */
class Colorize implements FilterInterface{

    /**
     * @var int
     */
    protected $red; // -100 >= 0 >= 100
    /**
     * @var int
     */
    protected $green; // -100 >= 0 >= 100
    /**
     * @var int
     */
    protected $blue; // -100 >= 0 >= 100

    /**
     * Colorize constructor.
     * @param int $red The amount of red colors. >= -100 and <= -1 to reduce. 0 for no change. >= 1 and <= 100 to add.
     * @param int $green The amount of green colors. >= -100 and <= -1 to reduce. 0 for no change. >= 1 and <= 100 to add.
     * @param int $blue The amount of blue colors. >= -100 and <= -1 to reduce. 0 for no change. >= 1 and <= 100 to add.
     */
    public function __construct($red, $green, $blue)
    {
        $this->red = round($red * 2.55);
        $this->green = round($green * 2.55);
        $this->blue = round($blue * 2.55);
    }

    /**
     * @param Image $image
     *
     * @return Image
     */
    public function apply( $image ) {

        imagefilter($image->getCore(), IMG_FILTER_COLORIZE, $this->red, $this->green, $this->blue);
        return $image;
    }

}