<?php

namespace Grafika\Gd\Filter;

use Grafika\FilterInterface;
use Grafika\Gd\Image;

/**
 * Change the values for red, green and blue.
 */
class Colorize implements FilterInterface{

    protected $red; // -100 >= 0 >= 100
    protected $green; // -100 >= 0 >= 100
    protected $blue; // -100 >= 0 >= 100

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