<?php
namespace Grafika\Gd\DrawingObject;

use Grafika\DrawingObject\Polygon as Base;
use Grafika\DrawingObjectInterface;
use Grafika\Gd\Editor;

/**
 * Class Rectangle
 * @package Grafika
 */
class Polygon extends Base implements DrawingObjectInterface
{

    public function draw($image)
    {
        if(function_exists('imageantialias')){
            imageantialias($image->getCore(), true);
        }

        $points = $this->points();
        $count = count($this->points);


        // Create filled polygon
        if( null !== $this->fillColor){
            list($r, $g, $b, $alpha) = $this->getFillColor()->getRgba();
            $fillColorResource = imagecolorallocatealpha(
                $image->getCore(), $r, $g, $b,
                Editor::gdAlpha($alpha)
            );
            imagefilledpolygon($image->getCore(), $points,
                $count,
                $fillColorResource
            );
        }

        // Create polygon borders. It will be placed on top of the filled polygon (if present)
        if ( 0 < $this->getBorderSize() and null !== $this->borderColor) { // With border > 0 AND borderColor !== null
            list($r, $g, $b, $alpha) = $this->getBorderColor()->getRgba();
            $borderColorResource = imagecolorallocatealpha(
                $image->getCore(), $r, $g, $b,
                Editor::gdAlpha($alpha)
            );
            imagepolygon($image->getCore(), $points,
                $count,
                $borderColorResource
            );
        }
        return $image;
    }

    protected function points(){
        $points = array();
        foreach($this->points as $point){
            $points[] = $point[0];
            $points[] = $point[1];
        }
        if( count($points) < 6 ){
            throw new \Exception('Polygon needs at least 3 points.');
        }
        return $points;
    }
}