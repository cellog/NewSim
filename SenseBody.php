<?php
namespace ThroughBall;
class SenseBody
{
    protected $time;
    private $params = array(
        1 => 'view_quality',
        2 => 'view_width',
        3 => 'stamina',
        4 => 'effort',
        5 => 'capacity',
        6 => 'speed',
        7 => 'direction',
        8 => 'head_angle',
        9 => 'kick',
        10 => 'dash',
        11 => 'turn',
        12 => 'say',
        13 => 'turn_neck',
        14 => 'catch',
        15 => 'move',
        16 => 'change_view',
        17 => 'arm_movable',
        18 => 'arm_expires',
        19 => 'arm_target_distance',
        20 => 'arm_target_direction',
        21 => 'arm_count',
        22 => 'focus_target',
        23 => 'focus_count',
        24 => 'tackle_expires',
        25 => 'tackle_count',
        26 => 'collision',
        27 => 'foulcount',
        28 => 'card'
    );
    protected $items = array();
    function setParams(array $params)
    {
        for ($i = 1; $i < 32; $i++) {
            if (isset($params[$i])) {
                $params[$this->params[$i]] = $params[$i];
            } else {
                $params[$this->params[$i]] = false;
            }
        }
        $this->items = $params;
    }

    function setTime($time)
    {
        $this->time = $time;
    }

    function getTime()
    {
        return $this->time;
    }

    function getParam($name)
    {
        if (isset($this->items[$name])) {
            return $this->items[$name];
        }
        throw new \Exception('Unknown sense_body: ' . $name . ' requested');
    }
}
