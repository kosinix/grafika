<?php
namespace Grafika\DrawingObject;

use Grafika\Color;

/**
 * Base class
 * @package Grafika
 */
abstract class CubicBezier
{

    /**
     * Starting point. Array of X Y values.
     * @var array
     */
    protected $point1;

    /**
     * Control point 1. Array of X Y values.
     * @var array
     */
    protected $control1;

    /**
     * Control point 2. Array of X Y values.
     * @var array
     */
    protected $control2;

    /**
     * End point. Array of X Y values.
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
     * Creates a cubic bezier. Cubic bezier has 2 control points.
     * @param array $point1 Array of X and Y value for start point.
     * @param array $control1 Array of X and Y value for control point 1.
     * @param array $control2 Array of X and Y value for control point 2.
     * @param array $point2 Array of X and Y value for end point.
     * @param Color|string $color Color of the curve. Accepts hex string or a Color object. Defaults to black.
     */
    public function __construct($point1, $control1, $control2, $point2, $color = '#000000')
    {
        if (is_string($color)) {
            $color = new Color($color);
        }
        $this->point1 = $point1;
        $this->control1 = $control1;
        $this->control2 = $control2;
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
    public function getControl1()
    {
        return $this->control1;
    }

    /**
     * @return array
     */
    public function getControl2()
    {
        return $this->control2;
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