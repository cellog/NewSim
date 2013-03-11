<?php
namespace ThroughBall;
class Hear
{
    protected $name = 'hear';
    private $currentparam = 0;
    private $paramnames = array(
        'time',
        'sender',
        'message',
    );
    protected $params = array(
        'time' => false,
        'sender' => false,
        'message' => false
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
