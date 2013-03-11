<?php
namespace ThroughBall;
class Stamina extends BodyItem {
    private $currentparam = 0;
    protected $name = 'stamina';
    private $paramnames = array(
        'stamina', // stamina remaining
        'effort', // effort level
        'capacity' // total stamina capacity
    );
    protected $params = array(
        'stamina' => false,
        'effort' => false,
        'capacity' => false,
    );
    function setValue($value)
    {
        $this->params[$this->paramnames[$this->currentparam++]] = $value + 0;
    }
}
