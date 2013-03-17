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
        if (!$sensebody->getParam('speed')) {
            $this->bodydirection = $sensebody->getParam('direction');
        }
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
        // cache current coordinates
        $this->coordinates = $this->getCoordinates();
        return;
        if (!$this->sensebody->getParam('speed')) {
            // body direction is direction parameter and is set in sensebody handler
            //return;
        }
        // use coords to find body direction here
        $bodydirections = array();
        foreach ($this->see->listSeenItems() as $param) {
            if (!isset($this->knownLocations[$param])) {
                continue;
            }
            $coords = $this->toAbsoluteCoordinates($this->knownLocations[$param]);
            $angle = -rad2deg(atan2($coords[1] - $this->coordinates[1], $coords[0] - $this->coordinates[0]));
            $bodydirections[] = -($info['direction'] - $angle);
        }
        $this->bodydirection = array_sum($bodydirections)/count($bodydirections);
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
        $params = $this->see->listSeenItems();
        if (!count($params)) {
            return false;
        }
        // check for flags
        $found = array();
        $far = array();
        $near = array();
        foreach ($params as $param) {
            if (isset($this->knownLocations[$param])) {
                $seen = $this->see->getItem($param);
                $found[$param] = array($seen,
                                       $this->toAbsoluteCoordinates($this->knownLocations[$param]));
                if ($seen['distance'] < 11) {
                    // this is 100% accurate, basically
                    $far = array();
                    $near = array($param => $found[$param]);
                    break;
                }
                if ($seen['distance'] < 20) {
                    // these will be more accurate
                    $near[$param] = $found[$param];
                    if (count($near) >= 2) {
                        // this is enough for certainty
                        $far = array();
                        break;
                    }
                } else {
                    $far[$param] = $found[$param];
                }
            }
        }
        $calcx = array();
        $calcy = array();
        $results = array();
        foreach (array('near' => $near, 'far' => $far) as $name => $found) {
            if (!count($found)) {
                continue;
            }
            foreach ($found as $param => $info) {
                // solve the right triangle to determine cartesian distance to the object
                // first calculate the other angle
                $A = $info[0]['direction'];
                if (!$A) {
                    // we are level with this landmark
                    $calcx[] = $info[1][0] - $info[0]['distance'];
                    $calcy[] = $info[1][1];
                    continue;
                }
                if ($A < 0) {
                    $A = 0 - $A; // make it positive
                    $negateY = true;
                    if ($A < 90) {
                        $negateX = true;
                    } else {
                        $negateX = false;
                    }
                } else {
                    $negateY = false;
                    if ($A < 90) {
                        $negateX = true;
                    } else {
                        $negateX = false;
                    }
                }
                $B = 90;
                $C = 90 - $A; // angles must add up to 180 in a triangle
    
                // next use the law of sines a/sin A = b/sin B = c/sin C
                // sin of 90 is 1 so b/sin B = distance/1 = distance (convenient!)
                // so a/sin A = b, a = bsinA = distance*sin A
                $b = $info[0]['distance'];
                $a = $info[0]['distance']*sin(deg2rad($A));
                $c = $info[0]['distance']*sin(deg2rad($C));
                if ($negateY) {
                    $a = -$a;
                }
                if ($negateX) {
                    $b = -$b;
                }
                $calcy[] = $info[1][1] + $a;
                $calcx[] = $info[1][0] + $b;
            }
            // now to help eliminate the random error, average everything
            if (count($calcx)) {
                $x = array_sum($calcx)/count($calcx);
            }
            if (count($calcy)) {
                $y = array_sum($calcy)/count($calcy);
            }
            if (count($calcx)) {
                $results[$name] = array($x, $y);
            }
        }
        if (isset($results['near']) && !isset($results['far'])) {
            return $results['near'];
        }
        if (!isset($results['near'])) {
            return $results['far'];
        }
        // bias towards near results
        return array(.7 * $results['near'][0] + .3 * $results['far'][0],
                     .7 * $results['near'][1] + .3 * $results['far'][1]);
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
    }

    function realDirection($angle)
    {
        return $angle + $this->sensebody->getParam('direction') + $this->sensebody->getParam('head_angle');
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
        if ($this->bodydirection + $angle > 180) {
            $this->bodydirection = 360 - $this->bodydirection;
        } elseif ($this->bodydirection + $angle < -180) {
            $this->bodydirection = 360 + $this->bodydirection;
        }
        $this->bodydirection += $angle;
    }

    function kick($power, $direction)
    {
        $this->queueCommand('(kick ' . $power . ' ' . $direction . ')');
    }

    function getGoalDirection()
    {
        $coords = $this->getCoordinates();
        $goal = $this->toAbsoluteCoordinates($this->knownLocations[$this->opponentGoal()]);
        // get relative coords to get sides a and b of the right triangle
        $b = $goal[0] - $coords[0];
        $a = $goal[1] - $coords[1];
        // if b is positive, we are beneath the goal
        if ($a < 0) {
            $beneath = true;
            $hypa = -$a;
        } else {
            $hypa = $a;
            $beneath = false;
        }
        // use a^2+b^2=c^2 for right triangle to get distance
        $c = sqrt($hypa*$hypa + $b*$b);
        // simple formula: use atan(opposite/adjacent) to get the angle
        $dir = $this->sensebody->getParam('head_angle') -
             $this->bodydirection;
        $dir = 0;
        $B = -(rad2deg(atan2($a, $b)) - $dir);
        return array('direction' => $B, 'distance' => $c);
        $goal = $this->see->getItem($this->opponentGoal());
        if ($goal) {
            return $goal['direction'];
        }
    }

    function shoot($direction = null)
    {
        if (!$direction) {
            $direction = $this->getGoalDirection();
            $direction = $direction['direction'];
        }
        $this->kick(100, $direction);
    }
}

