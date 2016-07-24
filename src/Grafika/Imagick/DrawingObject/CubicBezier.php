<?php
namespace Grafika\Imagick\DrawingObject;

use Grafika\DrawingObject\CubicBezier as Base;
use Grafika\DrawingObjectInterface;
use Grafika\Imagick\Image;
use Grafika\ImageInterface;

/**
 * Class CubicBezier
 * @package Grafika
 */
class CubicBezier extends Base implements DrawingObjectInterface
{

    /**
     * @param ImageInterface $image
     * @return Image
     */
    public function draw($image)
    {
        // Localize vars
        $width = $image->getWidth();
        $height = $image->getHeight();
        $imagick = $image->getCore();

        $draw = new \ImagickDraw();

        $strokeColor = new \ImagickPixel($this->getColor()->getHexString());
        $fillColor = new \ImagickPixel('rgba(0,0,0,0)');

        $draw->setStrokeOpacity(1);
        $draw->setStrokeColor($strokeColor);
        $draw->setFillColor($fillColor);

        $points = array(
            array('x'=> $this->point1[0], 'y'=> $this->point1[1]),
            array('x'=> $this->control1[0], 'y'=> $this->control1[1]),
            array('x'=> $this->control2[0], 'y'=> $this->control2[1]),
            array('x'=> $this->point2[0], 'y'=> $this->point2[1]),
        );
        $draw->bezier($points);

        // Render the draw commands in the ImagickDraw object
        $imagick->drawImage($draw);

        $type = $image->getType();
        $file = $image->getImageFile();
        return new Image($imagick, $file, $width, $height, $type); // Create new image with updated core
    }
}