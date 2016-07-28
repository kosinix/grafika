<?php

namespace Grafika\Gd\Filter;

use Grafika\FilterInterface;
use Grafika\Gd\Image;

/**
 * Performs a gamma correction
 */
class Gamma implements FilterInterface{

    protected $amount; // >= 1.0

    public function __construct($amount)
    {
        $this->amount = (float) $amount;
    }

    /**
     * @param Image $image
     *
     * @return Image
     */
    public function apply( $image ) {

        imagegammacorrect($image->getCore(), 1, $this->amount);
        return $image;
    }

}