<?php
namespace ThroughBall;
class Collision extends BodyItem {
    protected $collisions = array();

    function setValue($value)
    {
        if ($value == 'none') return;
        $this->collisions[str_replace(array('(',')'), array('',''), $value)] = 1;
    }
}
