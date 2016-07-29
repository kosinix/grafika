<?php

namespace Grafika\Imagick\Filter;

use Grafika\FilterInterface;
use Grafika\Imagick\Image;

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
        $image->getCore()->modulateImage(100 + $this->amount, 100, 100);
        return $image;
    }

}