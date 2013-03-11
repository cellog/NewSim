<?php
namespace ThroughBall;
class ChangePlayerType {
    protected $name;
    private $currentparam = 0;
    private $paramnames = array(
        'unum',
        'type',
    );
    protected $params = array(
        'unum' => false,
        'type' => false,
    );
    function setName($name)
    {
        $this->name = $name;
    }

    function setValue($value)
    {
        $this->params[$this->paramnames[$this->currentparam++]] = $value;
    }

    function getName()
    {
        return $this->name;
    }

    function getValues()
    {
        return $this->params;
    }
}
