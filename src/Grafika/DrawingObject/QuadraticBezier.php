<?php
namespace Grafika\DrawingObject;

use Grafika\Color;

/**
 * Base class
 * @package Grafika
 */
abstract class QuadraticBezier
{

    /**
     * Starting point.
     * @var array
     */
    protected $point1;

    /**
     * Control point.
     * @var array
     */
    protected $control;

    /**
     * End point.
     * @var array
     */
    protected $point2;

    /**
     * Color of curve.
     *
     * @var Color
     */
    protected $color;

    /**
     * Creates a quadratic bezier. Quadratic bezier has 1 control point.
     *
     * @param array $point1 Array of X and Y value for start point.
     * @param array $control Array of X and Y value for control point.
     * @param array $point2 Array of X and Y value for end point.
     * @param Color|string $color Color of the curve. Accepts hex string or a Color object. Defaults to black.
     */
    public function __construct($point1, $control, $point2, $color = '#000000')
    {
        if (is_string($color)) {
            $color = new Color($color);
        }
        $this->point1 = $point1;
        $this->control = $control;
        $this->point2 = $point2;
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
    public function getControl()
    {
        return $this->control;
    }

    /**
     * @return array
     */
    public function getPoint2()
    {
        return $this->point2;
    }

    /**
     * @return Color
     */
    public function getColor()
    {
        return $this->color;
    }
    
}