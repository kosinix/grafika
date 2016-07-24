<?php
namespace Grafika\Gd\DrawingObject;

use Grafika\DrawingObject\Ellipse as Base;
use Grafika\DrawingObjectInterface;
use Grafika\Gd\Editor;
use Grafika\ImageInterface;

/**
 * Class Ellipse
 * @package Grafika
 */
class Ellipse extends Base implements DrawingObjectInterface
{

    /**
     * TODO: Anti-aliased curves
     * @param ImageInterface $image
     * @return ImageInterface
     */
    public function draw($image)
    {

        list($x, $y) = $this->pos;
        $left = $x + $this->width / 2;
        $top = $y + $this->height / 2;

        if( null !== $this->fillColor ){
            list($r, $g, $b, $alpha) = $this->fillColor->getRgba();
            $fillColorResource = imagecolorallocatealpha($image->getCore(), $r, $g, $b, Editor::gdAlpha($alpha));
            imagefilledellipse($image->getCore(), $left, $top, $this->width, $this->height, $fillColorResource);
        }
        // Create borders. It will be placed on top of the filled ellipse (if present)
        if ( 0 < $this->getBorderSize() and null !== $this->borderColor) { // With border > 0 AND borderColor !== null
            list($r, $g, $b, $alpha) = $this->borderColor->getRgba();
            $borderColorResource = imagecolorallocatealpha($image->getCore(), $r, $g, $b, Editor::gdAlpha($alpha));
            imageellipse($image->getCore(), $left, $top, $this->width, $this->height, $borderColorResource);
        }

        return $image;
    }
}