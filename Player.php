<?php
namespace ThroughBall;
use ThroughBall\Util\UDP;
class Player extends UDP
{
    protected $isgoalie = false;
    protected $unum;
    protected $side;
    protected $playmode;
    protected $parser;
    protected $seeparser;
    protected $bodyparser;
    protected $lexer;
    protected $serverparams;
    protected $playerparams;
    protected $playertypes;
    protected $sensebody;
    protected $see;
    protected $commands = array();
    protected $debug = false;
    protected $lexdebug = false;
    protected $cycle = 0;
    protected $lastcycle = -1;
    function __construct($team, $isgoalie = false, $host = '127.0.0.1', $port = 6000)
    {
        parent::__construct($team, $host, $port);
        $this->isgoalie = (bool) $isgoalie;
        $this->parser = new PlayerParser;
        $this->lexer = new PlayerLexer;
        $this->seeparser = new SeeParser($this->debug);
        $this->bodyparser = new SenseBodyParser($this->debug);
        $this->see = new See;
        $this->sensebody = new SenseBody;
    }

    function getInitString()
    {
        $goalie = $this->isgoalie ? ' (goalie)' : '';
        return "(init " . $this->team . " (version 14.0)$goalie)";
    }

    function parse($string)
    {
        if ($this->debug) {
            echo "parsing \"$string\"\n";
        }
        $string = explode("\x00", $string);
        foreach ($string as $str) {
            if (!$str) continue;
            if (substr($str, 0, 4) == '(see') {
                $tag = $this->seeparser->parse($str, $this->see);
            } elseif (substr($str, 0, 4) == '(sen') {
                $tag = $this->bodyparser->parse($str, $this->sensebody);
            } else {
                $logger = null;
                if ($this->lexdebug) {
                    $logger = new Logger;
                }
                $this->lexer->setup($str, $logger);
                $this->parser->setup($this->lexer);
                $tag = $this->parser->parse();
                $tag = $tag[0];
            }
            if ($tag instanceof SenseBody) {
                $this->handleSenseBody($tag);
            } elseif ($tag instanceof See) {
                $this->handleSee($tag);
            } elseif ($tag instanceof Hear) {
                $this->handleHear($tag);
            } elseif ($tag instanceof Init) {
                $params = $tag->getValues();
                $this->unum = $params['unum'];
                $this->side = $params['side'];
                $this->playmode = $params['playmode'];
            } elseif ($tag instanceof ServerParams) {
                $this->serverparams = $tag;
            } elseif ($tag instanceof PlayerParams) {
                $this->playerparams = $tag;
            } elseif ($tag instanceof \Exception) {
                throw $tag;
            }
        }
        if ($this->lastcycle != $this->cycle && count($this->commands)) {
            $command = array_shift($this->commands);
            // turn_neck can happen at the same time as another command
            if (-1 == strpos($command, 'turn_neck')) {
                $this->lastcycle = $this->cycle;
            }
            if ($this->debug) {
                echo "sending ",$command,"\n";
            }
            $this->send($command);
        }
        
    }

    function queueCommand($cycle, $command)
    {
        $this->commands[] = $command . "\x00";
    }

    function handleSenseBody($sensebody)
    {
        $this->sensebody = $sensebody;
        if ($this->debug) {
            echo "sense body ", $this->unum, "\n";
        }
    }

    function handleSee($see)
    {
        $this->see = $see;
        $this->cycle = $see->getTime();
        if ($this->debug) {
            echo "see ", $this->unum, "\n";
        }
    }

    function handleHear($hear)
    {
        $this->hear = $hear;
        if ($this->debug) {
            echo "hear ", $this->unum, "\n";
        }
    }

    function isKickable(Item $ball)
    {
        if ($ball->distance < 0.7) {
            return true;
        }
        return false;
    }

    function opponentGoal()
    {
        return $this->side == 'l' ? '(g r)' : '(g l)';
    }

    function ownGoal()
    {
        return $this->side == 'r' ? '(g r)' : '(g l)';
    }

    function opponent()
    {
        return $this->side == 'l' ? 'r' : 'l';
    }

    function move($x, $y)
    {
        if ($cycle != 0) {
            return;
        }
        $this->queueCommand(0, '(move ' . $x . ' ' . $y . ')');
    }

    protected $moveCycle = 0;
    function moveTowards($item, $speed = 100)
    {
        if ($item instanceof Item) {
            $direction = $item->direction;
        }
        if ($this->moveCycle < $this->cycle) {
            $this->moveCycle = $this->cycle;
        }
        $this->queueCommand($this->moveCycle++, '(dash ' . $speed . ' ' . $direction . ')');
    }

    function turnTowards($item)
    {
        $this->turn($item->direction);
    }

    protected $turnCycle = 0;
    function turn($angle)
    {
        if ($this->turnCycle < $this->cycle) {
            $this->turnCycle = $this->cycle;
        }
        $this->queueCommand($this->turnCycle++, '(turn ' . $angle . ')');
    }

    protected $kickCycle = 0;
    function kick($power, $direction)
    {
        if ($this->kickCycle < $this->cycle) {
            $this->kickCycle = $this->cycle;
        }
        $this->queueCommand($this->kickCycle++, '(kick ' . $power . ' ' . $direction . ')');
    }
}