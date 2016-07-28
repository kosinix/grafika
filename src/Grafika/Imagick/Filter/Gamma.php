<?php

namespace Grafika\Imagick\Filter;

use Grafika\FilterInterface;
use Grafika\Imagick\Image;

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

        $image->getCore()->gammaImage($this->amount);
        return $image;
    }

}