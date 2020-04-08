<?php


namespace Grafika\Util;


class TTFBox
{
    // As per https://www.php.net/manual/en/function.imagettfbbox.php
    // $keys = ['llx', 'lly', 'lrx', 'lry', 'urx', 'ury', 'ulx', 'uly',];
    const INDEX_LLX = 0;
    const INDEX_LLY = 1;

    const INDEX_LRX = 2;
    const INDEX_LRY = 3;

    const INDEX_URX = 4;
    const INDEX_URY = 5;

    const INDEX_ULX = 6;
    const INDEX_ULY = 7;

    private $ttfBox;

    public function __construct(array $ttfBox)
    {
        $this->ttfBox = $ttfBox;
    }

    public function rotate(int $cx, int $cy, int $angleDegree): self
    {
        $angleRad = deg2rad($angleDegree);
        $cos = cos($angleRad);
        $sin = sin($angleRad);
        $rotated = [];
        for ($i = 0; $i < 8; $i += 2) {
            list($x, $y) = $this->rotatePoint($cos, $sin, $cx, $cy, $this->ttfBox[$i], $this->ttfBox[$i + 1]);
            $rotated[$i] = $x;
            $rotated[$i + 1] = $y;
        }
        return new self($rotated);
    }

    public function reduceHeight(TTFBox $ttfBox): self
    {
        $combinedBox = $this->ttfBox;
        $combinedBox[self::INDEX_LLY] =  min($this->ttfBox[self::INDEX_LLY], $ttfBox->ttfBox[self::INDEX_LLY]);
        $combinedBox[self::INDEX_LRY] =  min($this->ttfBox[self::INDEX_LRY], $ttfBox->ttfBox[self::INDEX_LRY]);
        $combinedBox[self::INDEX_URY] =  max($this->ttfBox[self::INDEX_URY], $ttfBox->ttfBox[self::INDEX_URY]);
        $combinedBox[self::INDEX_ULY] =  max($this->ttfBox[self::INDEX_ULY], $ttfBox->ttfBox[self::INDEX_ULY]);
        return new self($combinedBox);
    }

    public function combineHeight(TTFBox $ttfBox): self
    {
        $combinedBox = $this->ttfBox;
        $combinedBox[self::INDEX_LLY] =  max($this->ttfBox[self::INDEX_LLY], $ttfBox->ttfBox[self::INDEX_LLY]);
        $combinedBox[self::INDEX_LRY] =  max($this->ttfBox[self::INDEX_LRY], $ttfBox->ttfBox[self::INDEX_LRY]);
        $combinedBox[self::INDEX_URY] =  min($this->ttfBox[self::INDEX_URY], $ttfBox->ttfBox[self::INDEX_URY]);
        $combinedBox[self::INDEX_ULY] =  min($this->ttfBox[self::INDEX_ULY], $ttfBox->ttfBox[self::INDEX_ULY]);
        return new self($combinedBox);
    }

    public function getYPoints(): array
    {
        return [
            $this->ttfBox[self::INDEX_LLY],
            $this->ttfBox[self::INDEX_LRY],
            $this->ttfBox[self::INDEX_URY],
            $this->ttfBox[self::INDEX_ULY],
        ];
    }

    public function getXPoints(): array
    {
        return [
            $this->ttfBox[self::INDEX_LLX],
            $this->ttfBox[self::INDEX_LRX],
            $this->ttfBox[self::INDEX_URX],
            $this->ttfBox[self::INDEX_ULX],
        ];
    }

    public function getCoordinates(): string
    {
        return sprintf('(%d, %d), (%d, %d), (%d, %d), (%d, %d)', ... $this->ttfBox);
    }

    public function getLowerLeftX(): int
    {
        return $this->ttfBox[self::INDEX_LLX];
    }

    public function getLowerLeftY(): int
    {
        return $this->ttfBox[self::INDEX_LLY];
    }

    public function getLowerRightX(): int
    {
        return $this->ttfBox[self::INDEX_LRX];
    }

    public function getLowerRightY(): int
    {
        return $this->ttfBox[self::INDEX_LRY];
    }

    public function getUpperLeftX(): int
    {
        return $this->ttfBox[self::INDEX_ULX];
    }

    public function getUpperLeftY(): int
    {
        return $this->ttfBox[self::INDEX_ULY];
    }

    public function getUpperRightX(): int
    {
        return $this->ttfBox[self::INDEX_URX];
    }

    public function getUpperRightY(): int
    {
        return $this->ttfBox[self::INDEX_URY];
    }

    private function rotatePoint(float $cos, float $sin, int $cx, int $cy, int $x, int $y): array
    {
        // Thanks: https://stackoverflow.com/a/32376643
        // cos(angle) * (p.x - cx) - sin(angle) * (p.y - cy) + cx,
        $nx = ($cos * ($x - $cx)) - ($sin * ($y - $cy)) + $cx;
        // sin(angle) * (p.x - cx) + cos(angle) * (p.y - cy) + cy);
        $ny = ($sin * ($x - $cx)) + ($cos * ($y - $cy)) + $cy;

        return [(int) $nx, (int) $ny];
    }

}
