<?php
namespace ThroughBall;
class BodyItem {
    protected $name;
    protected $value;
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

    function getValues()
    {
        return $this->value;
    }
}
