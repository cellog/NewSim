<?php
namespace ThroughBall\Util;
class MonitorParser
{
    protected $continuousmonitor;
    protected $text;
    protected $ret = array();
    protected $serverparam;
    protected $playerparam;
    function __construct($continuousmonitor = null)
    {
        $this->continuousmonitor = $continuousmonitor;
    }

    function setup($text)
    {
        $this->text = explode("\n", $text);
        $this->ret = array();
    }

    function parse($continuousmonitor = null)
    {
        if ($continuousmonitor) {
            $this->continuousmonitor = $continuousmonitor;
        }
        foreach ($this->text as $item) {
            if (!$item) {
                continue;
            }
            if ($item[1] == 'm') {
                // message
            } elseif ($item[1] == 's') {
                // show or server_param
                if ($item[2] == 'h') {
                    $states = array('disabled', 'stand', 0x2 => 'kick', 0x4 => 'kick_fault',
                                    0x8 => 'goalie', 0x10 => 'catch', 0x20 => 'catch_fault');
                    // show
                    $result = array();
                    if (!preg_match('/\(\(b\) (?<ballx>\-?\d+(?:\.\d+)?) (?<bally>\-?\d+(?:\.\d+)?) '
                                        . '(?<ballvelocityx>\-?\d+(?:\.\d+)?) (?<ballvelocityy>\-?\d+(?:\.\d+)?)/',
                                        $item, $matches)) {
                        continue;
                    }
                    $result['ball'] = array(
                        'location' => array('x' => (float) $matches['ballx'], 'y' => (float) $matches['bally']),
                        'velocityvector' => array('x' => (float) $matches['ballvelocityx'], 'y' => (float) $matches['ballvelocityy'])
                    );
                    if (!preg_match_all('/\(\((?<team>l|r) '
                                   . '(?<unum>\d+)\) '
                                   . '(?<type>\d+) '
                                   . '(?<state>(?:0x)?[0-9A-Fa-f]+) '
                                   . '(?<x>\-?\d+(?:\.\d+)?) '
                                   . '(?<y>\-?\d+(?:\.\d+)?) '
                                   . '(?<velocityx>\-?\d+(?:\.\d+)?) '
                                   . '(?<velocityy>\-?\d+(?:\.\d+)?) '
                                   . '(?<angle>\-?\d+(?:\.\d+)?) '
                                   . '(?<neckangle>\-?\d+(?:\.\d+)?) '
                                   . '(?:(?<armx>\-?\d+(?:\.\d+)?) '
                                   . '(?<army>\-?\d+(?:\.\d+)?) )?'
                                   . '\(v (?<viewmode>h|l) '
                                   . '(?<viewangle>\-?\d+(?:\.\d+)?)\) '
                                   . '\(s (?<stamina>\d+) (?<effort>\d+) (?<recovery>\d+) (?<capacity>\d+)\) '
                                   . '(?:\(f (?<focusside>l|r) (?<focusunum>\d+)\) )?'
                                   . '\(c '
                                   . '(?<kicks>\d+) '
                                   . '(?<dashes>\d+) '
                                   . '(?<turns>\d+) '
                                   . '(?<catches>\d+) '
                                   . '(?<moves>\d+) '
                                   . '(?<neckturns>\d+) '
                                   . '(?<viewchanges>\d+) '
                                   . '(?<saycount>\d+) '
                                   . '(?<tackles>\d+) '
                                   . '(?<armcount>\d+) '
                                   . '(?<focuscount>\d+)'
                                   . '\)'
                                   . '\)/', $item, $matches)) {
                        var_dump($item);exit;
                    }
                    foreach ($matches[0] as $i => $unused) {
                        $player = array();
                        foreach (array('team', 'unum', 'type', 'state', 'x', 'y', 'velocityx', 'velocityy',
                                       'angle', 'neckangle', 'armx', 'army', 'viewmode', 'viewangle', 'stamina',
                                       'effort', 'recovery', 'capacity', 'focusside', 'focusunum', 'kicks',
                                       'dashes', 'turns', 'catches', 'moves', 'neckturns', 'viewchanges',
                                       'saycount', 'tackles', 'armcount', 'focuscount') as $name) {
                            $player[$name] = $matches[$name][$i];
                            if ($name == 'team' || $name == 'type' || $name == 'state' || $name == 'viewmode'
                                || $name == 'focusside') {
                                continue;
                            } elseif ($name == 'x' || $name == 'y' || $name == 'velocityx' || $name == 'velocityy'
                                      || $name == 'angle' || $name == 'neckangle' || $name == 'armx' || $name == 'army'
                                      || $name == 'viewangle') {
                                if (!strlen($player[$name])) {
                                    $player[$name] = false;
                                } else {
                                    $player[$name] = (float) $player[$name];
                                }
                            } else {
                                if (!strlen($player[$name])) {
                                    $player[$name] = false;
                                } else {
                                    $player[$name] = (int) $player[$name];
                                }
                            }
                        }
                        $player['state'] = hexdec($player['state']);
                        $player['state'] = $states[$player['state']];
                        $result['players'][$player['team']][] = $player;
                    }
                    $this->ret[] = $result;
                    if ($this->continuousmonitor) {
                        $this->continuousmonitor->display($result);
                    }
                } else {
                    // server_param
                }
            } elseif ($item[1] == 't') {
                // team
            } elseif ($item[1] == 'p') {
                // playerparam or playertype or playertypes or playmode
                if ($item[5] == 'm') {
                    // playmode
                } elseif ($item[7] == 'p') {
                    // playerparam
                } elseif ($item[11] == 's') {
                    // playertypes
                } else {
                    // playertype
                }
            }
        }
        return $this->ret;
    }
}
