<?php
namespace ThroughBall;
include __DIR__ . '/PlayerLexer.php';
include __DIR__ . '/ServerParams.php';
include __DIR__ . '/PlayerParams.php';
include __DIR__ . '/Param.php';
use ThroughBall\PlayerLexer as a;
class ParseException extends \Exception {}
class PlayerParser
{
    protected $options;
    protected $state;
    protected $statestack = array();
    protected $tokenstack = array();
    protected $tokenindex = -1;
    protected $return;
    protected $lex;

    function __construct($options = array())
    {
        $this->options = $options;
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
        if (is_array($this->tokenstack[$index])) {
            return $this->tokenstack[$index]['value'];
        }
        return $this->tokenstack[$index];
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
                a::CLOSEPAREN => 'handleTagClose'
            ),
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
        'player_param' => array(
            'knowntokens' => array(
                a::OPENPAREN => 'handlePlayerParam',
                a::CLOSEPAREN => 'handlePlayerParam'
            ),
            'reduce' => array(
                'simpletag' => 'reduceSimpleTag'
            )
        ),
        'simpletag' => array(
            'knowntokens' => array(
                a::IDENTIFIER => 'handleSimpleTag',
                a::CLOSEPAREN => 'handleSimpleTag'
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

    function reduceSimpleTag()
    {
        $this->stack(-1)->addParam($this->stack());
        $this->tokenindex--;
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
        $this->stack(-1)->setValue($this->value());
        $this->tokenindex--; // pop the param value
    }

    function handleEndParamList()
    {
        $this->popstate();
        $this->handleCloseLayout();
    }

    function handleTemplateClose()
    {
        $this->popstate();
    }
}
$lex = new PlayerLexer('(player_param (allow_mult_default_type 0)(catchable_area_l_stretch_max 1.3)(catchable_area_l_stretch_min 1)(dash_power_rate_delta_max 0)(dash_power_rate_delta_min 0)(effort_max_delta_factor -0.004)(effort_min_delta_factor -0.004)(extra_stamina_delta_max 50)(extra_stamina_delta_min 0)(foul_detect_probability_delta_factor 0)(inertia_moment_delta_factor 25)(kick_power_rate_delta_max 0)(kick_power_rate_delta_min 0)(kick_rand_delta_factor 1)(kickable_margin_delta_max 0.1)(kickable_margin_delta_min -0.1)(new_dash_power_rate_delta_max 0.0008)(new_dash_power_rate_delta_min -0.0012)(new_stamina_inc_max_delta_factor -6000)(player_decay_delta_max 0.1)(player_decay_delta_min -0.1)(player_size_delta_factor -100)(player_speed_max_delta_max 0)(player_speed_max_delta_min 0)(player_types 18)(pt_max 1)(random_seed 1362027203)(stamina_inc_max_delta_factor 0)(subs_max 3))', new Logger);
$lex->debug = true;
$parser = new PlayerParser();
$parser->setup($lex);
$ret = $parser->parse();
var_dump($ret);