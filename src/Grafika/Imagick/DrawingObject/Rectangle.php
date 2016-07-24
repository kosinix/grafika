<?php
namespace Grafika\Imagick\DrawingObject;

use Grafika\DrawingObject\Rectangle as Base;
use Grafika\DrawingObjectInterface;

/**
 * Class Rectangle
 * @package Grafika
 */
class Rectangle extends Base implements DrawingObjectInterface{

    public function draw( $image ) {

        $draw = new \ImagickDraw();
        $draw->setStrokeWidth($this->borderSize);

        if(null !== $this->fillColor) {
            $fillColor = new \ImagickPixel( $this->fillColor->getHexString() );
            $draw->setFillColor($fillColor);
        } else {
            $draw->setFillOpacity(0);
        }

        if(null !== $this->borderColor) {
            $borderColor = new \ImagickPixel( $this->borderColor->getHexString() );
            $draw->setStrokeColor($borderColor);
        } else {
            $draw->setStrokeOpacity(0);
        }



        $x1 = $this->pos[0];
        $x2 = $x1 + $this->getWidth();
        $y1 = $this->pos[1];
        $y2 = $y1 + $this->getHeight();

        $draw->rectangle( $x1, $y1, $x2, $y2 );

        $image->getCore()->drawImage($draw);

        return $image;
    }

}