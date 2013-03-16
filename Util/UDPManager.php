<?php
namespace ThroughBall\Util;
use ThroughBall\Player;
class UDPManager
{
    protected $team;

    function __construct($team)
    {
        $this->team = $team;
    }
    static protected $objects = array();

    function addObject(UDP $connection)
    {
        $connection->init();
        self::$objects[spl_object_hash($connection)] = $connection;
    }

    function removeObject(UDP $connection)
    {
        unset(self::$objects[spl_object_hash($connection)]);
    }

    function addGoalie($host = '127.0.0.1', $port = 6000)
    {
        $this->addObject($a = new Player($this->team, true, $host, $port));
        return $a;
    }

    function addPlayer($class = 'ThroughBall\\Player', $host = '127.0.0.1', $port = 6000)
    {
        $this->addObject($a = new $class($this->team, false, $host, $port));
        return $a;
    }

    function run()
    {
        while (count(self::$objects)) {
            foreach (self::$objects as $udp) {
                if (!count(self::$objects)) break;
                $udp->parse($udp->receive());
            }
        }
    }

    function stop()
    {
        self::$objects = array();
    }
}