<?php
namespace Grafika\Gd\DrawingObject;

use Grafika\DrawingObject\QuadraticBezier as Base;
use Grafika\DrawingObjectInterface;
use Grafika\Gd\Image;
use Grafika\ImageInterface;

/**
 * Class QuadraticBezier
 * @package Grafika
 */
class QuadraticBezier extends Base implements DrawingObjectInterface
{
    /**
     * @link http://members.chello.at/easyfilter/bresenham.pdf
     * @param ImageInterface $image
     * @return Image
     */
    public function draw($image)
    {
        // Localize vars
        $width = $image->getWidth();
        $height = $image->getHeight();
        $gd = $image->getCore();

        list($x0, $y0) = $this->point1;
        list($x1, $y1) = $this->control;
        list($x2, $y2) = $this->point2;

        $this->plot($gd, $x0, $y0, $x1, $y1, $x2, $y2);

        $type = $image->getType();
        $file = $image->getImageFile();
        return new Image($gd, $file, $width, $height, $type); // Create new image with updated core
    }

    protected function plot($gd, $x0, $y0, $x1, $y1, $x2, $y2)
    {
        /* plot any quadratic Bezier curve */
        $x = $x0 - $x1;
        $y = $y0 - $y1;
        $t = $x0 - 2 * $x1 + $x2; //double

        if ((int)$x * ($x2 - $x1) > 0) { /* horizontal cut at P4? */
            if ((int)$y * ($y2 - $y1) > 0) /* vertical cut at P6 too? */ {
                if (abs(($y0 - 2 * $y1 + $y2) / $t * $x) > abs($y)) { /* which first? */
                    $x0 = $x2;
                    $x2 = $x + $x1;
                    $y0 = $y2;
                    $y2 = $y + $y1; /* swap points */
                }
            } /* now horizontal cut at P4 comes first */
            $t = ($x0 - $x1) / $t;
            $r = (1 - $t) * ((1 - $t) * $y0 + 2.0 * $t * $y1) + $t * $t * $y2; /* By(t=P4) */
            $t = ($x0 * $x2 - $x1 * $x1) * $t / ($x0 - $x1); /* gradient dP4/dx=0 */
            $x = floor($t + 0.5);
            $y = floor($r + 0.5);
            $r = ($y1 - $y0) * ($t - $x0) / ($x1 - $x0) + $y0; /* intersect P3 | P0 P1 */
            $this->plotSegment($gd, $x0, $y0, $x, floor($r + 0.5), $x, $y);
            $r = ($y1 - $y2) * ($t - $x2) / ($x1 - $x2) + $y2; /* intersect P4 | P1 P2 */
            $x0 = $x1 = $x;
            $y0 = $y;
            $y1 = floor($r + 0.5); /* P0 = P4, P1 = P8 */
        }
        if ((int)($y0 - $y1) * ($y2 - $y1) > 0) { /* vertical cut at P6? */
            $t = $y0 - 2 * $y1 + $y2;
            $t = ($y0 - $y1) / $t;
            $r = (1 - $t) * ((1 - $t) * $x0 + 2.0 * $t * $x1) + $t * $t * $x2; /* Bx(t=P6) */
            $t = ($y0 * $y2 - $y1 * $y1) * $t / ($y0 - $y1); /* gradient dP6/dy=0 */
            $x = floor($r + 0.5);
            $y = floor($t + 0.5);
            $r = ($x1 - $x0) * ($t - $y0) / ($y1 - $y0) + $x0; /* intersect P6 | P0 P1 */
            $this->plotSegment($gd, $x0, $y0, floor($r + 0.5), $y, $x, $y);
            $r = ($x1 - $x2) * ($t - $y2) / ($y1 - $y2) + $x2; /* intersect P7 | P1 P2 */
            $x0 = $x;
            $x1 = floor($r + 0.5);
            $y0 = $y1 = $y; /* P0 = P6, P1 = P7 */
        }
        $this->plotSegment($gd, $x0, $y0, $x1, $y1, $x2, $y2); /* remaining part */
    }

    /**
     * Draw an limited anti-aliased quadratic Bezier segment.
     * @param $gd
     * @param $x0
     * @param $y0
     * @param $x1
     * @param $y1
     * @param $x2
     * @param $y2
     */
    protected function plotSegment($gd, $x0, $y0, $x1, $y1, $x2, $y2)
    {
        $sx = $x2 - $x1;
        $sy = $y2 - $y1;
        $xx = $x0 - $x1;
        $yy = $y0 - $y1;

        $cur = $xx * $sy - $yy * $sx; /* $curvature */
        assert($xx * $sx <= 0 && $yy * $sy <= 0);
        if ($sx * (int)$sx + $sy * (int)$sy > $xx * $xx + $yy * $yy) { /* begin with longer part */
            $x2 = $x0;
            $x0 = $sx + $x1;
            $y2 = $y0;
            $y0 = $sy + $y1;
            $cur = -$cur; /* swap P0 P2 */
        }
        if ($cur != 0) { /* no straight line */
            $xx += $sx;
            $xx *= $sx = $x0 < $x2 ? 1 : -1; /* x step direction */
            $yy += $sy;
            $yy *= $sy = $y0 < $y2 ? 1 : -1; /* y step direction */
            $xy = 2 * $xx * $yy;
            $xx *= $xx;
            $yy *= $yy; /* differences 2nd degree */
            if ($cur * $sx * $sy < 0) { /* negat$ed $curvature? */
                $xx = -$xx;
                $yy = -$yy;
                $xy = -$xy;
                $cur = -$cur;
            }
            $dx = 4.0 * $sy * ($x1 - $x0) * $cur + $xx - $xy; /* differences 1st degree */
            $dy = 4.0 * $sx * ($y0 - $y1) * $cur + $yy - $xy;
            $xx += $xx;
            $yy += $yy;
            $err = $dx + $dy + $xy; /* $error 1st step */
            do {
                $cur = min($dx + $xy, -$xy - $dy);
                $ed = max($dx + $xy, -$xy - $dy); /* approximate $error distance */
                $ed += 2 * $ed * $cur * $cur / (4 * $ed * $ed + $cur * $cur);
                $this->setPixel($gd, $x0, $y0, abs($err - $dx - $dy - $xy) / $ed); /* plot $curve */
                if ($x0 == $x2 || $y0 == $y2) {
                    break;
                } /* $curve finish$ed */
                $x1 = $x0;
                $cur = $dx - $err;
                $y1 = 2 * $err + $dy < 0;
                if (2 * $err + $dx > 0) { /* x step */
                    if ($err - $dy < $ed) {
                        $this->setPixel($gd, $x0, $y0 + $sy, abs($err - $dy) / $ed);
                    }
                    $x0 += $sx;
                    $dx -= $xy;
                    $err += $dy += $yy;
                }
                if ($y1) { /* y step */
                    if ($cur < $ed) {
                        $this->setPixel($gd, $x1 + $sx, $y0, abs($cur) / $ed);
                    }
                    $y0 += $sy;
                    $dy -= $xy;
                    $err += $dx += $xx;
                }
            } while ($dy < $dx); /* gradient negates -> close curves */
        }
        $this->plotLine($gd, $x0, $y0, $x2, $y2); /* plot remaining needle to end */
    }

    protected function plotLine($gd, $x0, $y0, $x1, $y1)
    {
        $dx = abs($x1 - $x0);
        $sx = $x0 < $x1 ? 1 : -1;
        $dy = -abs($y1 - $y0);
        $sy = $y0 < $y1 ? 1 : -1;
        $err = $dx + $dy;

        $ed = $dx - $dy == 0 ? 1 : sqrt((float)$dx * $dx + (float)$dy * $dy);
        for (; ;) { /* pixel loop */
            $this->setPixel($gd, $x0, $y0, abs($err - $dx - $dy) / $ed);
            $e2 = $err;
            $x2 = $x0;
            if (2 * $e2 + $dx >= 0) { /* x step */
                if ($x0 == $x1) {
                    break;
                }
                if ($e2 - $dy < $ed) {
                    $this->setPixel($gd, $x0, $y0 + $sy, ($e2 - $dy) / $ed);
                }
                $err += $dy;
                $x0 += $sx;
            }
            if (2 * $e2 + $dy <= 0) { /* y step */
                if ($y0 == $y1) {
                    break;
                }
                if ($dx - $e2 < $ed) {
                    $this->setPixel($gd, $x2 + $sx, $y0, ($dx - $e2) / $ed);
                }
                $err += $dx;
                $y0 += $sy;
            }
        }
    }

    /**
     * @param resource $gd
     * @param int $x
     * @param int $y
     * @param float $ar Alpha ratio
     */
    protected function setPixel($gd, $x, $y, $ar)
    {
        list($r, $g, $b) = $this->color->getRgb();
        $c = imagecolorallocatealpha($gd, $r, $g, $b, 127 * $ar);
        imagesetpixel($gd, $x, $y, $c);
    }
}