<?php

namespace Grafika\Gd\Filter;

use Grafika\FilterInterface;
use Grafika\Gd\Image;

/**
 * Pixelate an image.
 */
class Pixelate implements FilterInterface{

    /**
     * @var int $amount Pixelate size from >= 1
     */
    protected $amount;

    /**
     * Pixelate constructor.
     * @param int $amount The size of pixelation. >= 1
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

        imagefilter($image->getCore(), IMG_FILTER_PIXELATE, $this->amount, true);
        return $image;
    }

}