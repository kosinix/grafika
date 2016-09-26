<?php
namespace Grafika;

/**
 * Interface ImageInterface
 * @package Grafika
 */
interface ImageInterface
{

    /**
     * Output a binary raw dump of an image in a specified format.
     *
     * @param string|ImageType $type Image format of the dump.
     */
    public function blob($type);

    /**
     * @param int $width
     * @param int $height
     *
     * @return mixed
     */
    public static function createBlank($width = 1, $height = 1);

    /**
     * Create Image from core.
     *
     * @param $core
     *
     * @return mixed
     */
    public static function createFromCore($core);

    /**
     * Create Image from image file.
     *
     * @param $imageFile
     *
     * @return mixed
     */
    public static function createFromFile($imageFile);

    /**
     * @return mixed
     */
    public function getCore();

    /**
     * @return mixed
     */
    public function getHeight();

    /**
     * @return mixed
     */
    public function getImageFile();

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @return mixed
     */
    public function getWidth();

    /**
     * Returns animated flag.
     *
     * @return bool True if animated GIF.
     */
    public function isAnimated();

}