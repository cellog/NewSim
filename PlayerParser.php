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
$lex = new PlayerLexer('(player_type (id 1)(player_speed_max 1.05)(stamina_inc_max 50.5118)(player_decay 0.357182)(inertia_moment 3.92956)(dash_power_rate 0.00508136)(player_size 0.3)(kickable_margin 0.602344)(kick_rand 0.00234432)(extra_stamina 60.5673)(effort_max 0.957731)(effort_min 0.557731)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.1378))
(player_type (id 2)(player_speed_max 1.05)(stamina_inc_max 50.4729)(player_decay 0.385374)(inertia_moment 4.63434)(dash_power_rate 0.00508785)(player_size 0.3)(kickable_margin 0.650137)(kick_rand 0.0501373)(extra_stamina 58.9027)(effort_max 0.964389)(effort_min 0.564389)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.16176))
(player_type (id 3)(player_speed_max 1.05)(stamina_inc_max 47.4927)(player_decay 0.480545)(inertia_moment 7.01362)(dash_power_rate 0.00558456)(player_size 0.3)(kickable_margin 0.737363)(kick_rand 0.137363)(extra_stamina 71.4044)(effort_max 0.914382)(effort_min 0.514382)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.1525))
(player_type (id 4)(player_speed_max 1.05)(stamina_inc_max 40.9819)(player_decay 0.374981)(inertia_moment 4.37454)(dash_power_rate 0.00666968)(player_size 0.3)(kickable_margin 0.736458)(kick_rand 0.136458)(extra_stamina 64.9099)(effort_max 0.94036)(effort_min 0.54036)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.16664))
(player_type (id 5)(player_speed_max 1.05)(stamina_inc_max 41.1314)(player_decay 0.333783)(inertia_moment 3.34457)(dash_power_rate 0.00664477)(player_size 0.3)(kickable_margin 0.699612)(kick_rand 0.0996124)(extra_stamina 87.9217)(effort_max 0.848313)(effort_min 0.448313)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.12177))
(player_type (id 6)(player_speed_max 1.05)(stamina_inc_max 44.756)(player_decay 0.445059)(inertia_moment 6.12647)(dash_power_rate 0.00604067)(player_size 0.3)(kickable_margin 0.710795)(kick_rand 0.110795)(extra_stamina 65.5066)(effort_max 0.937974)(effort_min 0.537974)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.08401))
(player_type (id 7)(player_speed_max 1.05)(stamina_inc_max 47.1423)(player_decay 0.427853)(inertia_moment 5.69632)(dash_power_rate 0.00564294)(player_size 0.3)(kickable_margin 0.659713)(kick_rand 0.0597132)(extra_stamina 67.9843)(effort_max 0.928063)(effort_min 0.528063)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.09086))
(player_type (id 8)(player_speed_max 1.05)(stamina_inc_max 44.331)(player_decay 0.322285)(inertia_moment 3.05713)(dash_power_rate 0.0061115)(player_size 0.3)(kickable_margin 0.723172)(kick_rand 0.123172)(extra_stamina 50.3063)(effort_max 0.998775)(effort_min 0.598775)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.04395))
(player_type (id 9)(player_speed_max 1.05)(stamina_inc_max 45.0234)(player_decay 0.392372)(inertia_moment 4.8093)(dash_power_rate 0.00599611)(player_size 0.3)(kickable_margin 0.651256)(kick_rand 0.0512559)(extra_stamina 55.4959)(effort_max 0.978016)(effort_min 0.578016)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.23937))
(player_type (id 10)(player_speed_max 1.05)(stamina_inc_max 42.4378)(player_decay 0.451899)(inertia_moment 6.29747)(dash_power_rate 0.00642703)(player_size 0.3)(kickable_margin 0.678293)(kick_rand 0.0782927)(extra_stamina 89.3623)(effort_max 0.842551)(effort_min 0.442551)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.15373))
(player_type (id 11)(player_speed_max 1.05)(stamina_inc_max 44.7766)(player_decay 0.340951)(inertia_moment 3.52378)(dash_power_rate 0.00603723)(player_size 0.3)(kickable_margin 0.701646)(kick_rand 0.101646)(extra_stamina 60.3915)(effort_max 0.958434)(effort_min 0.558434)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.19378))
(player_type (id 12)(player_speed_max 1.05)(stamina_inc_max 47.0823)(player_decay 0.418087)(inertia_moment 5.45218)(dash_power_rate 0.00565294)(player_size 0.3)(kickable_margin 0.649797)(kick_rand 0.0497967)(extra_stamina 76.2266)(effort_max 0.895093)(effort_min 0.495093)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.16837))
(player_type (id 13)(player_speed_max 1.05)(stamina_inc_max 47.1396)(player_decay 0.359522)(inertia_moment 3.98806)(dash_power_rate 0.0056434)(player_size 0.3)(kickable_margin 0.601979)(kick_rand 0.00197928)(extra_stamina 66.2142)(effort_max 0.935143)(effort_min 0.535143)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.07105))
(player_type (id 14)(player_speed_max 1.05)(stamina_inc_max 48.5083)(player_decay 0.496808)(inertia_moment 7.4202)(dash_power_rate 0.00541529)(player_size 0.3)(kickable_margin 0.769064)(kick_rand 0.169064)(extra_stamina 93.8204)(effort_max 0.824718)(effort_min 0.424718)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.03525))
(player_type (id 15)(player_speed_max 1.05)(stamina_inc_max 42.9907)(player_decay 0.30048)(inertia_moment 2.512)(dash_power_rate 0.00633489)(player_size 0.3)(kickable_margin 0.698267)(kick_rand 0.0982671)(extra_stamina 61.3236)(effort_max 0.954706)(effort_min 0.554706)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.27537))
(player_type (id 16)(player_speed_max 1.05)(stamina_inc_max 52.0315)(player_decay 0.496966)(inertia_moment 7.42414)(dash_power_rate 0.00482809)(player_size 0.3)(kickable_margin 0.66465)(kick_rand 0.0646502)(extra_stamina 53.8184)(effort_max 0.984726)(effort_min 0.584726)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.06471))
(player_type (id 17)(player_speed_max 1.05)(stamina_inc_max 49.1448)(player_decay 0.43318)(inertia_moment 5.82951)(dash_power_rate 0.00530919)(player_size 0.3)(kickable_margin 0.7625)(kick_rand 0.1625)(extra_stamina 70.1553)(effort_max 0.919379)(effort_min 0.519379)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.23044))', new Logger);
$lex->debug = true;
$parser = new PlayerParser();
$parser->setup($lex);
$ret = $parser->parse();
var_dump($parser->getPlayerTypes());