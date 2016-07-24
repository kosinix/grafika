<?php
namespace Grafika\Imagick\DrawingObject;

use Grafika\DrawingObject\Ellipse as Base;
use Grafika\DrawingObjectInterface;
use Grafika\ImageInterface;

/**
 * Class Ellipse
 * @package Grafika
 */
class Ellipse extends Base implements DrawingObjectInterface
{

    /**
     * @param ImageInterface $image
     * @return ImageInterface
     */
    public function draw($image)
    {

        $strokeColor = new \ImagickPixel($this->getBorderColor()->getHexString());
        $fillColor = new \ImagickPixel($this->getFillColor()->getHexString());

        $draw = new \ImagickDraw();
        $draw->setStrokeColor($strokeColor);
        $draw->setFillColor($fillColor);

        $draw->setStrokeWidth($this->borderSize);

        list($x, $y) = $this->pos;
        $left = $x + $this->width / 2;
        $top = $y + $this->height / 2;
        $draw->ellipse($left, $top, $this->width/2, $this->height/2, 0, 360);

        $image->getCore()->drawImage($draw);

        return $image;
    }
}