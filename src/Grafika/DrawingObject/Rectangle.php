<?php
namespace Grafika\DrawingObject;

use Grafika\Color;

/**
 * Base class
 * @package Grafika
 */
abstract class Rectangle
{
    /**
     * Image width in pixels
     * @var int
     */
    protected $width;

    /**
     * Image height in pixels
     * @var int
     */
    protected $height;

    /**
     * X and Y position in an array.
     * @var array
     */
    protected $pos;

    /**
     * @var int
     */
    protected $borderSize;

    /**
     * @var Color
     */
    protected $fillColor;

    /**
     * @var Color
     */
    protected $borderColor;

    /**
     * Creates a rectangle.
     *
     * @param int $width Width of rectangle in pixels.
     * @param int $height Height in pixels.
     * @param array $pos Array of X and Y position. X is the distance in pixels from the left of the canvass to the left of the rectangle. Y is the distance from the top of the canvass to the top of the rectangle. Defaults to array(0,0).
     * @param int $borderSize Size of the border in pixels. Defaults to 1 pixel. Set to 0 for no border.
     * @param Color|string|null $borderColor Border color. Defaults to black. Set to null for no color.
     * @param Color|string|null $fillColor Fill color. Defaults to white. Set to null for no color.
     */
    public function __construct(
        $width,
        $height,
        $pos = array(0, 0),
        $borderSize = 1,
        $borderColor = '#000000',
        $fillColor = '#FFFFFF'
    ) {
        if (is_string($borderColor)) {
            $borderColor = new Color($borderColor);
        }
        if (is_string($fillColor)) {
            $fillColor = new Color($fillColor);
        }
        $this->width = $width;
        $this->height = $height;
        $this->pos = $pos;
        $this->borderSize = $borderSize;
        $this->borderColor = $borderColor;
        $this->fillColor = $fillColor;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return array
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * @return int
     */
    public function getBorderSize()
    {
        return $this->borderSize;
    }

    /**
     * @return Color
     */
    public function getFillColor()
    {
        return $this->fillColor;
    }

    /**
     * @return Color
     */
    public function getBorderColor()
    {
        return $this->borderColor;
    }


}