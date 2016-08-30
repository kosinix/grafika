<?php

namespace Grafika\Gd\Filter;

use Grafika\FilterInterface;
use Grafika\Gd\Image;

/**
 * Dither image. Dithering will reduce the color to black and white and add noise.
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
        $pixel = array();

        // Localize vars
        $width = $image->getWidth();
        $height = $image->getHeight();
        $old = $image->getCore();

        $new = imagecreatetruecolor($width, $height);

        for ( $y = 0; $y < $height; $y+=1 ) {
            for ( $x = 0; $x < $width; $x+=1 ) {

                $color = imagecolorat( $old, $x, $y );
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
                imagesetpixel( $new, $x, $y,
                    imagecolorallocate( $new,
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

        imagedestroy($old); // Free resource
        // Create new image with updated core
        return new Image(
            $new,
            $image->getImageFile(),
            $width,
            $height,
            $image->getType()
        );
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
        $width  = $image->getWidth();
        $height = $image->getHeight();
        $old    = $image->getCore();

        $new = imagecreatetruecolor( $width, $height );

        $thresholdMap = array(
            array( 15, 135, 45, 165 ),
            array( 195, 75, 225, 105 ),
            array( 60, 180, 30, 150 ),
            array( 240, 120, 210, 90 )
        );

        for ( $y = 0; $y < $height; $y += 1 ) {
            for ( $x = 0; $x < $width; $x += 1 ) {

                $color = imagecolorat( $old, $x, $y );
                $r     = ( $color >> 16 ) & 0xFF;
                $g     = ( $color >> 8 ) & 0xFF;
                $b     = $color & 0xFF;

                $gray = round( $r * 0.3 + $g * 0.59 + $b * 0.11 );

                $threshold = $thresholdMap[ $x % 4 ][ $y % 4 ];
                $oldPixel  = ( $gray + $threshold ) / 2;
                if ( $oldPixel <= 127 ) { // Determine if black or white. Also has the benefit of clipping excess value
                    $newPixel = 0;
                } else {
                    $newPixel = 255;
                }

                // Current pixel
                imagesetpixel( $new, $x, $y,
                    imagecolorallocate( $new,
                        $newPixel,
                        $newPixel,
                        $newPixel
                    )
                );

            }
        }

        imagedestroy( $old ); // Free resource
        // Create new image with updated core
        return new Image(
            $new,
            $image->getImageFile(),
            $width,
            $height,
            $image->getType()
        );
    }
}