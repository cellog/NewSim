<?php
namespace ThroughBall\Util;
/**
 * a vector described by the line from a player's face to one of the sidelines
 *
 * This is used to handle (l [rtbl]) in (see)
 */
class LineVector extends PolarVector
{
    function __construct($length, $angle, $name)
    {
        // angle is always acute, so we have to translate it into its real angle
        switch ($name) {
            case '(l r)' :
                $angle = -$angle;
                break;
            case '(l b)' :
                $angle = 90 - $angle;
                break;
            case '(l l)' :
                $angle = 180 - $angle;
                break;
            case '(l t)' :
                $angle = -(90 + $angle);
                break;
            default:
                throw new \Exception('Unknown line: ' . $name);
        }
        parent::__construct($length, self::normalizeAngle($angle));
    }
}
