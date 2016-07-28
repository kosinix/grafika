<?php

namespace Grafika\Gd\Filter;

use Grafika\FilterInterface;
use Grafika\Gd\Image;

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

        imagefilter($image->getCore(), IMG_FILTER_CONTRAST, ($this->amount * -1));
        return $image;
    }

}