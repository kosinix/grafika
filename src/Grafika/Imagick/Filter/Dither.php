<?php

namespace Grafika\Imagick\Filter;

use Grafika\FilterInterface;
use Grafika\Imagick\Image;

/**
 * Dither image using Floyd-Steinberg algorithm. Dithering will turn the image black and white and add noise.
 */
class Dither implements FilterInterface{


    /**
     * @param Image $image
     *
     * @return Image
     */
    public function apply( $image ) {
        return $this->floydSteinberg( $image );
    }

    /**
     * @param Image $image
     *
     * @return Image
     */
    private function floydSteinberg( $image ){
        $pixels = array();

        // Localize vars
        $width = $image->getWidth();
        $height = $image->getHeight();

        // Loop using image1
        $pixelIterator = $image->getCore()->getPixelIterator();
        foreach ($pixelIterator as $y => $rows) { /* Loop through pixel rows */
            foreach ( $rows as $x => $px ) { /* Loop through the pixels in the row (columns) */
                /**
                 * @var $px \ImagickPixel */
                $rgba = $px->getColor();

                $gray = round($rgba['r'] * 0.3 + $rgba['g'] * 0.59 + $rgba['b'] * 0.11);

                if(isset($pixels[$x][$y])){ // Add errors to color if there are
                    $gray += $pixels[$x][$y];
                }

                if ( $gray <= 127 ) { // Determine if black or white. Also has the benefit of clipping excess val due to adding the error
                    $blackOrWhite = 0;
                } else {
                    $blackOrWhite = 255;
                }

                $oldPixel = $gray;
                $newPixel = $blackOrWhite;

                // Current pixel
                $px->setColor("rgb($newPixel,$newPixel,$newPixel)");

                $qError = $oldPixel - $newPixel; // Quantization error

                // Propagate error on neighbor pixels
                if ( $x + 1 < $width ) {
                    $pixels[$x+1][$y] = (isset($pixels[$x+1][$y]) ? $pixels[$x+1][$y] : 0) + ($qError * (7 / 16));
                }

                if ( $x - 1 > 0 and $y + 1 < $height ) {
                    $pixels[$x-1][$y+1] = (isset($pixels[$x-1][$y+1]) ? $pixels[$x-1][$y+1] : 0) + ($qError * (3 / 16));
                }

                if ( $y + 1 < $height ) {
                    $pixels[$x][$y+1] = (isset($pixels[$x][$y+1]) ? $pixels[$x][$y+1] : 0) + ($qError * (5 / 16));
                }

                if ( $x + 1 < $width and $y + 1 < $height ) {
                    $pixels[$x+1][$y+1] = (isset($pixels[$x+1][$y+1]) ? $pixels[$x+1][$y+1] : 0) + ($qError * (1 / 16));
                }

            }
            $pixelIterator->syncIterator(); /* Sync the iterator, this is important to do on each iteration */
        }

        $type = $image->getType();
        $file = $image->getImageFile();
        $image = $image->getCore();

        return new Image( $image, $file, $width, $height, $type ); // Create new image with updated core

    }
}