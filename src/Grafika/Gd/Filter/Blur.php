<?php

namespace Grafika\Gd\Filter;

use Grafika\FilterInterface;
use Grafika\Gd\Image;

/**
 * Blurs the image
 */
class Blur implements FilterInterface
{

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
    public function apply($image)
    {
        for ($i=0; $i < $this->amount; $i++) {
            imagefilter($image->getCore(), IMG_FILTER_GAUSSIAN_BLUR);
        }
        return $image;
    }
}