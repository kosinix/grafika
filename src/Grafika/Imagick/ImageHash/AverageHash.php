<?php


namespace Grafika\Imagick\ImageHash;

use Grafika\Imagick\Editor;
use Grafika\Imagick\Image;

/**
 * AverageHash
 *
 * Algorithm:
 * Reduce size. Remove high frequencies and detail by shrinking to 8x8 so that there are 64 total pixels.
 * Reduce color. The tiny 8x8 picture is converted to a grayscale.
 * Average the colors. Compute the mean value of the 64 colors.
 * Compute the bits. Each bit is simply set based on whether the color value is above or below the mean.
 * Construct the hash. Set the 64 bits into a 64-bit integer. The order does not matter, just as long as you are consistent.
 *
 * http://www.hackerfactor.com/blog/index.php?/archives/432-Looks-Like-It.html
 *
 * @package Grafika\Imagick\ImageHash
 */
class AverageHash
{

    /**
     * Generate and get the average hash of the image.
     *
     * @param Image $image
     *
     * @return string
     */
    public function hash(Image $image)
    {
        return ''; // TODO: Implementation
    }
}