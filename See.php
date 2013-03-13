<?php
namespace ThroughBall;
class See
{
    protected $time;
    protected $items = array();
    function addItem(Item $param)
    {
        $this->items[$param->getName()] = $param;
    }

    function setTime($time)
    {
        $this->time = $time;
    }

    function getTime()
    {
        return $this->time;
    }

    function getItem($name)
    {
        return $this->items[$name];
    }

    function reset()
    {
        $this->items = array();
    }
}
