<?php
namespace ThroughBall\Util;
class FuzzySector
{
    protected
        $vector,
        $minradius,
        $minradiussquared,
        $maxradiussquared,
        $maxradius,
        $startangle,
        $endangle;
    function __construct(Vector $vector, $minradius, $maxradius, $startangle, $endangle)
    {
        $this->vector = $vector;
        $this->minradius = $minradius;
        $this->minradiussquared = $minradius*$minradius;
        $this->maxradius = $maxradius;
        $this->maxradiussquared = $maxradius*$maxradius;
        $this->startangle = Vector::normalizeAngle($startangle);
        $this->endangle = Vector::normalizeAngle($endangle);
    }

    function leftOrEqual($left, $right)
    {
        $arc = $right - $left;
        return $arc >= 0 && $arc < 180 || $arc < -180;
    }

    function angleWithin($left, $angle, $right)
    {
        if ($this->leftOrEqual($left, $right)) {
            return $this->leftOrEqual($left, $angle) && $this->leftOrEqual($angle, $right);
        } else {
            return $this->leftOrEqual($angle, $right) || $this->leftOrEqual($left, $this);
        }
    }

    function has($coords)
    {
        if ($coords instanceof Vector) {
            $coords = array($coords->width(), $coords->height());
        }
        $coords[0] -= $this->vector->width();
        $coords[1] -= $this->vector->height();
        $length = $coords[0]*$coords[0] + $coords[1]*$coords[1];
        $vangle = rad2deg(atan2($coords[1], $coords[0]));
        $a = $this->minradiussquared <= $length;
        $b = $this->maxradiussquared >= $length;
        $c = $this->angleWithin($this->startangle, $vangle, $this->endangle);
        return $a && $b && $c;
    }
}
