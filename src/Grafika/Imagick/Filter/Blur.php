<?php

namespace Grafika\Imagick\Filter;

use Grafika\FilterInterface;
use Grafika\Imagick\Image;

/**
 * Blurs the image
 */
class Blur implements FilterInterface{

    protected $amount;

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
        return $image->getCore()->blurImage(1 * $this->amount, 0.5 * $this->amount);
    }

}