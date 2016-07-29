<?php

namespace Grafika\Imagick\Filter;

use Grafika\FilterInterface;
use Grafika\Imagick\Image;

/**
 * Blurs the image.
 */
class Blur implements FilterInterface{

    /**
     * @var int
     */
    protected $amount;

    /**
     * Blur constructor.
     * @param int $amount The amount of blur to apply. Possible values 1-100.
     */
    public function __construct($amount = 1)
    {
        $this->amount = (int) $amount;
    }

    /**
     * @param Image $image
     *
     * @return Image
     */
    public function apply( $image ) {
        $image->getCore()->blurImage(1 * $this->amount, 0.5 * $this->amount);
        return $image;
    }

}