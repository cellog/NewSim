<?php
namespace ThroughBall\Util;
class FuzzySector
{
    protected
        $vector,
        $minradius,
        $maxradius,
        $startangle,
        $endangle;
    function __construct(Vector $vector, $minradius, $maxradius, $startangle, $endangle)
    {
        $this->vector = $vector;
        $this->minradius = $minradius;
        $this->maxradius = $maxradius;
        $this->startangle = $startangle;
        $this->endangle = $endangle;
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

    function has(Vector $coords)
    {
        $v = Vector::subtract($coords, $this->vector);
        $length = $v->length();
        return $this->minradius <= $length && $this->maxradius >= $length && $this->angleWithin($this->startangle,
                                                                                                $v->angle(),
                                                                                                $this->endangle);
    }
}
