<?php
namespace ThroughBall;
class Init {
    protected $name;
    private $currentparam = 0;
    private $paramnames = array(
        'side',
        'unum',
        'playmode',
    );
    protected $params = array(
        'side' => false,
        'unum' => false,
        'playmode' => false,
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
