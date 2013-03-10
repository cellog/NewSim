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
        'headdirection'
    );
    protected $params = array(
        'distance' => false,
        'direction' => false,
        'distancedelta' => false,
        'directiondelta' => false,
        'bodydirection' => false,
        'headdirection' => false
    );
    function setName($name)
    {
        $this->name = $name;
    }

    function setValue($value)
    {
        $this->params[$this->paramnames[$this->currentparam++]] = $value + 0;
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
