<?php
namespace ThroughBall;
class Item {
    protected $name;
    private $currentparam = 0;
    private $paramnames = array(
        'distance',
        'direction',
        'distancedelta',
        'directiondelta',
        'bodydirection',
        'headdirection',
        'pointingdirection',
    );
    protected $params = array(
        'distance' => false,
        'direction' => false,
        'distancedelta' => false,
        'directiondelta' => false,
        'bodydirection' => false,
        'headdirection' => false,
        'pointingdirection' => false,
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

    function __get($name)
    {
        return $this->params[$name];
    }
}
