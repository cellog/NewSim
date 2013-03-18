<?php
namespace ThroughBall\Util;
class Vector
{
    protected $x, $y;
    protected $normalized = false;
    function __construct($width = null, $height = null)
    {
        $this->x = $width;
        $this->y = $height;
    }

    function from(Vector $v)
    {
        $this->x = $v->width();
        $this->y = $v->height();
        $this->normalized = $v->isNormalized();
    }

    function toPolar()
    {
        $p = new PolarVector;
        $p->from($this);
        return $p;
    }

    function toVector()
    {
        return $this;
    }

    function dotProduct(Vector $v)
    {
        if (!$v->isNormalized()) {
            $v = clone $v;
            $v->normalize();
        }
        if (!$this->isNormalized()) {
            $x = clone $this;
            $x->normalize();
        } else {
            $x = $this;
        }
        return $x->width()*$v->width() + $x->height()*$v->height();
    }

    function angleBetween(Vector $v)
    {
        return acos(deg2rad($this->dotProduct($v)));
    }

    static function normalizeAngle($angle)
    {
        $angle %= 360;
        if ($angle < -180) {
            $angle += 360;
        } elseif ($angle > 180) {
            $angle -= 180;
        }
        return $angle;
    }

    function normalize()
    {
        $length = $this->length();
        $this->x /= $length;
        $this->y /= $length;
    }

    function isNormalized()
    {
        return $this->normalized;
    }

    function length()
    {
        return sqrt($this->x^2 + $this->y^2);
    }

    function angle()
    {
        return rad2deg(atan2($this->y, $this->x));
    }

    function width()
    {
        return $this->x;
    }

    function height()
    {
        return $this->y;
    }
}
