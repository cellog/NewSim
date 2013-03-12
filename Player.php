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
    protected $lexer;
    protected $serverparams;
    protected $playerparams;
    protected $playertypes;
    protected $sensebody;
    protected $see;
    protected $commands = array();
    protected $debug = true;
    protected $lexdebug = false;
    protected $cycle = 0;
    function __construct($team, $isgoalie = false, $host = '127.0.0.1', $port = 6000)
    {
        parent::__construct($team, $host, $port);
        $this->isgoalie = (bool) $isgoalie;
        $this->parser = new PlayerParser;
        $this->lexer = new PlayerLexer;
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
        $logger = null;
        if ($this->lexdebug) {
            $logger = new Logger;
        }
        $this->lexer->setup($string, $logger);
        $this->parser->setup($this->lexer);
        $info = $this->parser->parse();
        foreach ($info as $tag) {
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
        if (isset($this->commands[$this->cycle])) {
            foreach ($this->commands[$this->cycle] as $command) {
                if ($this->debug) {
                    echo "sending ",$command,"\n";
                }
                $this->send($command);
            }
            unset($this->commands[$this->cycle]);
        }
        
    }

    function queueCommand($cycle, $command)
    {
        $this->commands[$cycle][] = $command . "\x00";
    }

    function handleSenseBody($sensebody)
    {
        $this->sensebody = $sensebody;
        $this->cycle = $sensebody->getTime();
        if ($this->debug) {
            echo "sense body ", $this->unum, "\n";
        }
    }

    function handleSee($see)
    {
        $this->see = $see;
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