<?php
namespace ThroughBall;
class PlayerTypes {
    protected $types = array();
    function addPlayerType(PlayerType $type)
    {
        $this->params[$type->getId()] = $type;
    }
}
