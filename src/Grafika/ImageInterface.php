<?php
namespace Grafika;

/**
 * Interface ImageInterface
 * @package Grafika
 */
interface ImageInterface {

    /**
     * @param $imageFile
     *
     * @return mixed
     */
    public static function createFromFile( $imageFile );

    /**
     * @param int $width
     * @param int $height
     *
     * @return mixed
     */
    public static function createBlank($width = 1, $height = 1);

    /**
     * @return mixed
     */
    public function getCore();

    /**
     * @return mixed
     */
    public function getImageFile();

    /**
     * @return mixed
     */
    public function getWidth();

    /**
     * @return mixed
     */
    public function getHeight();

    /**
     * @return mixed
     */
    public function getType();
}