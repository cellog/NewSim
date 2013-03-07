<?php
namespace ThroughBall;
include __DIR__ . '/PlayerLexer.php';
use ThroughBall\PlayerLexer as a;
class ParseException extends \Exception {}
class PlayerParser
{
    protected $options;
    protected $state;
    protected $statestack = array();
    protected $tokenstack = array();
    protected $tokenindex = 0;
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
        $return = new TemplateParser\Template;
        $this->lex = $lex;
        $this->lex->N = 0;
        $this->tokenstack[0] = $return;
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
                $this->{$this->stateInformation[$this->state]['knowntokens'][$this->lex->token]}();
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
            ),
        ),
        'server_param' => array(
            'knowntokens' => array(
                a::OPENPAREN => 'handleSimpleTag',
            )
        ),
        'simpletag' => array(
            'knowntokens' => array(
                a::IDENTIFIER => 'handleIdentifier',
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

    function handleServerParam()
    {
        if ($this->token() == a::OPENPAREN) {
            $this->tokenindex--;
            $this->pushstate('simpletag');
            return;
        }
        $param = new namespace\ServerParams;
        $this->replace($param);
        $this->tokenindex--; // throw away the token
    }

    function handleSimpleTag()
    {
        $param = new namespace\SimpleParam;
        $param->name = $this->value();
        $this->replace($param);
        $this->pushstate('simpleparam');
    }

    function handleSimpleParam()
    {
        if ($this->token() == a::CLOSEPAREN) {
            $this->popstate(); // return to serverparams
            $this->stack(-1)->addParam($this->stack());
            $this->tokenindex--; // pop the param
            return;
        }
        $this->stack(-1)->value = $this->value();
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
$lex = new TemplateLexer("hi there this is my { text}{% include test fee=\"hi\" foe=fum%}");
$lex->debug();
$parser = new TemplateParser();
$parser->setup($lex);
$ret = $parser->parse();
var_dump($ret);