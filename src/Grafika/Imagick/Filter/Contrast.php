<?php

namespace Grafika\Imagick\Filter;

use Grafika\FilterInterface;
use Grafika\Imagick\Image;

/**
 * Change the contrast of an image. Contrast is the difference in luminance or colour that makes an object distinguishable.
 */
class Contrast implements FilterInterface{

    /**
     * @var int
     */
    protected $amount; // -100 >= 0 >= 100

    /**
     * Contrast constructor.
     * @param int $amount The amount of contrast to apply. >= -100 and <= -1 to reduce. 0 for no change. >= 1 and <= 100 to increase.
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

        $image->getCore()->sigmoidalContrastImage($this->amount > 0, $this->amount / 4, 0);
        return $image;
    }

}