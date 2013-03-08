<?php
namespace ThroughBall;
class ServerParams {
    protected $params = array();
    function addParam(Param $param)
    {
        $this->params[$param->getName()] = $param->getValue();
    }
}
