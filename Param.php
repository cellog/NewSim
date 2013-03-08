<?php
namespace ThroughBall;
class Param {
    protected
    $name,
    $value;
    function setName($name)
    {
        $this->name = $name;
    }

    function setValue($value)
    {
        $this->value = $value;
    }

    function getName()
    {
        return $this->name;
    }

    function getValue()
    {
        return $this->value;
    }
}
