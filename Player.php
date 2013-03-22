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
    protected $coordinates;
    protected $bodydirection = 0;
    protected $commands = array();
    protected $debug = false;
    protected $lexdebug = false;
    protected $cycle = 0;
    protected $lastcycle = -1;
    protected $knownLocations = array(
        '(f c)' => array(0, 0),
        '(f c b)' => array(0, 34),
        '(f c t)' => array(0, -34),
        '(f g l b)' => array(-52.5, 7),
        '(g l)' => array(-52.5, 0),
        '(f g l t)' => array(-52.5, -7),
        '(f g r b)' => array(52.5, 7),
        '(g r)' => array(52.5, 0),
        '(f g r t)' => array(52.5, -7),
        '(f l b)' => array(-52.5, 34),
        '(f l t)' => array(-52.5, -34),
        '(f r b)' => array(52.5, 34),
        '(f r t)' => array(52.5, -34),
        '(f p l c)' => array(-36, 0),
        '(f p l b)' => array(-36, 20),
        '(f p l t)' => array(-36, -20),
        '(f p r c)' => array(36, 0),
        '(f p r b)' => array(36, 20),
        '(f p r t)' => array(36, -20),

        '(f t l 50)' => array(40, -39),
        '(f t l 40)' => array(40, -39),
        '(f t l 30)' => array(30, -39),
        '(f t l 20)' => array(20, -39),
        '(f t l 10)' => array(10, -39),
        '(f t 0)' => array(0, -39),
        '(f t l 10)' => array(-10, -39),
        '(f t l 20)' => array(-20, -39),
        '(f t l 30)' => array(-30, -39),
        '(f t l 40)' => array(-40, -39),
        '(f t l 50)' => array(-40, -39),

        '(f l t 30)' => array(-57.5, -30),
        '(f l t 20)' => array(-57.5, -20),
        '(f l t 10)' => array(-57.5, -10),
        '(f l 0)' => array(-57.5, 0),
        '(f l b 10)' => array(-57.5, 10),
        '(f l b 20)' => array(-57.5, 20),
        '(f l b 30)' => array(-57.5, 30),

        '(f b l 50)' => array(40, 39),
        '(f b l 40)' => array(40, 39),
        '(f b l 30)' => array(30, 39),
        '(f b l 20)' => array(20, 39),
        '(f b l 10)' => array(10, 39),
        '(f b 0)' => array(0, 39),
        '(f b l 10)' => array(-10, 39),
        '(f b l 20)' => array(-20, 39),
        '(f b l 30)' => array(-30, 39),
        '(f b l 40)' => array(-40, 39),
        '(f b l 50)' => array(-40, 39),


        '(f r t 30)' => array(57.5, -30),
        '(f r t 20)' => array(57.5, -20),
        '(f r t 10)' => array(57.5, -10),
        '(f r 0)' => array(57.5, 0),
        '(f r b 10)' => array(57.5, 10),
        '(f r b 20)' => array(57.5, 20),
        '(f r b 30)' => array(57.5, 30),
    );
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
        $this->coordinates = new Util\Vector(0,0);
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
                new Util\ObjectTable;
            } elseif ($tag instanceof PlayerParams) {
                $this->playerparams = $tag;
            } elseif ($tag instanceof \Exception) {
                throw $tag;
            }
        }
        if ($this->lastcycle != $this->cycle && count($this->commands)) {
            list($command, $callback) = array_shift($this->commands);
            // turn_neck can happen at the same time as another command
            if (-1 == strpos($command, 'turn_neck')) {
                $this->lastcycle = $this->cycle;
            }
            if ($this->debug) {
                echo "sending ",$command,"\n";
            }
            $this->send($command);
            if (is_callable($callback)) {
                call_user_func($callback);
            }
        }
        
    }

    function queueCommand($command, $callback = null)
    {
        $this->commands[] = array($command . "\x00", $callback);
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
        if ($this->debug) {
            echo "see ", $this->unum, "\n";
        }
        $this->see = $see;
        // check for lines
        $closestline = false;
        foreach (array('(l t)', '(l r)', '(l b)', '(l l)') as $line) {
            $seenline = $see->getItem($line);
            if (!$seenline) continue;
            if ($closestline) {
                if ($seenline['distance'] < $closestline['distance']) {
                    $linename = $line;
                    $closestline = $seenline;
                }
            } else {
                $linename = $line;
                $closestline = $seenline;
            }
        }
        if ($closestline) {
            $vector = new Util\LineVector($closestline['distance'], $closestline['direction'], $linename);
            $this->bodydirection = $vector->angle();
            // cache current coordinates and direction
            return; // easy peasy
        }
        // hard way
        $bodydirections = array();
        $landmarks = array();
        $i = 0;
        foreach ($see->listSeenItems() as $param) {
            if (!isset($this->knownLocations[$param])) {
                continue;
            }
            $bodydirections[] = $see->getItem($param);
            $landmarks[] = $this->knownLocations[$param];
            if (++$i == 2) break;
        }
        if ($i != 2) {
            // cache current coordinates and direction
            return; // fail
        }

        $vector1 = new Util\PolarVector($bodydirections[0]['distance'], $bodydirections[0]['direction']);
        $vector2 = new Util\PolarVector($bodydirections[1]['distance'], $bodydirections[1]['direction']);

        $landmark1 = new Util\Vector($landmarks[0][0], $landmarks[0][1]);
        $landmark2 = new Util\Vector($landmarks[1][0], $landmarks[1][1]);
        $separation = Util\Vector::subtract($vector1, $vector2);
        $landmarkseparation = Util\Vector::subtract($landmark1, $landmark2);
        $this->bodydirection = $landmarkseparation->angle() - $separation->angle();
        // cache current coordinates and direction
    }

    function handleHear($hear)
    {
        $this->hear = $hear;
        if ($this->debug) {
            echo "hear ", $this->unum, "\n";
        }
    }

    function isKickable(array $ball)
    {
        if ($ball['distance'] < 0.7) {
            return true;
        }
        return false;
    }

    function toAbsoluteCoordinates($x, $y = null)
    {
        if (is_array($x)) {
            $y = $x[1];
            $x = $x[0];
        }
        return array($x + 57.5, 39 - $y);
    }

    function toRelativeCoordinates($x, $y = null)
    {
        if (is_array($x)) {
            $y = $x[1];
            $x = $x[0];
        }
        return array($x - 57.5, 39 - $y);
    }

    function getCoordinates()
    {
        $params = $this->see->sortedSeenFlags();
        if (!count($params)) {
            if (!$this->coordinates) {
                $this->coordinates = new Util\Vector(0,0);
            }
            return false;
        }
        list($flag, $closest) = each($params);

        $error = 0.5;
        $this->see->generateSeenPoints($closest, $flag, $this->bodydirection, $error);
        if (!$this->see->hasPoints()) {
            return false;
        }
        $this->error = new Util\Vector(0, 0);
        $this->coordinates = new Util\Vector(0, 0);

        $this->see->updatePointsByFlags($params, $this->bodydirection, $error);
        $this->see->averagePoints($this->coordinates, $this->error);
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
        $this->queueCommand('(move ' . $x . ' ' . $y . ')');
        $this->coordinates->assign($x, $y);
    }

    function realDirection($angle)
    {
        return $angle + $this->bodydirection + $this->sensebody->getParam('head_angle');
    }

    function moveTowards($item, $speed = 100)
    {
        if (is_array($item)) {
            $direction = $item['direction'];
        }
        $this->queueCommand('(dash ' . $speed . ' ' . $direction . ')');
    }

    function dash($direction, $speed)
    {
        $this->queueCommand('(dash ' . $speed . ' ' . $direction . ')');
    }

    function turnTowards($direction)
    {
        if (is_array($direction)) {
            $direction = $item['direction'];
        }
        $this->turn($direction);
    }

    function turn($angle)
    {
        $self = $this;
        $this->queueCommand('(turn ' . $angle . ')',
                            function() use ($angle, $self) {$self->updateDirection($angle);});
    }

    function updateDirection($angle)
    {
        return;
        echo "Adding angle " , $angle , " to dir ", $this->bodydirection, "\n";
        $this->bodydirection += $angle;
    }

    function kick($power, $direction)
    {
        $this->queueCommand('(kick ' . $power . ' ' . $direction . ')');
    }

    function getGoalDirection()
    {
        $this->getCoordinates();
        $self = $this->coordinates;
        $goalvector = clone Util\ObjectTable::$landmarks[$this->opponentGoal()];
        $goalvector->minus($self);

        //echo "self ";$self->dump();
        //echo "goal ";Util\ObjectTable::$landmarks[$this->opponentGoal()]->dump();
        //echo "subtracted ";$goalvector->dump();
        //echo "body dir ",$this->bodydirection, "\n";
        return $goalvector;
    }

    function shoot($direction = null)
    {
        if (!$direction) {
            $direction = $this->getGoalDirection()->angle();
        }
        $this->kick(100, $direction);
    }
}

