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
    protected $debug = false;
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
        $this->lexer->setup($string);
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

    function move($x, $y)
    {
        if ($cycle != 0) {
            return;
        }
        $this->queueCommand(0, '(move ' . $x . ' ' . $y . ')');
    }
}