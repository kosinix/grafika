<?php

namespace Grafika\Imagick\Filter;

use Grafika\FilterInterface;
use Grafika\Imagick\Image;

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

        $size = $this->amount;
        $width = $image->getWidth();
        $height = $image->getHeight();
        $image->getCore()->scaleImage(max(1, ($width / $size)), max(1, ($height / $size)));
        $image->getCore()->scaleImage($width, $height);
        return $image;
    }

}