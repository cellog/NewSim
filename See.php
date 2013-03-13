<?php
namespace ThroughBall;
class See
{
    protected $time;
    protected $items = array();
    
    function setParams(array $params)
    {
        for ($i = 1; $i < 32; $i++) {
            if (isset($params[$i])) {
                $params[$this->params[$i]] = $params[$i];
            } else {
                $params[$this->params[$i]] = false;
            }
        }
        $this->items = $params;
    }

    function setTime($time)
    {
        $this->time = $time;
    }

    function getTime()
    {
        return $this->time;
    }

    function getParam($name)
    {
        if (isset($this->items[$name])) {
            return $this->items[$name];
        }
        throw new \Exception('Unknown sense_body: ' . $name . ' requested');
    }

    private $stats = array(
        'team' => 0,
        'unum' => 1,
        'isgoalie' => 2,
        'distance' => 3,
        'direction' => 4,
        'distancedelta' => 5,
        'directionelta' => 6,
        'bodydirection' => 7,
        'headdirection' => 8,
        'pointingdirection' => 9,
        'tackling' => 10,
        'kicking' => 10
    );

    function getPlayer($unum, $team, $stat = null)
    {
        if (isset($this->items['(p "' . $team . '" ' . $unum . ')'])) {
            $ret = $this->items['(p "' . $team . '" ' . $unum . ')'];
            $isgoalie = false;
        }
        if (isset($this->items['(p "' . $team . '" ' . $unum . ' goalie)'])) {
            $ret = $this->items['(p "' . $team . '" ' . $unum . ' goalie)'];
            $isgoalie = true;
        }
        if (!isset($ret)) {
            return false;
        }
        if ($stat) {
            if ($stat == 'goalie') {
                return $isgoalie;
            }
            if ($stat == 'tackling') {
                return $ret[$this->stats['tackling']] == 't';
            }
            if ($stat == 'kicking') {
                return $ret[$this->stats['tackling']] == 'k';
            }
            if (!isset($ret[$this->stats[$stat]])) {
                throw new \Exception('Unknown stat ' . $stat . ' requested for player ' . $unum . ' on team ' .
                                     $team);
            }
            return $ret[$this->stats[$stat]];
        }
        foreach ($this->stats as $name => $stat) {
            $ret[$name] = $ret[$stat];
        }
        $ret['tackling'] = $ret['tackling'] == 't';
        $ret['kicking'] = $ret['kicking'] == 'k';
        $ret['isgoalie'] = $isgoalie;
        return $ret;
    }

    function getItem($name)
    {
        if (isset($this->items[$name])) {
            $ret = $this->items[$name];
        } else {
            return false;
        }
        if ($stat) {
            if (!isset($ret[$this->stats[$stat]])) {
                throw new \Exception('Unknown stat ' . $stat . ' requested for player ' . $unum . ' on team ' .
                                     $team);
            }
            return $ret[$this->stats[$stat]];
        }
        foreach ($this->stats as $name => $stat) {
            $ret[$name] = $ret[$stat];
        }
        return $ret;
    }

    function reset()
    {
        $this->items = array();
    }
}
