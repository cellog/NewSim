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
    return false;
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
              17 => 0,
            );
        if ($this->N >= strlen($this->input)) {
            return false; // end of input
        }
        $yy_global_pattern = '/\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)/';

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
        1 => array(0, "\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        2 => array(0, "\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        3 => array(0, "\G(\")|\G(reconnect)|\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        4 => array(0, "\G(reconnect)|\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        5 => array(0, "\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        6 => array(0, "\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        7 => array(0, "\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        8 => array(0, "\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        9 => array(0, "\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        10 => array(0, "\G(server_param)|\G(player_param)|\G(player_type)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        11 => array(0, "\G(player_param)|\G(player_type)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        12 => array(0, "\G(player_type)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        13 => array(0, "\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        14 => array(0, "\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        15 => array(0, "\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        16 => array(0, "\G([\-_a-zA-Z0-9]+)"),
        17 => array(0, ""),
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

    if ($this->debug) $this->logger->log("player_type [" . $this->value . "]");
    $this->token = self::PLAYERTYPE;
    }
    function yy_r2_14($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("whitespace [" . $this->value . "]");
    return false;
    }
    function yy_r2_15($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("real number [" . $this->value . "]");
    $this->token = self::REALNUMBER;
    }
    function yy_r2_16($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("number [" . $this->value . "]");
    $this->token = self::NUMBER;
    }
    function yy_r2_17($yy_subpatterns)
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

$a = new PlayerLexer('(player_type (id 0)(player_speed_max 1.05)(stamina_inc_max 45)(player_decay 0.4)(inertia_moment 5)(dash_power_rate 0.006)(player_size 0.3)(kickable_margin 0.7)(kick_rand 0.1)(extra_stamina 50)(effort_max 1)(effort_min 0.6)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1))
(player_type (id 1)(player_speed_max 1.05)(stamina_inc_max 50.5118)(player_decay 0.357182)(inertia_moment 3.92956)(dash_power_rate 0.00508136)(player_size 0.3)(kickable_margin 0.602344)(kick_rand 0.00234432)(extra_stamina 60.5673)(effort_max 0.957731)(effort_min 0.557731)(kick_power_rate 0.027)(foul_detect_probability 0.5)(catchable_area_l_stretch 1.1378))
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
$a->debug = true;

while ($a->yylex());
