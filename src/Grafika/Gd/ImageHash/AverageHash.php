<?php


namespace Grafika\Gd\ImageHash;

use Grafika\Gd\Editor;
use Grafika\Gd\Image;

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
 * @package Grafika\Gd\ImageHash
 */
class AverageHash
{

    /**
     * Generate and get the average hash of the image.
     *
     * @param Image $image
     *
     * @param Editor $editor
     *
     * @return string
     */
    public function hash($image, $editor)
    {
        // Resize the image.
        $width = 8;
        $height = 8;

        $image = clone $image; // Make sure we are working on the clone if Image is passed
        $editor->resizeExact($image, $width, $height); // Resize to exactly 8x8
        $gd = $image->getCore();

        // Create an array of greyscale pixel values.
        $pixels = array();
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = imagecolorat($gd, $x, $y);
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;

                $pixels[] = floor(($r + $g + $b) / 3); // Gray
            }
        }

        // Get the average pixel value.
        $average = floor(array_sum($pixels) / count($pixels));
        // Each hash bit is set based on whether the current pixels value is above or below the average.
        $hash = '';
        foreach ($pixels as $pixel) {
            if ($pixel > $average) {
                $hash .= '1';
            } else {
                $hash .= '0';
            }
        }
        return $hash;
    }
}