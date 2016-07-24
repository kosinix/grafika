<?php
namespace Grafika;

/**
 * Holds the color information.
 * @package Grafika
 */
class Color {

    /**
     * @var string Hex string: #FFFFFF
     */
    protected $hexString;

    /**
     * @var float Transparency value 0-1
     */
    protected $alpha;

    /**
     * Color constructor.
     *
     * @param string $hexString Hex string
     * @param float $alpha Transparency value 0-1
     */
    public function __construct( $hexString = '', $alpha = 1.0 ){
        $this->hexString = $hexString; // TODO: Validate hexstring
        $this->alpha = $alpha;
    }

    /**
     * Get RGB array
     *
     * @return array Contains array($r, $g, $b)
     */
    public function getRgb(){
        return $this->hexToRgb( $this->hexString );
    }

    /**
     * Get RGBA array
     *
     * @return array Contains array($r, $g, $b, $a)
     */
    public function getRgba(){
        $rgba = $this->hexToRgb( $this->hexString );
        $rgba[] = $this->alpha;
        return $rgba;
    }

    /**
     * Convert hex string to RGB
     * @param string $hex Hex string. Possible values: #ffffff, #fff, fff
     * @return array Contains (RGB) values red, green and blue
     */
    public function hexToRgb( $hex ) {
        $hex = ltrim($hex, '#'); // Remove #

        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        return array($r, $g, $b); // Returns an array with the rgb values
    }

    /**
     * Get hex string.
     *
     * @return string
     */
    public function getHexString() {
        return $this->hexString;
    }

    /**
     * Set hex string.
     *
     * @param string $hexString
     */
    public function setHexString($hexString) {
        $this->hexString = $hexString;
    }

    /**
     * Alpha value.
     * @return float
     */
    public function getAlpha() {
        return $this->alpha;
    }

    /**
     * @param float $alpha
     */
    public function setAlpha($alpha) {
        $this->alpha = $alpha;
    }


}