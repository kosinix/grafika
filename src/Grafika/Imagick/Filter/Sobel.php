<?php

namespace Grafika\Imagick\Filter;

use Grafika\FilterInterface;
use Grafika\Imagick\Image;

/**
 * Sobel filter is an edge detection filter.
 * @link https://en.wikipedia.org/wiki/Sobel_operator
 */
class Sobel implements FilterInterface{


    /**
     * @param Image $image
     *
     * @return Image
     */
    public function apply( $image ) {

        $pixels = array();
        $finalPx = array();
        // Localize vars
        $width = $image->getWidth();
        $height = $image->getHeight();

        // Loop
        $pixelIterator = $image->getCore()->getPixelIterator();
        foreach ($pixelIterator as $y => $rows) { /* Loop through pixel rows */
            foreach ( $rows as $x => $px ) { /* Loop through the pixels in the row (columns) */

                // row 0
                if ($x > 0 and $y > 0) {
                    $matrix[0][0] = $this->getColor($px, $pixels, $x - 1, $y - 1);
                } else {
                    $matrix[0][0] = $this->getColor($px, $pixels, $x, $y);
                }

                if ($y > 0) {
                    $matrix[1][0] = $this->getColor($px, $pixels, $x, $y - 1);
                } else {
                    $matrix[1][0] = $this->getColor($px, $pixels, $x, $y);
                }

                if ($x + 1 < $width and $y > 0) {
                    $matrix[2][0] = $this->getColor($px, $pixels, $x + 1, $y - 1);
                } else {
                    $matrix[2][0] = $this->getColor($px, $pixels, $x, $y);
                }

                // row 1
                if ($x > 0) {
                    $matrix[0][1] = $this->getColor($px, $pixels, $x - 1, $y);
                } else {
                    $matrix[0][1] = $this->getColor($px, $pixels, $x, $y);
                }

                if ($x + 1 < $width) {
                    $matrix[2][1] = $this->getColor($px, $pixels, $x + 1, $y);
                } else {
                    $matrix[2][1] = $this->getColor($px, $pixels, $x, $y);
                }

                // row 1
                if ($x > 0 and $y + 1 < $height) {
                    $matrix[0][2] = $this->getColor($px, $pixels, $x - 1, $y + 1);
                } else {
                    $matrix[0][2] = $this->getColor($px, $pixels, $x, $y);
                }

                if ($y + 1 < $height) {
                    $matrix[1][2] = $this->getColor($px, $pixels, $x, $y + 1);
                } else {
                    $matrix[1][2] = $this->getColor($px, $pixels, $x, $y);
                }

                if ($x + 1 < $width and $y + 1 < $height) {
                    $matrix[2][2] = $this->getColor($px, $pixels, $x + 1, $y + 1);
                } else {
                    $matrix[2][2] = $this->getColor($px, $pixels, $x, $y);
                }

                $edge = $this->convolve($matrix);
                $edge = intval($edge / 2);
                if ($edge > 255) {
                    $edge = 255;
                }

                /**
                 * @var \ImagickPixel $px Current pixel.
                 */
                $finalPx[] = $edge; // R
                $finalPx[] = $edge; // G
                $finalPx[] = $edge; // B

            }
            $pixelIterator->syncIterator(); /* Sync the iterator, this is important to do on each iteration */
        }

        $new = new \Imagick();
        $new->newImage($width, $height, new \ImagickPixel('black'));
        /* Import the pixels into image.
        width * height * strlen("RGB") must match count($pixels) */
        $new->importImagePixels(0, 0, $width, $height, "RGB", \Imagick::PIXEL_CHAR, $finalPx);

        $type = $image->getType();
        $file = $image->getImageFile();

        return new Image( $new, $file, $width, $height, $type ); // Create new image with updated core

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

    /**
     * @param \ImagickPixel $px
     * @param array $pixels
     * @param int $x
     * @param int $y
     *
     * @return float
     */
    private function getColor($px, &$pixels, $x, $y)
    {
        if (isset($pixels[$x][$y])) {
            return $pixels[$x][$y];
        }
        $rgba = $px->getColor();
        return $pixels[$x][$y] = round($rgba['r'] * 0.3 + $rgba['g'] * 0.59 + $rgba['b'] * 0.11); // gray
    }
}