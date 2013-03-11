<?php
namespace ThroughBall;
class Arm extends BodyItem {
    protected $name = 'arm';
    private $currentparam = 0;
    private $paramnames = array(
        'movable', // cycles until the arm is allowed to be moved again
        'expires', // cycles until the arm no longer points in that direction
        'targetdistance',
        'targetdirection',
        'count',
    );
    protected $params = array(
        'movable' => false,
        'expires' => false,
        'targetdistance' => false,
        'targetdirection' => false,
        'count' => false,
    );
    function setValue($value)
    {
        $this->params[$this->paramnames[$this->currentparam++]] = $value;
    }

    
}
