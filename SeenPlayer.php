<?php
namespace ThroughBall;
class SeenPlayer extends Item {
    protected $team;
    protected $unum;
    protected $isgoalie = false;
    protected $iskicking = false;
    protected $istackling = false;

    function setTeam($team)
    {
        $this->team = $team;
    }

    function setUnum($unum)
    {
        $this->unum = $unum;
    }

    function setIsgoalie()
    {
        $this->isgoalie = true;
    }

    function setIsKicking()
    {
        $this->iskicking = true;
    }

    function setIsTackling()
    {
        $this->istackling = true;
    }

    function getName()
    {
        return "player " . $this->unum . " " . $this->team . ($this->isgoalie ? " goalie" : "");
    }
}
