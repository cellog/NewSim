<?php
namespace ThroughBall;
include __DIR__ . '/PlayerLexer.php';
include __DIR__ . '/ServerParams.php';
include __DIR__ . '/PlayerParams.php';
include __DIR__ . '/PlayerTypes.php';
include __DIR__ . '/PlayerType.php';
include __DIR__ . '/Param.php';
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
                throw new ParseException("Unexpected token: [" . $this->lex->value . "], expected one of: " .
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
                a::CLOSEPAREN => 'handleSenseBody'
            ),
            'reduce' => array(
                'simpletag' => 'reduceSimpleTag',
                '3tag' => 'reduce3Tag',
                'complextag' => 'reduceComplexTag'
            )
        ),
        'see' => array(
            'knowntokens' => array(
                a::OPENPAREN => 'handleSee',
                a::CLOSEPAREN => 'handleSee'
            ),
            'reduce' => array(
                'seeitem' => 'reduceSeenItem',
            )
        ),
        'seeitem' => array(
            'knowntokens' => array(
                a::UNCLEARBALL => 'handleSeeItem',
                a::UNCLEARFLAG => 'handleSeeItem',
                a::UNCLEARPLAYER => 'handleSeeItem',
                a::UNCLEARGOAL => 'handleSeeItem',
                a::GOALL => 'handleSeeItem',
                a::GOALR => 'handleSeeItem',
                a::CENTERFLAG => 'handleSeeItem',
                a::CLOSEPAREN => 'handleSeeItem'
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
}
$lex = new PlayerLexer('(sense_body 0 (view_mode high normal) (stamina 8000 1 130600) (speed 0 0) (head_angle 0) (kick 0) (dash 0) (turn 0) (say 0) (turn_neck 0) (catch 0) (move 0) (change_view 0) (arm (movable 0) (expires 0) (target 0 0) (count 0)) (focus (target none) (count 0)) (tackle (expires 0) (count 0)) (collision none) (foul  (charged 0) (card none)))
(see 0 ((f r t) 55.7 3) ((f g r b) 70.8 38) ((g r) 66.7 34) ((f g r t) 62.8 28) ((f p r c) 53.5 43) ((f p r t) 42.5 23) ((f t 0) 3.6 -34 0 0) ((f t r 10) 13.2 -9 0 0) ((f t r 20) 23.1 -5) ((f t r 30) 33.1 -3 0 0) ((f t r 40) 42.9 -3) ((f t r 50) 53 -2) ((f r 0) 70.8 31) ((f r t 10) 66 24) ((f r t 20) 62.8 16) ((f r t 30) 60.9 7) ((f r b 10) 76.7 38) ((f r b 20) 83.1 43) ((P) 3 180) ((p "opponent" 1 goalie) 6 0 0 0 0 0) ((p "opponent" 2) 9 0 0 0 0 0))
', new Logger);
$lex->debug = true;
$parser = new PlayerParser();
$parser->setup($lex);
$ret = $parser->parse();
var_dump($parser->getPlayerTypes());