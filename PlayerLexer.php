<?php
namespace ThroughBall;
class PlayerLexer
{
    /**
     * describes how to convert a token into a human-readable element for error messages
     *
     * @var array array of token number => textual description of that token
     */
    static $humanReadableTokenNames = array(
        self::INIT => 'init',
        self::IDENTIFIER => '<identifier>',
        self::OPENPAREN => '(',
        self::CLOSEPAREN => ')',
        self::SIDE => 'l|r',
        self::NUMBER => '<number>',
        self::REALNUMBER => '<real number>',
        self::VER => 'ver',
        self::OK => 'ok',
        self::COMPRESSION => 'compression',
        self::WARNING => 'warning',
        self::ERROR => 'error',
        self::RECONNECT => 'reconnect',
        self::SERVERPARAM => 'server_param',
        self::PLAYERPARAM => 'player_param',
        self::PLAYERTYPE => 'player_type',
        self::SENSEBODY => 'sense_body',
        self::VIEWMODE => 'view_mode',
        self::HIGHLOW => 'high/low',
        self::VIEWWIDTH => 'narrow/normal/wide',
        self::QUOTEDSTRING => '<string>',
        self::GOALIE => 'goalie',
    );

    const INIT = 1;
    const IDENTIFIER = 2;
    const OPENPAREN = 3;
    const CLOSEPAREN = 4;
    const SIDE = 5;
    const NUMBER = 6;
    const REALNUMBER = 7;
    const VER = 8;
    const OK = 9;
    const COMPRESSION = 10;
    const WARNING = 11;
    const RECONNECT = 12;
    const ERROR = 13;
    const SERVERPARAM = 14;
    const PLAYERPARAM = 15;
    const PLAYERTYPE = 16;
    const SENSEBODY = 17;
    const VIEWMODE = 18;
    const HIGHLOW = 19;
    const VIEWWIDTH = 20;
    const QUOTEDSTRING = 21;
    const GOALIE = 22;

    private $input;
    public $N;
    public $token;
    public $value;
    public $line;
    public $debug = false;
    public $logger;
    private $_string = '';

    function __construct($data, Logger $log = null)
    {
        $this->input = str_replace("\r\n", "\n", $data);
        $this->N = 0;
        if (null !== $log) {
            $this->debug = true;
            $this->logger = $log;
        }
    }

    /**
     * @param array $tokens an associative array mapping token names to something else
     * @return string a comma-delimited list of human-readable names of the tokens
     */
    function getHumanReadableNames(array $tokens)
    {
        $ret = '';
        foreach ($tokens as $token) {
            if ($ret) {
                $ret .= ', ';
            }
            $ret .= self::$humanReadableTokenNames[$token];
        }
        return $ret; 
    }

    function debug()
    {
        $this->debug = true;
        $this->logger = new DebugEchoLogger;
    }


    private $_yy_state = 1;
    private $_yy_stack = array();

    function yylex()
    {
        return $this->{'yylex' . $this->_yy_state}();
    }

    function yypushstate($state)
    {
        array_push($this->_yy_stack, $this->_yy_state);
        $this->_yy_state = $state;
    }

    function yypopstate()
    {
        $this->_yy_state = array_pop($this->_yy_stack);
    }

    function yybegin($state)
    {
        $this->_yy_state = $state;
    }



    function yylex1()
    {
        $tokenMap = array (
              1 => 0,
              2 => 0,
            );
        if ($this->N >= strlen($this->input)) {
            return false; // end of input
        }
        $yy_global_pattern = '/\G(\\()|\G(\\s+|\r|\n)/';

        do {
            if (preg_match($yy_global_pattern,$this->input, $yymatches, null, $this->N)) {
                $yysubmatches = $yymatches;
                $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->input,
                        $this->N, 5) . '... state YYINITIAL');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                if ($tokenMap[$this->token]) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                        $tokenMap[$this->token]);
                } else {
                    $yysubmatches = array();
                }
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r1_' . $this->token}($yysubmatches);
                if ($r === null) {
                    $this->N += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->N += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->N >= strlen($this->input)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {
                    $yy_yymore_patterns = array(
        1 => array(0, "\G(\\s+|\r|\n)"),
        2 => array(0, ""),
    );

                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[$this->token][1])) {
                            throw new Exception('cannot do yymore for the last token');
                        }
                        $yysubmatches = array();
                        if (preg_match('/' . $yy_yymore_patterns[$this->token][1] . '/',
                              $this->input, $yymatches, null, $this->N)) {
                            $yysubmatches = $yymatches;
                            $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            $this->token += key($yymatches) + $yy_yymore_patterns[$this->token][0]; // token number
                            $this->value = current($yymatches); // token value
                            $this->line = substr_count($this->value, "\n");
                            if ($tokenMap[$this->token]) {
                                // extract sub-patterns for passing to lex function
                                $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                                    $tokenMap[$this->token]);
                            } else {
                                $yysubmatches = array();
                            }
                        }
                        $r = $this->{'yy_r1_' . $this->token}($yysubmatches);
                    } while ($r !== null && !is_bool($r));
                    if ($r === true) {
                        // we have changed state
                        // process this token in the new state
                        return $this->yylex();
                    } elseif ($r === false) {
                        $this->N += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");
                        if ($this->N >= strlen($this->input)) {
                            return false; // end of input
                        }
                        // skip this token
                        continue;
                    } else {
                        // accept
                        $this->N += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");
                        return true;
                    }
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->input[$this->N]);
            }
            break;
        } while (true);

    } // end function


    const YYINITIAL = 1;
    function yy_r1_1($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("parenthesis [" . $this->value . "]");
    $this->yypushstate(self::INTAG);
    $this->token = self::OPENPAREN;
    }
    function yy_r1_2($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("whitespace [" . $this->value . "]");
    }


    function yylex2()
    {
        $tokenMap = array (
              1 => 0,
              2 => 0,
              3 => 0,
              4 => 0,
              5 => 0,
              6 => 0,
              7 => 0,
              8 => 0,
              9 => 0,
              10 => 0,
              11 => 0,
              12 => 0,
              13 => 0,
              14 => 0,
              15 => 0,
              16 => 0,
            );
        if ($this->N >= strlen($this->input)) {
            return false; // end of input
        }
        $yy_global_pattern = '/\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)/';

        do {
            if (preg_match($yy_global_pattern,$this->input, $yymatches, null, $this->N)) {
                $yysubmatches = $yymatches;
                $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->input,
                        $this->N, 5) . '... state INTAG');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                if ($tokenMap[$this->token]) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                        $tokenMap[$this->token]);
                } else {
                    $yysubmatches = array();
                }
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r2_' . $this->token}($yysubmatches);
                if ($r === null) {
                    $this->N += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->N += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->N >= strlen($this->input)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {
                    $yy_yymore_patterns = array(
        1 => array(0, "\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        2 => array(0, "\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        3 => array(0, "\G(\")|\G(reconnect)|\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        4 => array(0, "\G(reconnect)|\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        5 => array(0, "\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        6 => array(0, "\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        7 => array(0, "\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        8 => array(0, "\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        9 => array(0, "\G(warning)|\G(server_param)|\G(player_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        10 => array(0, "\G(server_param)|\G(player_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        11 => array(0, "\G(player_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        12 => array(0, "\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        13 => array(0, "\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        14 => array(0, "\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        15 => array(0, "\G([\-_a-zA-Z0-9]+)"),
        16 => array(0, ""),
    );

                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[$this->token][1])) {
                            throw new Exception('cannot do yymore for the last token');
                        }
                        $yysubmatches = array();
                        if (preg_match('/' . $yy_yymore_patterns[$this->token][1] . '/',
                              $this->input, $yymatches, null, $this->N)) {
                            $yysubmatches = $yymatches;
                            $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            $this->token += key($yymatches) + $yy_yymore_patterns[$this->token][0]; // token number
                            $this->value = current($yymatches); // token value
                            $this->line = substr_count($this->value, "\n");
                            if ($tokenMap[$this->token]) {
                                // extract sub-patterns for passing to lex function
                                $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                                    $tokenMap[$this->token]);
                            } else {
                                $yysubmatches = array();
                            }
                        }
                        $r = $this->{'yy_r2_' . $this->token}($yysubmatches);
                    } while ($r !== null && !is_bool($r));
                    if ($r === true) {
                        // we have changed state
                        // process this token in the new state
                        return $this->yylex();
                    } elseif ($r === false) {
                        $this->N += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");
                        if ($this->N >= strlen($this->input)) {
                            return false; // end of input
                        }
                        // skip this token
                        continue;
                    } else {
                        // accept
                        $this->N += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");
                        return true;
                    }
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->input[$this->N]);
            }
            break;
        } while (true);

    } // end function


    const INTAG = 2;
    function yy_r2_1($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("parenthesis [" . $this->value . "]");
    $this->yypushstate(self::INTAG);
    $this->token = self::OPENPAREN;
    }
    function yy_r2_2($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("parenthesis [" . $this->value . "]");
    $this->token = self::CLOSEPAREN;
    $this->yypopstate();
    }
    function yy_r2_3($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("init [" . $this->value . "]");
    $this->token = self::INIT;
    }
    function yy_r2_4($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("begin string");
    $this->yypushstate(self::INSTRING);
    $this->_string = '';
    $this->N++; // skip the opening quote
    return true;
    }
    function yy_r2_5($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("reconnect [" . $this->value . "]");
    $this->token = self::RECONNECT;
    }
    function yy_r2_6($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("version [" . $this->value . "]");
    $this->token = self::VERSION;
    }
    function yy_r2_7($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("clang [" . $this->value . "]");
    $this->token = self::CLANG;
    $this->N--;
    }
    function yy_r2_8($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("goalie [" . $this->value . "]");
    $this->token = self::GOALIE;
    $this->N--;
    }
    function yy_r2_9($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("error [" . $this->value . "]");
    $this->yypushstate(self::INERROR);
    $this->token = self::ERROR;
    }
    function yy_r2_10($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("warning [" . $this->value . "]");
    $this->yypushstate(self::INERROR);
    $this->token = self::WARNING;
    }
    function yy_r2_11($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("server_param [" . $this->value . "]");
    $this->token = self::SERVERPARAM;
    }
    function yy_r2_12($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("player_param [" . $this->value . "]");
    $this->token = self::PLAYERPARAM;
    }
    function yy_r2_13($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("whitespace [" . $this->value . "]");
    return false;
    }
    function yy_r2_14($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("real number [" . $this->value . "]");
    $this->token = self::REALNUMBER;
    }
    function yy_r2_15($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("number [" . $this->value . "]");
    $this->token = self::NUMBER;
    }
    function yy_r2_16($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("identifier [" . $this->value . "]");
    $this->token = self::IDENTIFIER;
    }


    function yylex3()
    {
        $tokenMap = array (
              1 => 0,
              2 => 0,
              3 => 0,
            );
        if ($this->N >= strlen($this->input)) {
            return false; // end of input
        }
        $yy_global_pattern = '/\G(\\\\)|\G(\")|\G([^[\"\\\\]+)/';

        do {
            if (preg_match($yy_global_pattern,$this->input, $yymatches, null, $this->N)) {
                $yysubmatches = $yymatches;
                $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->input,
                        $this->N, 5) . '... state INSTRING');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                if ($tokenMap[$this->token]) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                        $tokenMap[$this->token]);
                } else {
                    $yysubmatches = array();
                }
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r3_' . $this->token}($yysubmatches);
                if ($r === null) {
                    $this->N += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->N += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->N >= strlen($this->input)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {
                    $yy_yymore_patterns = array(
        1 => array(0, "\G(\")|\G([^[\"\\\\]+)"),
        2 => array(0, "\G([^[\"\\\\]+)"),
        3 => array(0, ""),
    );

                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[$this->token][1])) {
                            throw new Exception('cannot do yymore for the last token');
                        }
                        $yysubmatches = array();
                        if (preg_match('/' . $yy_yymore_patterns[$this->token][1] . '/',
                              $this->input, $yymatches, null, $this->N)) {
                            $yysubmatches = $yymatches;
                            $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            $this->token += key($yymatches) + $yy_yymore_patterns[$this->token][0]; // token number
                            $this->value = current($yymatches); // token value
                            $this->line = substr_count($this->value, "\n");
                            if ($tokenMap[$this->token]) {
                                // extract sub-patterns for passing to lex function
                                $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                                    $tokenMap[$this->token]);
                            } else {
                                $yysubmatches = array();
                            }
                        }
                        $r = $this->{'yy_r3_' . $this->token}($yysubmatches);
                    } while ($r !== null && !is_bool($r));
                    if ($r === true) {
                        // we have changed state
                        // process this token in the new state
                        return $this->yylex();
                    } elseif ($r === false) {
                        $this->N += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");
                        if ($this->N >= strlen($this->input)) {
                            return false; // end of input
                        }
                        // skip this token
                        continue;
                    } else {
                        // accept
                        $this->N += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");
                        return true;
                    }
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->input[$this->N]);
            }
            break;
        } while (true);

    } // end function


    const INSTRING = 3;
    function yy_r3_1($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("string escape");
    $this->yybegin(self::INESCAPE);
    return true;
    }
    function yy_r3_2($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("returning string [$this->_string]");
    $this->yypopstate();
    $this->value = $this->_string;
    $this->token = self::QUOTEDSTRING;
    $this->N -= strlen($this->_string) - 1; // make sure the counter is right
    $this->_string = '';
    }
    function yy_r3_3($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("added to string [".$this->value."]");
    $this->_string .= $this->value;
    return false;
    }


    function yylex4()
    {
        $tokenMap = array (
              1 => 0,
              2 => 0,
            );
        if ($this->N >= strlen($this->input)) {
            return false; // end of input
        }
        $yy_global_pattern = '/\G(\"|\\\\)|\G(.)/';

        do {
            if (preg_match($yy_global_pattern,$this->input, $yymatches, null, $this->N)) {
                $yysubmatches = $yymatches;
                $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->input,
                        $this->N, 5) . '... state INESCAPE');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                if ($tokenMap[$this->token]) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                        $tokenMap[$this->token]);
                } else {
                    $yysubmatches = array();
                }
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r4_' . $this->token}($yysubmatches);
                if ($r === null) {
                    $this->N += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->N += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->N >= strlen($this->input)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {
                    $yy_yymore_patterns = array(
        1 => array(0, "\G(.)"),
        2 => array(0, ""),
    );

                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[$this->token][1])) {
                            throw new Exception('cannot do yymore for the last token');
                        }
                        $yysubmatches = array();
                        if (preg_match('/' . $yy_yymore_patterns[$this->token][1] . '/',
                              $this->input, $yymatches, null, $this->N)) {
                            $yysubmatches = $yymatches;
                            $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            $this->token += key($yymatches) + $yy_yymore_patterns[$this->token][0]; // token number
                            $this->value = current($yymatches); // token value
                            $this->line = substr_count($this->value, "\n");
                            if ($tokenMap[$this->token]) {
                                // extract sub-patterns for passing to lex function
                                $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                                    $tokenMap[$this->token]);
                            } else {
                                $yysubmatches = array();
                            }
                        }
                        $r = $this->{'yy_r4_' . $this->token}($yysubmatches);
                    } while ($r !== null && !is_bool($r));
                    if ($r === true) {
                        // we have changed state
                        // process this token in the new state
                        return $this->yylex();
                    } elseif ($r === false) {
                        $this->N += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");
                        if ($this->N >= strlen($this->input)) {
                            return false; // end of input
                        }
                        // skip this token
                        continue;
                    } else {
                        // accept
                        $this->N += strlen($this->value);
                        $this->line += substr_count($this->value, "\n");
                        return true;
                    }
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->input[$this->N]);
            }
            break;
        } while (true);

    } // end function


    const INESCAPE = 4;
    function yy_r4_1($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("escape [".$this->value."]");
    $this->_string .= '\\' . $this->value;
    return false;
    }
    function yy_r4_2($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("non-escape [".$this->value."]");
    $this->yybegin(self::INSTRING);
    $this->_string .= $this->value;
    return true;
    }

/*lex2php
%statename INERROR
ERRORTYPE {
    if ($this->debug) $this->logger->log("error type [" . $this->value . "]");
    $this->token = self::ERRORTYPE;
}

")" {
    if ($this->debug) $this->logger->log("parenthesis [" . $this->value . "]");
    $this->token = self::CLOSEPAREN;
    $this->yypopstate();
}
*/
}
class Exception extends \Exception {}

class Logger {
    function log($d) {echo $d,"\n";}
}

$a = new PlayerLexer('(player_param (allow_mult_default_type 0)(catchable_area_l_stretch_max 1.3)(catchable_area_l_stretch_min 1)(dash_power_rate_delta_max 0)(dash_power_rate_delta_min 0)(effort_max_delta_factor -0.004)(effort_min_delta_factor -0.004)(extra_stamina_delta_max 50)(extra_stamina_delta_min 0)(foul_detect_probability_delta_factor 0)(inertia_moment_delta_factor 25)(kick_power_rate_delta_max 0)(kick_power_rate_delta_min 0)(kick_rand_delta_factor 1)(kickable_margin_delta_max 0.1)(kickable_margin_delta_min -0.1)(new_dash_power_rate_delta_max 0.0008)(new_dash_power_rate_delta_min -0.0012)(new_stamina_inc_max_delta_factor -6000)(player_decay_delta_max 0.1)(player_decay_delta_min -0.1)(player_size_delta_factor -100)(player_speed_max_delta_max 0)(player_speed_max_delta_min 0)(player_types 18)(pt_max 1)(random_seed 1362027203)(stamina_inc_max_delta_factor 0)(subs_max 3))', new Logger);
$a->debug = true;

while ($a->yylex());
