<?php
namespace Grafika\Gd\DrawingObject;

use Grafika\DrawingObject\CubicBezier as Base;
use Grafika\DrawingObjectInterface;
use Grafika\Gd\Image;
use Grafika\ImageInterface;

/**
 * Class CubicBezier
 * @package Grafika
 */
class CubicBezier extends Base implements DrawingObjectInterface
{

    /**
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
        list($x1, $y1) = $this->control1;
        list($x2, $y2) = $this->control2;
        list($x3, $y3) = $this->point2;

        $this->plot($gd, $x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3);

        $type = $image->getType();
        $file = $image->getImageFile();
        return new Image($gd, $file, $width, $height, $type); // Create new image with updated core
    }

    protected function plot($gd, $x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3)
    { /* plot any cubic Bezier curve */
        $n = 0;
        $i = 0;
        $xc = $x0 + $x1 - $x2 - $x3;
        $xa = $xc - 4 * ($x1 - $x2);
        $xb = $x0 - $x1 - $x2 + $x3;
        $xd = $xb + 4 * ($x1 + $x2);
        $yc = $y0 + $y1 - $y2 - $y3;
        $ya = $yc - 4 * ($y1 - $y2);
        $yb = $y0 - $y1 - $y2 + $y3;
        $yd = $yb + 4 * ($y1 + $y2);
        $fx0 = $x0;
        $fx1 = 0;
        $fx2 = 0;
        $fx3 = 0;
        $fy0 = $y0;
        $fy1 = 0;
        $fy2 = 0;
        $fy3 = 0;
        $t1 = $xb * $xb - $xa * $xc;
        $t2 = 0;
        $t = array();
        /* sub-divide curve at gradient sign changes */
        if ($xa == 0) { /* horizontal */
            if (abs($xc) < 2 * abs($xb)) {
                $t[$n++] = $xc / (2.0 * $xb);
            } /* one change */
        } else {
            if ($t1 > 0.0) { /* two changes */
                $t2 = sqrt($t1);
                $t1 = ($xb - $t2) / $xa;
                if (abs($t1) < 1.0) {
                    $t[$n++] = $t1;
                }
                $t1 = ($xb + $t2) / $xa;
                if (abs($t1) < 1.0) {
                    $t[$n++] = $t1;
                }
            }
        }
        $t1 = $yb * $yb - $ya * $yc;
        if ($ya == 0) { /* vertical */
            if (abs($yc) < 2 * abs($yb)) {
                $t[$n++] = $yc / (2.0 * $yb);
            } /* one change */
        } else {
            if ($t1 > 0.0) { /* two changes */
                $t2 = sqrt($t1);
                $t1 = ($yb - $t2) / $ya;
                if (abs($t1) < 1.0) {
                    $t[$n++] = $t1;
                }
                $t1 = ($yb + $t2) / $ya;
                if (abs($t1) < 1.0) {
                    $t[$n++] = $t1;
                }
            }
        }
        for ($i = 1; $i < $n; $i++) /* bubble sort of 4 points */ {
            if (($t1 = $t[$i - 1]) > $t[$i]) {
                $t[$i - 1] = $t[$i];
                $t[$i] = $t1;
                $i = 0;
            }
        }
        $t1 = -1.0;
        $t[$n] = 1.0; /* begin / end point */
        for ($i = 0; $i <= $n; $i++) { /* plot each segment separately */
            $t2 = $t[$i]; /* sub-divide at $t[$i-1], $t[$i] */
            $fx1 = ($t1 * ($t1 * $xb - 2 * $xc) - $t2 * ($t1 * ($t1 * $xa - 2 * $xb) + $xc) + $xd) / 8 - $fx0;
            $fy1 = ($t1 * ($t1 * $yb - 2 * $yc) - $t2 * ($t1 * ($t1 * $ya - 2 * $yb) + $yc) + $yd) / 8 - $fy0;
            $fx2 = ($t2 * ($t2 * $xb - 2 * $xc) - $t1 * ($t2 * ($t2 * $xa - 2 * $xb) + $xc) + $xd) / 8 - $fx0;
            $fy2 = ($t2 * ($t2 * $yb - 2 * $yc) - $t1 * ($t2 * ($t2 * $ya - 2 * $yb) + $yc) + $yd) / 8 - $fy0;
            $fx0 -= $fx3 = ($t2 * ($t2 * (3 * $xb - $t2 * $xa) - 3 * $xc) + $xd) / 8;
            $fy0 -= $fy3 = ($t2 * ($t2 * (3 * $yb - $t2 * $ya) - 3 * $yc) + $yd) / 8;
            $x3 = floor($fx3 + 0.5);
            $y3 = floor($fy3 + 0.5); /* scale bounds to int */
            if ($fx0 != 0.0) {
                $fx1 *= $fx0 = ($x0 - $x3) / $fx0;
                $fx2 *= $fx0;
            }
            if ($fy0 != 0.0) {
                $fy1 *= $fy0 = ($y0 - $y3) / $fy0;
                $fy2 *= $fy0;
            }
            if ($x0 != $x3 || $y0 != $y3) /* segment $t1 - $t2 */ {
                $this->plotCubicSegment($gd, $x0, $y0, $x0 + $fx1, $y0 + $fy1, $x0 + $fx2, $y0 + $fy2, $x3, $y3);
            }
            $x0 = $x3;
            $y0 = $y3;
            $fx0 = $fx3;
            $fy0 = $fy3;
            $t1 = $t2;
        }
    }

    protected function plotCubicSegment($gd, $x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3)
    { /* plot limited anti-aliased cubic Bezier segment */
        $f = 0;
        $fx = 0;
        $fy = 0;
        $leg = 1;
        $sx = $x0 < $x3 ? 1 : -1;
        $sy = $y0 < $y3 ? 1 : -1; /* step direction */

        $xc = -abs($x0 + $x1 - $x2 - $x3);
        $xa = $xc - 4 * $sx * ($x1 - $x2);
        $xb = $sx * ($x0 - $x1 - $x2 + $x3);
        $yc = -abs($y0 + $y1 - $y2 - $y3);
        $ya = $yc - 4 * $sy * ($y1 - $y2);
        $yb = $sy * ($y0 - $y1 - $y2 + $y3);

        $ab = 0;
        $ac = 0;
        $bc = 0;
        $ba = 0;
        $xx = 0;
        $xy = 0;
        $yy = 0;
        $dx = 0;
        $dy = 0;
        $ex = 0;
        $px = 0;
        $py = 0;
        $ed = 0;
        $ip = 0;
        $EP = 0.01;
        /* check for curve restrains */
        /* slope P0-P1 == P2-P3 and (P0-P3 == P1-P2 or no slope change) */
        assert(($x1 - $x0) * ($x2 - $x3) < $EP && (($x3 - $x0) * ($x1 - $x2) < $EP || $xb * $xb < $xa * $xc + $EP));
        assert(($y1 - $y0) * ($y2 - $y3) < $EP && (($y3 - $y0) * ($y1 - $y2) < $EP || $yb * $yb < $ya * $yc + $EP));
        if ($xa == 0 && $ya == 0) { /* quadratic Bezier */
            $sx = floor((3 * $x1 - $x0 + 1) / 2);
            $sy = floor((3 * $y1 - $y0 + 1) / 2); /* new midpoint */
            $this->plotQuadSegment($gd, $x0, $y0, $sx, $sy, $x3, $y3);
            return;
        }
        $x1 = ($x1 - $x0) * ($x1 - $x0) + ($y1 - $y0) * ($y1 - $y0) + 1; /* line lengths */
        $x2 = ($x2 - $x3) * ($x2 - $x3) + ($y2 - $y3) * ($y2 - $y3) + 1;
        do { /* loop over both ends */
            $ab = $xa * $yb - $xb * $ya;
            $ac = $xa * $yc - $xc * $ya;
            $bc = $xb * $yc - $xc * $yb;
            $ip = 4 * $ab * $bc - $ac * $ac; /* self intersection loop at all? */
            $ex = $ab * ($ab + $ac - 3 * $bc) + $ac * $ac; /* P0 part of self-intersection loop? */
            $f = $ex > 0 ? 1 : sqrt(1 + 1024 / $x1); /* calculate resolution */
            $ab *= $f;
            $ac *= $f;
            $bc *= $f;
            $ex *= $f * $f; /* increase resolution */
            $xy = 9 * ($ab + $ac + $bc) / 8;
            $ba = 8 * ($xa - $ya);/* init differences of 1st degree */
            $dx = 27 * (8 * $ab * ($yb * $yb - $ya * $yc) + $ex * ($ya + 2 * $yb + $yc)) / 64 - $ya * $ya * ($xy - $ya);
            $dy = 27 * (8 * $ab * ($xb * $xb - $xa * $xc) - $ex * ($xa + 2 * $xb + $xc)) / 64 - $xa * $xa * ($xy + $xa);
            /* init differences of 2nd degree */
            $xx = 3 * (3 * $ab * (3 * $yb * $yb - $ya * $ya - 2 * $ya * $yc) - $ya * (3 * $ac * ($ya + $yb) + $ya * $ba)) / 4;
            $yy = 3 * (3 * $ab * (3 * $xb * $xb - $xa * $xa - 2 * $xa * $xc) - $xa * (3 * $ac * ($xa + $xb) + $xa * $ba)) / 4;
            $xy = $xa * $ya * (6 * $ab + 6 * $ac - 3 * $bc + $ba);
            $ac = $ya * $ya;
            $ba = $xa * $xa;
            $xy = 3 * ($xy + 9 * $f * ($ba * $yb * $yc - $xb * $xc * $ac) - 18 * $xb * $yb * $ab) / 8;
            if ($ex < 0) { /* negate values if inside self-intersection loop */
                $dx = -$dx;
                $dy = -$dy;
                $xx = -$xx;
                $yy = -$yy;
                $xy = -$xy;
                $ac = -$ac;
                $ba = -$ba;
            } /* init differences of 3rd degree */
            $ab = 6 * $ya * $ac;
            $ac = -6 * $xa * $ac;
            $bc = 6 * $ya * $ba;
            $ba = -6 * $xa * $ba;
            $dx += $xy;
            $ex = $dx + $dy;
            $dy += $xy; /* error of 1st step */
            for ($fx = $fy = $f; $x0 != $x3 && $y0 != $y3;) {
                $y1 = min($xy - $dx, $dy - $xy);
                $ed = max($xy - $dx, $dy - $xy); /* approximate error distance */
                $ed = $f * ($ed + 2 * $ed * $y1 * $y1 / (4 * $ed * $ed + $y1 * $y1));
                $y1 = 255 * abs($ex - ($f - $fx + 1) * $dx - ($f - $fy + 1) * $dy + $f * $xy) / $ed;
                if ($y1 < 256) {
                    $this->setPixel($gd, $x0, $y0, $y1 / 255);
                } /* plot curve */
                $px = abs($ex - ($f - $fx + 1) * $dx + ($fy - 1) * $dy); /* pixel intensity x move */
                $py = abs($ex + ($fx - 1) * $dx - ($f - $fy + 1) * $dy); /* pixel intensity y move */
                $y2 = $y0;
                do { /* move sub-steps of one pixel */
                    if ($ip >= -$EP) /* intersection possible? -> check.. */ {
                        if ($dx + $xx > $xy || $dy + $yy < $xy) {
                            goto exits;
                        }
                    } /* two x or y steps */
                    $y1 = 2 * $ex + $dx; /* save value for test of y step */
                    if (2 * $ex + $dy > 0) { /* x sub-step */
                        $fx--;
                        $ex += $dx += $xx;
                        $dy += $xy += $ac;
                        $yy += $bc;
                        $xx += $ab;
                    } else {
                        if ($y1 > 0) {
                            goto exits;
                        }
                    } /* tiny nearly cusp */
                    if ($y1 <= 0) { /* y sub-step */
                        $fy--;
                        $ex += $dy += $yy;
                        $dx += $xy += $bc;
                        $xx += $ac;
                        $yy += $ba;
                    }
                } while ($fx > 0 && $fy > 0); /* pixel complete? */
                if (2 * $fy <= $f) { /* x+ anti-aliasing pixel */
                    if ($py < $ed) {
                        $this->setPixel($gd, $x0 + $sx, $y0, $py / $ed);
                    } /* plot curve */
                    $y0 += $sy;
                    $fy += $f; /* y step */
                }
                if (2 * $fx <= $f) { /* y+ anti-aliasing pixel */
                    if ($px < $ed) {
                        $this->setPixel($gd, $x0, $y2 + $sy, $px / $ed);
                    } /* plot curve */
                    $x0 += $sx;
                    $fx += $f; /* x step */
                }
            }
            break; /* finish curve by line */
            exits:
            if (2 * $ex < $dy && 2 * $fy <= $f + 2) { /* round x+ approximation pixel */
                if ($py < $ed) {
                    $this->setPixel($gd, $x0 + $sx, $y0, $py / $ed);
                } /* plot curve */
                $y0 += $sy;
            }
            if (2 * $ex > $dx && 2 * $fx <= $f + 2) { /* round y+ approximation pixel */
                if ($px < $ed) {
                    $this->setPixel($gd, $x0, $y2 + $sy, $px / $ed);
                } /* plot curve */
                $x0 += $sx;
            }
            $xx = $x0;
            $x0 = $x3;
            $x3 = $xx;
            $sx = -$sx;
            $xb = -$xb; /* swap legs */
            $yy = $y0;
            $y0 = $y3;
            $y3 = $yy;
            $sy = -$sy;
            $yb = -$yb;
            $x1 = $x2;
        } while ($leg--); /* try other end */
        $this->plotLine($gd, $x0, $y0, $x3, $y3); /* remaining part in case of cusp or crunode */
    }

    protected function plotQuadSegment($gd, $x0, $y0, $x1, $y1, $x2, $y2)
    { /* draw an limited anti-aliased quadratic Bezier segment */
        $sx = $x2 - $x1;
        $sy = $y2 - $y1;
        $xx = $x0 - $x1;
        $yy = $y0 - $y1;
        $xy = $dx = $dy = $err = $ed = 0;
        $cur = $xx * $sy - $yy * $sx; /* $curvature */
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
    { /* draw a black (0) anti-aliased line on white (255) background */
        $dx = abs($x1 - $x0);
        $sx = $x0 < $x1 ? 1 : -1;
        $dy = -abs($y1 - $y0);
        $sy = $y0 < $y1 ? 1 : -1;
        $err = $dx + $dy;
        $e2 = $x2 = 0; /* $error value e_xy */
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