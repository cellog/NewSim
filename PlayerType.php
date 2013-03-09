<?php
namespace ThroughBall;
class PlayerType {
    protected $info = array();
    function addParam($param)
    {
        $this->info[$param->getName()] = $param->getValue();
    }

    function getId()
    {
        return $this->info['id'];
    }
}
