<?php


namespace Grafika\Gd\ImageHash;

use Grafika\Gd\Editor;
use Grafika\Gd\Image;

/**
 * DifferenceHash
 *
 * Algorithm:
 * Reduce size. The fastest way to remove high frequencies and detail is to shrink the image. In this case, shrink it to 9x8 so that there are 72 total pixels.
 * Reduce color. Convert the image to a grayscale picture. This changes the hash from 72 pixels to a total of 72 colors.
 * Compute the difference. The algorithm works on the difference between adjacent pixels. This identifies the relative gradient direction. In this case, the 9 pixels per row yields 8 differences between adjacent pixels. Eight rows of eight differences becomes 64 bits.
 * Assign bits. Each bit is simply set based on whether the left pixel is brighter than the right pixel.
 *
 * http://www.hackerfactor.com/blog/index.php?/archives/529-Kind-of-Like-That.html
 *
 * @package Grafika\Gd\ImageHash
 */
class DifferenceHash
{

    /**
     * Generate and get the difference hash of image.
     *
     * @param Image $image
     *
     * @param Editor $editor
     *
     * @return string
     */
    public function hash($image, $editor)
    {
        $width  = 9;
        $height = 8;

        $image = clone $image; // Make sure we are working on the clone if Image is passed
        $editor->resizeExact($image, $width, $height); // Resize to exactly 9x8
        $gd = $image->getCore();

        // Build hash
        $hash = '';
        for ($y = 0; $y < $height; $y++) {
            // Get the pixel value for the leftmost pixel.
            $rgba = imagecolorat($gd, 0, $y);
            $r    = ($rgba >> 16) & 0xFF;
            $g    = ($rgba >> 8) & 0xFF;
            $b    = $rgba & 0xFF;

            $left = floor(($r + $g + $b) / 3);
            for ($x = 1; $x < $width; $x++) {
                // Get the pixel value for each pixel starting from position 1.
                $rgba  = imagecolorat($gd, $x, $y);
                $r     = ($rgba >> 16) & 0xFF;
                $g     = ($rgba >> 8) & 0xFF;
                $b     = $rgba & 0xFF;
                $right = floor(($r + $g + $b) / 3);
                // Each hash bit is set based on whether the left pixel is brighter than the right pixel.
                if ($left > $right) {
                    $hash .= '1';
                } else {
                    $hash .= '0';
                }
                // Prepare the next loop.
                $left = $right;
            }
        }
        $editor->free( $image );
        return $hash;
    }
}