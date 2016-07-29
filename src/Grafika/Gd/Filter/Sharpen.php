<?php

namespace Grafika\Gd\Filter;

use Grafika\FilterInterface;
use Grafika\Gd\Image;

/**
 * Sharpen an image.
 */
class Sharpen implements FilterInterface{

    /**
     * @var int $amount 
     */
    protected $amount;

    /**
     * Sharpen constructor.
     * @param int $amount Amount of sharpening from >= 1 to <= 100
     */
    public function __construct($amount)
    {
        $this->amount = (int) $amount;
    }

    /**
     * @param Image $image
     *
     * @return Image
     */
    public function apply( $image ) {
        $amount = $this->amount;
        // build matrix
        $min = $amount >= 10 ? $amount * -0.01 : 0;
        $max = $amount * -0.025;
        $abs = ((4 * $min + 4 * $max) * -1) + 1;
        $div = 1;
        $matrix = array(
            array($min, $max, $min),
            array($max, $abs, $max),
            array($min, $max, $min)
        );
        // apply the matrix
        imageconvolution($image->getCore(), $matrix, $div, 0);
        return $image;
    }

}