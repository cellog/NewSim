<?php
namespace ThroughBall;
class Collision extends BodyItem {
    protected $name = 'collision';
    protected $collisions = array();

    function setValue($value)
    {
        if ($value == 'none') return;
        $this->collisions[str_replace(array('(',')'), array('',''), $value)] = 1;
    }
}
