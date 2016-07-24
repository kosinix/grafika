<?php
namespace Grafika\Gd\DrawingObject;

use Grafika\DrawingObject\Rectangle as Base;
use Grafika\DrawingObjectInterface;
use Grafika\Gd\Editor;

/**
 * Class Rectangle
 * @package Grafika
 */
class Rectangle extends Base implements DrawingObjectInterface
{

    public function draw($image)
    {
        $x1 = $this->pos[0];
        $x2 = $x1 + $this->getWidth();
        $y1 = $this->pos[1];
        $y2 = $y1 + $this->getHeight();

        if( null !== $this->fillColor ){
            list($r, $g, $b, $alpha) = $this->fillColor->getRgba();
            $fillColorResource = imagecolorallocatealpha($image->getCore(), $r, $g, $b, Editor::gdAlpha($alpha));
            imagefilledrectangle($image->getCore(), $x1, $y1, $x2, $y2, $fillColorResource);
        }
        // Create borders. It will be placed on top of the filled rectangle (if present)
        if ( 0 < $this->getBorderSize() and null !== $this->borderColor) { // With border > 0 AND borderColor !== null
            list($r, $g, $b, $alpha) = $this->borderColor->getRgba();
            $borderColorResource = imagecolorallocatealpha($image->getCore(), $r, $g, $b, Editor::gdAlpha($alpha));
            imagerectangle($image->getCore(), $x1, $y1, $x2, $y2, $borderColorResource);
        }

        return $image;
    }
}