<?php

namespace Grafika\Imagick\Filter;

use Grafika\FilterInterface;
use Grafika\Imagick\Image;

/**
 * Change contrast of image
 */
class Contrast implements FilterInterface{

    protected $amount; // -100 >= 0 >= 100

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

        $image->getCore()->sigmoidalContrastImage($this->amount > 0, $this->amount / 4, 0);
        return $image;
    }

}