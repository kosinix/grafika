<?php

namespace Grafika\Gd\Filter;

use Grafika\FilterInterface;
use Grafika\Gd\Image;

/**
 * Dither image using Floyd-Steinberg algorithm. Dithering will reduce the color to black and white and add noise.
 */
class Dither implements FilterInterface{
    /**
     * Dither an image.
     */
    public function __construct()
    {
    }


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
        $pixel = array();

        // Localize vars
        $width = $image->getWidth();
        $height = $image->getHeight();
        $gd = $image->getCore();

        for ( $y = 0; $y < $height; $y+=1 ) {
            for ( $x = 0; $x < $width; $x+=1 ) {

                $color = imagecolorat( $gd, $x, $y );
                $r = ($color >> 16) & 0xFF;
                $g = ($color >> 8) & 0xFF;
                $b = $color & 0xFF;

                $gray = round($r * 0.3 + $g * 0.59 + $b * 0.11);

                if(isset($pixel[$x][$y])){ // Add errors to color if there are
                    $gray += $pixel[$x][$y];
                }

                if ( $gray <= 127 ) { // Determine if black or white. Also has the benefit of clipping excess val due to adding the error
                    $blackOrWhite = 0;
                } else {
                    $blackOrWhite = 255;
                }

                $oldPixel = $gray;
                $newPixel = $blackOrWhite;

                // Current pixel
                imagesetpixel( $gd, $x, $y,
                    imagecolorallocate( $gd,
                        $newPixel,
                        $newPixel,
                        $newPixel
                    )
                );

                $qError = $oldPixel - $newPixel; // Quantization error

                // Propagate error on neighbor pixels
                if ( $x + 1 < $width ) {
                    $pixel[$x+1][$y] = (isset($pixel[$x+1][$y]) ? $pixel[$x+1][$y] : 0) + ($qError * (7 / 16));
                }

                if ( $x - 1 > 0 and $y + 1 < $height ) {
                    $pixel[$x-1][$y+1] = (isset($pixel[$x-1][$y+1]) ? $pixel[$x-1][$y+1] : 0) + ($qError * (3 / 16));
                }

                if ( $y + 1 < $height ) {
                    $pixel[$x][$y+1] = (isset($pixel[$x][$y+1]) ? $pixel[$x][$y+1] : 0) + ($qError * (5 / 16));
                }

                if ( $x + 1 < $width and $y + 1 < $height ) {
                    $pixel[$x+1][$y+1] = (isset($pixel[$x+1][$y+1]) ? $pixel[$x+1][$y+1] : 0) + ($qError * (1 / 16));
                }

            }
        }
        $type = $image->getType();
        $file = $image->getImageFile();

        return new Image( $gd, $file, $width, $height, $type ); // Create new image with updated core
    }
}