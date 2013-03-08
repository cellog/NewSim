<?php
namespace ThroughBall;
class PlayerParams {
    protected $params = array();
    function addParam(Param $param)
    {
        $this->params[$param->getName()] = $param->getValue();
    }
}
