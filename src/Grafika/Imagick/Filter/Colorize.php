<?php

namespace Grafika\Imagick\Filter;

use Grafika\FilterInterface;
use Grafika\Imagick\Image;

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