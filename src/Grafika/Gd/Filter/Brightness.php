<?php

namespace Grafika\Gd\Filter;

use Grafika\FilterInterface;
use Grafika\Gd\Image;

/**
 * Changes the brightness TODO: param checks
 */
class Brightness implements FilterInterface{

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
        imagefilter($image->getCore(), IMG_FILTER_BRIGHTNESS, ($this->amount * 2.55));
        return $image;
    }

}