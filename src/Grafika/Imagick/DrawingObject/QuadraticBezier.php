<?php
namespace Grafika\Imagick\DrawingObject;

use Grafika\DrawingObject\QuadraticBezier as Base;
use Grafika\DrawingObjectInterface;
use Grafika\Imagick\Image;
use Grafika\ImageInterface;

/**
 * Class QuadraticBezier
 * @package Grafika
 */
class QuadraticBezier extends Base implements DrawingObjectInterface
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



        list($x1, $y1) = $this->point1;
        list($x2, $y2) = $this->control;
        list($x3, $y3) = $this->point2;
        $draw->pathStart();
        $draw->pathMoveToAbsolute($x1, $y1);
        $draw->pathCurveToQuadraticBezierAbsolute(
            $x2, $y2,
            $x3, $y3
        );
        $draw->pathFinish();

        // Render the draw commands in the ImagickDraw object
        $imagick->drawImage($draw);

        $type = $image->getType();
        $file = $image->getImageFile();
        return new Image($imagick, $file, $width, $height, $type); // Create new image with updated core
    }
}