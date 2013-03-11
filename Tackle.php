<?php
namespace ThroughBall;
class Tackle extends BodyItem {
    private $currentparam = 0;
    private $paramnames = array(
        'expires', // cycles until the player no longer attempts to tackle
        'count',
    );
    protected $params = array(
        'expires' => false,
        'count' => false,
    );
    function setValue($value)
    {
        $this->params[$this->paramnames[$this->currentparam++]] = $value;
    }

    
}
