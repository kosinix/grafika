<?php

namespace Grafika\Imagick\Filter;

use Grafika\FilterInterface;
use Grafika\Imagick\Image;

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
        $image->getCore()->modulateImage(100 + $this->amount, 100, 100);
        return $image;
    }

}