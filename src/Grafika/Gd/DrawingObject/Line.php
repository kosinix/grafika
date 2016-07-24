<?php
namespace Grafika\Gd\DrawingObject;

use Grafika\DrawingObject\Line as Base;
use Grafika\DrawingObjectInterface;
use Grafika\Gd\Image;

/**
 * Class Line
 * @package Grafika
 */
class Line extends Base implements DrawingObjectInterface
{

    /**
     * @param Image $image
     *
     * @return Image
     */
    public function draw($image)
    {

        list( $x1, $y1 ) = $this->point1;
        list( $x2, $y2 ) = $this->point2;
        list( $r, $g, $b ) = $this->color->getRgb();
        $color = imagecolorallocate( $image->getCore(), $r, $g, $b );
        if ( function_exists( 'imageantialias' ) ) { // Not available on some if PHP is not precompiled with it even if GD is enabled
            imageantialias( $image->getCore(), true );
        }
        imageline( $image->getCore(), $x1, $y1, $x2, $y2, $color );

        return $image;
    }


}