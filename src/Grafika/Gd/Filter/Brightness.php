<?php

namespace Grafika\Gd\Filter;

use Grafika\FilterInterface;
use Grafika\Gd\Image;

/**
 * Change the image brightness.
 *
 * TODO: param checks
 */
class Brightness implements FilterInterface{

    /**
     * @var int
     */
    protected $amount; // -100 >= 0 >= 100

    /**
     * Brightness constructor.
     * @param int $amount The amount of brightness to apply. >= -100 and <= -1 to darken. 0 for no change. >= 1 and <= 100 to brighten.
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
        imagefilter($image->getCore(), IMG_FILTER_BRIGHTNESS, ($this->amount * 2.55));
        return $image;
    }

}