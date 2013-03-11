<?php
namespace ThroughBall;
include __DIR__ . '/PlayerLexer.php';
include __DIR__ . '/ServerParams.php';
include __DIR__ . '/PlayerParams.php';
include __DIR__ . '/PlayerTypes.php';
include __DIR__ . '/PlayerType.php';
include __DIR__ . '/Param.php';
include __DIR__ . '/See.php';
include __DIR__ . '/Item.php';
include __DIR__ . '/SeenPlayer.php';
include __DIR__ . '/BodyItem.php';
include __DIR__ . '/SenseBody.php';
include __DIR__ . '/Tackle.php';
include __DIR__ . '/Arm.php';
include __DIR__ . '/Collision.php';
include __DIR__ . '/Focus.php';
include __DIR__ . '/Foul.php';
include __DIR__ . '/ViewMode.php';
include __DIR__ . '/Stamina.php';
include __DIR__ . '/Speed.php';
use ThroughBall\PlayerLexer as a;
class ParseException extends \Exception {}
class PlayerParser
{
    protected $options;
    protected $state;
    protected $playertypes;
    protected $statestack = array();
    protected $tokenstack = array();
    protected $tokenindex = -1;
    protected $return;
    protected $lex;

    function __construct($options = array())
    {
        $this->options = $options;
        $this->playertypes = new PlayerTypes;
    }

    function getPlayerTypes()
    {
        return $this->playertypes;
    }

    function setup(PlayerLexer $lex)
    {
        $this->statestack = array();
        $this->state = "initial";
        $this->lex = $lex;
        $this->lex->N = 0;
        $this->tokenindex = -1;
    }

    /**
     * @return Testing\TemplateParser\Template
     */
    function parse()
    {
        if (!$this->lex) {
            throw new Exception("run setup before parsing");
        }
        if ($this->lex->N) {
            // reset for 2nd parse run
            $this->setup($this->lex);
        }
        while ($this->lex->yylex()) {
            if (isset($this->stateInformation[$this->state]['knowntokens'][$this->lex->token])) {
                $this->tokenindex++;
                $this->tokenstack[$this->tokenindex] = array('token' => $this->lex->token, 'value' => $this->lex->value);
                while ($this->{$this->stateInformation[$this->state]['knowntokens'][$this->lex->token]}());
            } else {
                // unexpected token
                throw new ParseException("In state " . $this->state . ", unexpected token: [" . $this->lex->value . "], expected one of: " .
                                         $this->lex->getHumanReadableNames(array_keys(
                                            $this->stateInformation[$this->state]['knowntokens'])));
            }
        }
        return $this->tokenstack[0];
    }

    function pushstate($state)
    {
        $this->state = $state;
        array_push($this->statestack, $state);
    }

    function popstate()
    {
        $old = array_pop($this->statestack);
        $this->state = $this->statestack[count($this->statestack) - 1];
        if (!$this->state) {
            $this->state = 'initial';
        }
        if (isset($this->stateInformation[$this->state]['reduce']) &&
                isset($this->stateInformation[$this->state]['reduce'][$old])) {
            $this->{$this->stateInformation[$this->state]['reduce'][$old]}();
        }
    }

    function r()
    {
        return $this->tokenstack[0];
    }

    function stack($offset = 0)
    {
        return $this->tokenstack[$this->tokenindex + $offset];
    }

    function value($index = null)
    {
        if ($index === null) {
            $index = $this->tokenindex;
        } else {
            $index += $this->tokenindex;
        }
        return $this->tokenstack[$index]['value'];
    }

    function token($index = null)
    {
        if ($index === null) {
            $index = $this->tokenindex;
        } else {
            $index += $this->tokenindex;
        }
        return $this->tokenstack[$index]['token'];
    }

    function replaceToken($newindex)
    {
        $newindex += $this->tokenindex;
        $this->tokenstack[$this->tokenindex] = $this->tokenstack[$newindex];
    }

    function replace($newvalue)
    {
        $this->tokenstack[$this->tokenindex] = $newvalue;
    }

    protected $stateInformation = array(
        'initial' => array(
            'knowntokens' => array(
                a::OPENPAREN => 'handleTagOpen',
            ),
        ),
        'tag' => array(
            'knowntokens' => array(
                a::SERVERPARAM => 'handleServerParam',
                a::PLAYERPARAM => 'handlePlayerParam',
                a::PLAYERTYPE => 'handlePlayerType',
                a::SENSEBODY => 'handleSenseBody',
                a::SEE => 'handleSee',
                a::CLOSEPAREN => 'handleTagClose'
            ),
            'reduce' => array(
                'player_type' => 'reducePlayerType'
            )
        ),
        'server_param' => array(
            'knowntokens' => array(
                a::OPENPAREN => 'handleServerParam',
                a::CLOSEPAREN => 'handleServerParam'
            ),
            'reduce' => array(
                'simpletag' => 'reduceSimpleTag'
            )
        ),
        'sense_body' => array(
            'knowntokens' => array(
                a::OPENPAREN => 'handleSenseBody',
                a::NUMBER => 'handleSenseBody',
                a::CLOSEPAREN => 'handleSenseBody'
            ),
            'reduce' => array(
                'sensedthing' => 'reduceSimpleTag',
            )
        ),
        'sensedthing' => array(
            'knowntokens' => array(
                a::IDENTIFIER => 'handleSensedThing',
                a::CLOSEPAREN => 'handleSensedThing',
                a::VIEWMODE => 'handleViewMode',
                a::STAMINA => 'handleStamina',
                a::SPEED => 'handleSpeed',
                a::ARM => 'handleArm',
                a::FOCUS => 'handleFocus',
                a::TACKLE => 'handleTackle',
                a::COLLISION => 'handleCollision',
                a::FOUL => 'handleFoul',
                a::NUMBER => 'handleSensedThing',
            )
        ),
        'viewmode' => array(
            'knowntokens' => array(
                a::IDENTIFIER => 'handleViewMode',
                a::CLOSEPAREN => 'handleViewMode',
            )
        ),
        'stamina' => array(
            'knowntokens' => array(
                a::REALNUMBER => 'handleStamina',
                a::NUMBER => 'handleStamina',
                a::CLOSEPAREN => 'handleStamina',
            )
        ),
        'speed' => array(
            'knowntokens' => array(
                a::REALNUMBER => 'handleSpeed',
                a::NUMBER => 'handleSpeed',
                a::CLOSEPAREN => 'handleSpeed',
            )
        ),
        'arm' => array(
            'knowntokens' => array(
                a::OPENPAREN => 'handleArm',
                a::CLOSEPAREN => 'handleArm',
            )
        ),
        'subarm' => array(
            'knowntokens' => array(
                a::IDENTIFIER => 'handleSubArm',
                a::TARGET => 'handleSubArm',
                a::CLOSEPAREN => 'handleSubArm',
                a::NUMBER => 'handleSubArm',
                a::REALNUMBER => 'handleSubArm',
            )
        ),
        'focus' => array(
            'knowntokens' => array(
                a::OPENPAREN => 'handleFocus',
            )
        ),
        'tackle' => array(
            'knowntokens' => array(
                a::OPENPAREN => 'handleTackle',
            )
        ),
        'collision' => array(
            'knowntokens' => array(
                a::OPENPAREN => 'handleCollision',
            )
        ),
        'foul' => array(
            'knowntokens' => array(
                a::OPENPAREN => 'handleFoul',
            )
        ),
        'see' => array(
            'knowntokens' => array(
                a::OPENPAREN => 'handleSee',
                a::NUMBER => 'handleSee',
                a::CLOSEPAREN => 'handleSee'
            ),
            'reduce' => array(
                'seeitem' => 'reduceSeeItem',
            )
        ),
        'seeitem' => array(
            'knowntokens' => array(
                a::BALL => 'handleSeeItem',
                a::UNCLEARBALL => 'handleSeeItem',
                a::UNCLEARFLAG => 'handleSeeItem',
                a::UNCLEARPLAYER => 'handleSeeItem',
                a::UNCLEARGOAL => 'handleSeeItem',
                a::GOALL => 'handleSeeItem',
                a::GOALR => 'handleSeeItem',
                a::CENTERFLAG => 'handleSeeItem',
                a::LEFTTOPFLAG => 'handleSeeItem',
                a::CENTERTOPFLAG => 'handleSeeItem',
                a::RIGHTTOPFLAG => 'handleSeeItem',
                a::LEFTBOTTOMFLAG => 'handleSeeItem',
                a::CENTERBOTTOMFLAG => 'handleSeeItem',
                a::RIGHTBOTTOMFLAG => 'handleSeeItem',
                a::PENALTYLEFTTOP => 'handleSeeItem',
                a::PENALTYLEFTCENTER => 'handleSeeItem',
                a::PENALTYLEFTBOTTOM => 'handleSeeItem',
                a::PENALTYRIGHTTOP => 'handleSeeItem',
                a::PENALTYRIGHTCENTER => 'handleSeeItem',
                a::PENALTYRIGHTBOTTOM => 'handleSeeItem',
                a::GOALLEFTTOP => 'handleSeeItem',
                a::GOALLEFTBOTTOM => 'handleSeeItem',
                a::GOALRIGHTTOP => 'handleSeeItem',
                a::GOALRIGHTBOTTOM => 'handleSeeItem',
                a::LINERIGHT => 'handleSeeItem',
                a::LINETOP => 'handleSeeItem',
                a::LINELEFT => 'handleSeeItem',
                a::LINEBOTTOM => 'handleSeeItem',
                a::FLAGRIGHT => 'handleSeeItem',
                a::FLAGTOP => 'handleSeeItem',
                a::FLAGLEFT => 'handleSeeItem',
                a::FLAGBOTTOM => 'handleSeeItem',
                a::VIRTUALFLAGLT30 => 'handleSeeItem',
                a::VIRTUALFLAGLT20 => 'handleSeeItem',
                a::VIRTUALFLAGLT10 => 'handleSeeItem',
                a::VIRTUALFLAGLB10 => 'handleSeeItem',
                a::VIRTUALFLAGLB20 => 'handleSeeItem',
                a::VIRTUALFLAGLB30 => 'handleSeeItem',
        
                a::VIRTUALFLAGBL50 => 'handleSeeItem',
                a::VIRTUALFLAGBL40 => 'handleSeeItem',
                a::VIRTUALFLAGBL30 => 'handleSeeItem',
                a::VIRTUALFLAGBL20 => 'handleSeeItem',
                a::VIRTUALFLAGBL10 => 'handleSeeItem',
                a::VIRTUALFLAGBR10 => 'handleSeeItem',
                a::VIRTUALFLAGBR20 => 'handleSeeItem',
                a::VIRTUALFLAGBR30 => 'handleSeeItem',
                a::VIRTUALFLAGBR40 => 'handleSeeItem',
                a::VIRTUALFLAGBR50 => 'handleSeeItem',
        
                a::VIRTUALFLAGRT30 => 'handleSeeItem',
                a::VIRTUALFLAGRT20 => 'handleSeeItem',
                a::VIRTUALFLAGRT10 => 'handleSeeItem',
                a::VIRTUALFLAGRB10 => 'handleSeeItem',
                a::VIRTUALFLAGRB20 => 'handleSeeItem',
                a::VIRTUALFLAGRB30 => 'handleSeeItem',
        
                a::VIRTUALFLAGTL50 => 'handleSeeItem',
                a::VIRTUALFLAGTL40 => 'handleSeeItem',
                a::VIRTUALFLAGTL30 => 'handleSeeItem',
                a::VIRTUALFLAGTL20 => 'handleSeeItem',
                a::VIRTUALFLAGTL10 => 'handleSeeItem',
                a::VIRTUALFLAGTR10 => 'handleSeeItem',
                a::VIRTUALFLAGTR20 => 'handleSeeItem',
                a::VIRTUALFLAGTR30 => 'handleSeeItem',
                a::VIRTUALFLAGTR40 => 'handleSeeItem',
                a::VIRTUALFLAGTR50 => 'handleSeeItem',

                a::NUMBER => 'handleSeeItem',
                a::REALNUMBER => 'handleSeeItem',
                a::QUOTEDSTRING => 'handleSeeItem',
                a::CLOSEPAREN => 'handleSeeItem',

                a::OPENPAREN => 'handlePlayer'
            )
        ),
        'player' => array(
            'knowntokens' => array(
                a::PLAYER => 'handlePlayer',
                a::QUOTEDSTRING => 'handlePlayer',
                a::NUMBER => 'handlePlayer',
                a::GOALIE => 'handlePlayer',
                a::CLOSEPAREN => 'handlePlayer'
            )
        ),
        'player_param' => array(
            'knowntokens' => array(
                a::OPENPAREN => 'handlePlayerParam',
                a::CLOSEPAREN => 'handlePlayerParam'
            ),
            'reduce' => array(
                'simpletag' => 'reduceSimpleTag'
            )
        ),
        'player_type' => array(
            'knowntokens' => array(
                a::OPENPAREN => 'handlePlayerType',
                a::CLOSEPAREN => 'handlePlayerType',
            ),
            'reduce' => array(
                'simpletag' => 'reduceSimpleTag'
            )
        ),
        'simpletag' => array(
            'knowntokens' => array(
                a::IDENTIFIER => 'handleSimpleTag',
                a::CLOSEPAREN => 'handleSimpleTag'
            ),
            'reduce' => array(
                'simpleparam' => 'reduceSimpleParam'
            )
        ),
        'simpleparam' => array(
            'knowntokens' => array(
                a::NUMBER => 'handleSimpleParam',
                a::REALNUMBER => 'handleSimpleParam',
                a::QUOTEDSTRING => 'handleSimpleParam',
                a::CLOSEPAREN => 'handleSimpleParam'
            )
        )
    );

    function handleTagOpen()
    {
        $this->pushstate('tag');
        $this->tokenindex--; // throw away the token
    }

    function handleTagClose()
    {
        $this->popstate();
        $this->tokenindex--;
        return;
    }

    function handleServerParam()
    {
        if ($this->token() == a::OPENPAREN) {
            $this->tokenindex--;
            $this->pushstate('simpletag');
            return;
        }
        if ($this->token() == a::CLOSEPAREN) {
            $this->popstate(); // return to previous state
            return;
        }
        $param = new namespace\ServerParams;
        $this->replace($param);
        $this->pushstate('server_param');
    }

    function handlePlayerParam()
    {
        if ($this->token() == a::OPENPAREN) {
            $this->tokenindex--;
            $this->pushstate('simpletag');
            return;
        }
        if ($this->token() == a::CLOSEPAREN) {
            $this->popstate(); // return to previous state
            return;
        }
        $param = new namespace\PlayerParams;
        $this->replace($param);
        $this->pushstate('player_param');
    }

    function handlePlayerType()
    {
        if ($this->token() == a::OPENPAREN) {
            $this->tokenindex--;
            $this->pushstate('simpletag');
            return;
        }
        if ($this->token() == a::CLOSEPAREN) {
            $this->tokenindex--; // discard closing parenthesis
            $this->popstate(); // return to previous state
            return true; // let the tag handler close itself
        }
        $param = new namespace\PlayerType;
        $this->replace($param);
        $this->pushstate('player_type');
    }

    function handleSimpleTag()
    {
        if ($this->token() == a::CLOSEPAREN) {
            $this->tokenindex--; // discard the close parenthesis
            $this->popstate(); // add the simple tag to its parent
            return;
        }
        $param = new namespace\Param;
        $param->setName($this->value());
        $this->replace($param);
        $this->pushstate('simpleparam');
    }

    function handleSimpleParam()
    {
        if ($this->token() == a::CLOSEPAREN) {
            $this->popstate(); // return to simpletag
            return true; // don't retrieve a new token yet
        }
    }

    function handleSee()
    {
        if ($this->token() == a::OPENPAREN) {
            $this->tokenindex--;
            $this->pushstate('seeitem');
            return;
        }
        if ($this->token() == a::CLOSEPAREN) {
            $this->popstate(); // return to previous state
            return true; // handle this in the parent
        }
        if ($this->token() == a::SEE) {
            $this->replace(new namespace\See);
            $this->pushstate('see');
            return;
        }
        // we were passed the simulator time
        $this->stack(-1)->setTime($this->value());
        $this->tokenindex--; // discard the token
    }

    function handleSeeItem()
    {
        if ($this->token() == a::CLOSEPAREN) {
            $this->popstate(); // return to previous state
            return;
        }
        $a = $this->token();
        if ($a == a::QUOTEDSTRING || $a == a::NUMBER || $a == a::REALNUMBER) {
            $this->stack(-1)->setValue($this->value());
            $this->tokenindex--;
            return;
        }
        $value = $this->value();
        $this->replace(new namespace\Item);
        $this->stack()->setName($value);
    }

    function handlePlayer()
    {
        $a = $this->token();
        if ($a == a::CLOSEPAREN) {
            $this->popstate();
            $this->tokenindex--; // discard
            return;
        }
        if ($a == a::QUOTEDSTRING) {
            $this->stack(-1)->setTeam($this->value());
            $this->tokenindex--;
            return;
        }
        if ($a == a::NUMBER) {
            $this->stack(-1)->setUnum($this->value());
            $this->tokenindex--;
            return;
        }
        if ($a == a::GOALIE) {
            $this->stack(-1)->setIsgoalie();
            $this->tokenindex--;
            return;
        }
        if ($a == a::OPENPAREN) {
            $this->tokenindex--; // discard
            $this->pushstate('player');
            return;
        }
        $this->replace(new namespace\SeenPlayer);
    }

    /***********************************************************************************************/
    /* sense_body */
    function handleSenseBody()
    {
        $a = $this->token();
        if ($a == a::OPENPAREN) {
            $this->tokenindex--;
            $this->pushstate('sensedthing');
            return;
        }
        if ($a == a::CLOSEPAREN) {
            $this->popstate();
            return true; // handle this in the parent
        }
        if ($a == a::SENSEBODY) {
            $this->replace(new namespace\SenseBody);
            $this->pushstate('sense_body');
            return;
        }
        // we were passed the simulator time
        $this->stack(-1)->setTime($this->value());
        $this->tokenindex--; // discard the token
    }

    function handleSensedThing()
    {
        if ($this->token() == a::CLOSEPAREN) {
            $this->tokenindex--; // discard the parenthesis
            $this->popstate(); // return to previous state
            return;
        }
        $a = $this->token();
        if ($a == a::QUOTEDSTRING || $a == a::NUMBER || $a == a::REALNUMBER) {
            $this->stack(-1)->setValue($this->value());
            $this->tokenindex--;
            return;
        }
        if ($a == a::IDENTIFIER) {
            $a = $this->value();
            $this->replace(new namespace\BodyItem);
            $this->stack()->setName($a);
            return;
        }
    }

    function senseBodyHelper($tag, $state, $class)
    {
        if ($this->token() == a::CLOSEPAREN) {
            $this->popstate(); // return to previous state
            return true; // handle in previous state
        }
        if ($this->token() == $tag) {
            $this->pushstate($state);
            $this->replace(new $class);
            return;
        }
    }

    function handleViewMode()
    {
        $a = $this->token();
        if ($a == a::CLOSEPAREN || $a == a::VIEWMODE) {
            return $this->senseBodyHelper(a::VIEWMODE, 'viewmode', __NAMESPACE__ . '\\ViewMode');
        }
        // we are parsing the identifiers here
        $this->stack(-1)->setValue($this->value());
        $this->tokenindex--; // discard value
    }

    function handleStamina()
    {
        $a = $this->token();
        if ($a == a::CLOSEPAREN || $a == a::STAMINA) {
            return $this->senseBodyHelper(a::STAMINA, 'stamina', __NAMESPACE__ . '\\Stamina');
        }
        // we are parsing the numbers here
        $this->stack(-1)->setValue($this->value());
        $this->tokenindex--; // discard value
    }

    function handleSpeed()
    {
        $a = $this->token();
        if ($a == a::CLOSEPAREN || $a == a::SPEED) {
            return $this->senseBodyHelper(a::SPEED, 'speed', __NAMESPACE__ . '\\Speed');
        }
        // we are parsing the numbers here
        $this->stack(-1)->setValue($this->value());
        $this->tokenindex--; // discard value
    }

    function handleArm()
    {
        $a = $this->token();
        if ($a == a::CLOSEPAREN || $a == a::ARM) {
            return $this->senseBodyHelper(a::ARM, 'arm', __NAMESPACE__ . '\\Arm');
        }
        if ($a == a::OPENPAREN) {
            $this->pushstate('subarm');
            $this->tokenindex--; // discard
            return;
        }
    }

    function handleSubArm()
    {
        $a = $this->token();
        if ($a == a::CLOSEPAREN) {
            $this->popstate();
            $this->tokenindex--; // discard
            return;
        }
        if ($a != a::NUMBER && $a != a::REALNUMBER) {
            $this->tokenindex--; // discard
            return;
        }
        // we are parsing the numbers here
        $this->stack(-1)->setValue($this->value());
        $this->tokenindex--; // discard value
    }

    function reduceSimpleTag()
    {
        $this->stack(-1)->addParam($this->stack());
        $this->tokenindex--;
    }

    function reduceSimpleParam()
    {
        $this->stack(-2)->setValue($this->value(-1));
        $this->tokenindex--; // pop the param value
        $this->replace($this->stack(1)); // replace the param value with close parenthesis
    }

    function reducePlayerType()
    {
        $this->playertypes->addPlayerType($this->stack());
        $this->replace($this->stack(1)); // replace with closing parenthesis
    }

    function reduceSeeItem()
    {
        $this->stack(-2)->addItem($this->stack(-1));
        $this->tokenindex--; // pop the item
        $this->tokenindex--; // discard the parenthesis
    }
}
$lex = new PlayerLexer('(sense_body 0 (view_mode high normal) (stamina 8000 1 130600) (speed 0 0) (head_angle 0) (kick 0) (dash 0) (turn 0) (say 0) (turn_neck 0) (catch 0) (move 0) (change_view 0) (arm (movable 0) (expires 0) (target 0 0) (count 0)) (focus (target none) (count 0)) (tackle (expires 0) (count 0)) (collision none) (foul  (charged 0) (card none)))', new Logger);
$lex->debug = true;
$parser = new PlayerParser();
$parser->setup($lex);
$ret = $parser->parse();
var_dump($parser->getPlayerTypes());