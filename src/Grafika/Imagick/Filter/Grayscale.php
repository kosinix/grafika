<?php

namespace Grafika\Imagick\Filter;

use Grafika\FilterInterface;
use Grafika\Imagick\Image;

/**
 * Turn image into grayscale.
 */
class Grayscale implements FilterInterface{

    /**
     * @param Image $image
     *
     * @return Image
     */
    public function apply( $image ) {
        $image->getCore()->modulateImage(100, 0, 100);
        return $image;
    }

}