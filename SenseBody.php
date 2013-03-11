<?php
namespace ThroughBall;
class SenseBody
{
    protected $time;
    protected $items = array();
    function addParam(BodyItem $param)
    {
        $this->items[$param->getName()] = $param;
    }

    function setTime($time)
    {
        $this->time = $time;
    }
}
