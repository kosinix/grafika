<?php

namespace Grafika\Gd\Filter;

use Grafika\FilterInterface;
use Grafika\Gd\Image;

/**
 * Sobel filter is an edge detection filter.
 * @link https://en.wikipedia.org/wiki/Sobel_operator
 */
class Sobel implements FilterInterface
{

    /**
     * @param Image $image
     *
     * @return Image
     */
    public function apply($image)
    {

        // Localize vars
        $width  = $image->getWidth();
        $height = $image->getHeight();
        $old     = $image->getCore();

        $pixels = array();
        $new    = imagecreatetruecolor($width, $height);
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                // row 0
                if ($x > 0 and $y > 0) {
                    $matrix[0][0] = $this->getColor($old, $pixels,$x - 1, $y - 1);
                } else {
                    $matrix[0][0] = $this->getColor($old, $pixels, $x, $y);
                }

                if ($y > 0) {
                    $matrix[1][0] = $this->getColor($old, $pixels, $x, $y - 1);
                } else {
                    $matrix[1][0] = $this->getColor($old, $pixels, $x, $y);
                }

                if ($x + 1 < $width and $y > 0) {
                    $matrix[2][0] = $this->getColor($old, $pixels, $x + 1, $y - 1);
                } else {
                    $matrix[2][0] = $this->getColor($old, $pixels, $x, $y);
                }

                // row 1
                if ($x > 0) {
                    $matrix[0][1] = $this->getColor($old, $pixels, $x - 1, $y);
                } else {
                    $matrix[0][1] = $this->getColor($old, $pixels, $x, $y);
                }

                if ($x + 1 < $width) {
                    $matrix[2][1] = $this->getColor($old, $pixels, $x + 1, $y);
                } else {
                    $matrix[2][1] = $this->getColor($old, $pixels, $x, $y);
                }

                // row 1
                if ($x > 0 and $y + 1 < $height) {
                    $matrix[0][2] = $this->getColor($old, $pixels, $x - 1, $y + 1);
                } else {
                    $matrix[0][2] = $this->getColor($old, $pixels, $x, $y);
                }

                if ($y + 1 < $height) {
                    $matrix[1][2] = $this->getColor($old, $pixels, $x, $y + 1);
                } else {
                    $matrix[1][2] = $this->getColor($old, $pixels, $x, $y);
                }

                if ($x + 1 < $width and $y + 1 < $height) {
                    $matrix[2][2] = $this->getColor($old, $pixels, $x + 1, $y + 1);
                } else {
                    $matrix[2][2] = $this->getColor($old, $pixels, $x, $y);
                }

                $edge = $this->convolve($matrix);
                $edge = intval($edge / 2);
                if ($edge > 255) {
                    $edge = 255;
                }
                $color = imagecolorallocate($new, $edge, $edge, $edge);
                imagesetpixel($new, $x, $y, $color);

            }
        }
        imagedestroy($old); // Free resource
        // Create and return new image with updated core
        return new Image(
            $new,
            $image->getImageFile(),
            $width,
            $height,
            $image->getType()
        );
    }

    private function convolve($matrix)
    {
        $gx = $matrix[0][0] + ($matrix[2][0] * -1) +
              ($matrix[0][1] * 2) + ($matrix[2][1] * -2) +
              $matrix[0][2] + ($matrix[2][2] * -1);

        $gy = $matrix[0][0] + ($matrix[1][0] * 2) + $matrix[2][0] +
              ($matrix[0][2] * -1) + ($matrix[1][2] * -2) + ($matrix[2][2] * -1);

        return sqrt(($gx * $gx) + ($gy * $gy));
    }

    private function getColor($gd, &$pixels, $x, $y)
    {
        if (isset($pixels[$x][$y])) {
            return $pixels[$x][$y];
        }
        $color = imagecolorat($gd, $x, $y);
        $r     = ($color >> 16) & 0xFF;
        $g     = ($color >> 8) & 0xFF;
        $b     = $color & 0xFF;

        return $pixels[$x][$y] = round($r * 0.3 + $g * 0.59 + $b * 0.11); // gray
    }
}