<?php
namespace Grafika\DrawingObject;

use Grafika\Color;

/**
 * Base class
 * @package Grafika
 */
abstract class Line
{

    /**
     * X,Y pos 1.
     * @var array
     */
    protected $point1;

    /**
     * X,Y pos 2.
     * @var array
     */
    protected $point2;

    /**
     * @var int Thickness of line.
     */
    protected $thickness;

    /**
     * @var Color
     */
    protected $color;

    /**
     * Creates a line.
     *
     * @param array $point1 Array containing int X and int Y position of the starting point.
     * @param array $point2 Array containing int X and int Y position of the starting point.
     * @param int $thickness Thickness in pixel. Note: This is currently ignored in GD editor and falls back to 1.
     * @param Color|string $color Color of the line. Defaults to black.
     */
    public function __construct(array $point1, array $point2, $thickness = 1, $color = '#000000')
    {
        if (is_string($color)) {
            $color = new Color($color);
        }
        $this->point1 = $point1;
        $this->point2 = $point2;
        $this->thickness = $thickness;
        $this->color = $color;
    }

    /**
     * @return array
     */
    public function getPoint1()
    {
        return $this->point1;
    }

    /**
     * @return array
     */
    public function getPoint2()
    {
        return $this->point2;
    }

    /**
     * @return int
     */
    public function getThickness()
    {
        return $this->thickness;
    }

    /**
     * @return Color
     */
    public function getColor()
    {
        return $this->color;
    }

}