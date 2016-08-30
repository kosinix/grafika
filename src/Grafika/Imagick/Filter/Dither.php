<?php

namespace Grafika\Imagick\Filter;

use Grafika\FilterInterface;
use Grafika\Imagick\Image;

/**
 * Dither image. Dithering will turn the image black and white and add noise.
 */
class Dither implements FilterInterface{

    /**
     * @var string Dithering algorithm to use.
     */
    private $type;

    /**
     * Dither an image.
     *
     * @param string $type Dithering algorithm to use. Options: diffusion, ordered. Defaults to diffusion.
     */
    public function __construct( $type = 'diffusion' )
    {
        $this->type = $type;
    }

    /**
     * Apply filter.
     *
     * @param Image $image
     *
     * @return Image
     * @throws \Exception
     */
    public function apply( $image ) {
        if ( $this->type === 'ordered' ) {
            return $this->ordered( $image );
        } else if ( $this->type === 'diffusion' ) {
            return $this->diffusion( $image );
        }
        throw new \Exception( sprintf( 'Invalid dither type "%s".', $this->type ) );
    }

    /**
     * Dither using error diffusion.
     *
     * @param Image $image
     *
     * @return Image
     */
    private function diffusion( $image ){
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

    /**
     * Dither by applying a threshold map.
     *
     * @param Image $image
     *
     * @return Image
     */
    private function ordered( $image ) {

        // Localize vars
        $width = $image->getWidth();
        $height = $image->getHeight();

        $thresholdMap = array(
            array( 15, 135, 45, 165 ),
            array( 195, 75, 225, 105 ),
            array( 60, 180, 30, 150 ),
            array( 240, 120, 210, 90 )
        );

        // Loop using image1
        $pixelIterator = $image->getCore()->getPixelIterator();
        foreach ($pixelIterator as $y => $rows) { /* Loop through pixel rows */
            foreach ( $rows as $x => $px ) { /* Loop through the pixels in the row (columns) */
                /**
                 * @var $px \ImagickPixel */
                $rgba = $px->getColor();

                $gray = round($rgba['r'] * 0.3 + $rgba['g'] * 0.59 + $rgba['b'] * 0.11);

                $threshold = $thresholdMap[ $x % 4 ][ $y % 4 ];
                $oldPixel  = ( $gray + $threshold ) / 2;
                if ( $oldPixel <= 127 ) { // Determine if black or white. Also has the benefit of clipping excess value
                    $newPixel = 0;
                } else {
                    $newPixel = 255;
                }

                // Current pixel
                $px->setColor("rgb($newPixel,$newPixel,$newPixel)");

            }
            $pixelIterator->syncIterator(); /* Sync the iterator, this is important to do on each iteration */
        }

        $type = $image->getType();
        $file = $image->getImageFile();
        $image = $image->getCore();

        return new Image( $image, $file, $width, $height, $type ); // Create new image with updated core

    }
}