<?php

namespace Grafika\Imagick\Filter;

use Grafika\FilterInterface;
use Grafika\Imagick\Image;

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
        $this->red = intval($red);
        $this->green = intval($green);
        $this->blue = intval($blue);
    }

    /**
     * @param Image $image
     *
     * @return Image
     */
    public function apply( $image ) {

        // normalize colorize levels
        $red = $this->normalizeLevel($this->red);
        $green = $this->normalizeLevel($this->green);
        $blue = $this->normalizeLevel($this->blue);
        $qrange = $image->getCore()->getQuantumRange();

        $image->getCore()->levelImage(0, $red, $qrange['quantumRangeLong'], \Imagick::CHANNEL_RED);
        $image->getCore()->levelImage(0, $green, $qrange['quantumRangeLong'], \Imagick::CHANNEL_GREEN);
        $image->getCore()->levelImage(0, $blue, $qrange['quantumRangeLong'], \Imagick::CHANNEL_BLUE);

        return $image;
    }

    private function normalizeLevel($level)
    {
        if ($level > 0) {
            return $level/5;
        } else {
            return ($level+100)/100;
        }
    }
}