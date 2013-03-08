<?php
namespace ThroughBall;
class PlayerType {
    protected $info = array();
    function addParam($param)
    {
        $this->params[$param->getName()] = $param->getValue();
    }

    function getId()
    {
        return $this->info['id'];
    }
}
