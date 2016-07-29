<?php

namespace Grafika\Gd\Filter;

use Grafika\FilterInterface;
use Grafika\Gd\Image;

/**
 * Performs a gamma correction on an image.
 */
class Gamma implements FilterInterface{

    /**
     * @var float
     */
    protected $amount; // >= 1.0

    /**
     * Gamma constructor.
     * @param float $amount The amount of gamma correction to apply. >= 1.0
     */
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