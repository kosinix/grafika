<?php

namespace Grafika\Gd\Helper;

/**
 * Class GifByteStream
 * Normalize string operations.
 * Treat string as byte stream where 2 string characters are treated as 1 hex string (byte).
 * Eg. String ffff with length 4 is 0xff 0xff in bytes with length of 2.
 */
final class GifByteStream
{
    /**
     * @var int
     */
    private $position;
    /**
     * @var string
     */
    private $bytes;

    /**
     * GifByteStream constructor.
     *
     * @param string $bytes Accepts only the string created by unpack('H*')
     */
    public function __construct($bytes)
    {
        $this->position = 0;
        $this->bytes    = $bytes;
    }

    /**
     * Take a bite from the byte stream.
     *
     * @param int $size Byte size in integer.
     *
     * @return string
     */
    public function bite($size)
    {
        $str = substr($this->bytes, $this->position * 2, $size * 2);
        $this->position += $size;

        return $str;
    }

    /**
     * @param $byteString
     * @param $offset
     *
     * @return bool|float
     */
    public function find($byteString, $offset)
    {
        $pos = strpos($this->bytes, $byteString, $offset * 2);
        if ($pos !== false) {
            return $pos / 2;
        }

        return false;
    }

    /**
     * @param int $step
     */
    public function back($step = 1)
    {
        $this->position -= $step;
    }

    /**
     * @param int $step
     */
    public function next($step = 1)
    {
        $this->position += $step;
    }

    /**
     * @return float
     */
    public function length()
    {
        return strlen($this->bytes) / 2;
    }

    /**
     * @param $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return mixed
     */
    public function getBytes()
    {
        return $this->bytes;
    }

    /**
     * @return bool
     */
    public function isEnd()
    {
        if ($this->position > $this->length() - 1) {
            return true;
        }

        return false;
    }
}