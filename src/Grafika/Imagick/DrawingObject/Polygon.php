<?php
namespace Grafika\Imagick\DrawingObject;

use Grafika\DrawingObject\Polygon as Base;
use Grafika\DrawingObjectInterface;

/**
 * Class Polygon
 * @package Grafika
 */
class Polygon extends Base implements DrawingObjectInterface{

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

        $draw->polygon($this->points());

        $image->getCore()->drawImage($draw);

        return $image;
    }

    protected function points(){
        $points = array();
        foreach($this->points as $i=>$pos){
            $points[$i] = array(
                'x' => $pos[0],
                'y' => $pos[1]
            );
        }
        if( count($points) < 3 ){
            throw new \Exception('Polygon needs at least 3 points.');
        }
        return $points;
    }

}