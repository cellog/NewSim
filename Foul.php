<?php
namespace ThroughBall;
class Foul extends BodyItem {
    protected $name = 'foul';
    private $currentparam = 0;
    private $paramnames = array(
        'number', // total fouls
        'card', // none, yellow or red
    );
    protected $params = array(
        'expires' => false,
        'card' => false,
    );
    function setValue($value)
    {
        $this->params[$this->paramnames[$this->currentparam++]] = $value;
    }

    
}
