<?php
namespace ThroughBall;
class SenseBody
{
    protected $time;
    protected $items = array();
    function addItem(BodyItem $param)
    {
        $this->items[$param->getName()] = $param;
    }

    function setTime($time)
    {
        $this->time = $time;
    }
}
