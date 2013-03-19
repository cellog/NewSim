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
        $angle = self::normalizeAngle($angle);
        // angle is always acute, so we have to translate it into its real angle
        if ($angle < 0) {
            switch ($name) {
                case '(l r)' :
                    $angle += 90;
                    break;
                case '(l b)' :
                    break;
                case '(l l)' :
                    $angle = -90 - $angle;
                    break;
                case '(l t)' :
                    $angle += 180;
                    break;
                default:
                    throw new \Exception('Unknown line: ' . $name);
            }
        } else {
            switch ($name) {
                case '(l r)' :
                    $angle = 90 - $angle;
                    break;
                case '(l b)' :
                    $angle = $angle - 180;
                    break;
                case '(l l)' :
                    $angle += 90;
                    break;
                case '(l t)' :
                    break;
                default:
                    throw new \Exception('Unknown line: ' . $name);
            }
        }
        parent::__construct($length, $angle);
    }
}
