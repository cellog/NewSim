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
            );
        if ($this->N >= strlen($this->input)) {
            return false; // end of input
        }
        $yy_global_pattern = '/\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)/';

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
        1 => array(0, "\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        2 => array(0, "\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        3 => array(0, "\G(\")|\G(reconnect)|\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        4 => array(0, "\G(reconnect)|\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        5 => array(0, "\G(version)|\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        6 => array(0, "\G(clang )|\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        7 => array(0, "\G(goalie )|\G(error)|\G(warning)|\G(server_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        8 => array(0, "\G(error)|\G(warning)|\G(server_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        9 => array(0, "\G(warning)|\G(server_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        10 => array(0, "\G(server_param)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        11 => array(0, "\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        12 => array(0, "\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        13 => array(0, "\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        14 => array(0, "\G([\-_a-zA-Z0-9]+)"),
        15 => array(0, ""),
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

    if ($this->debug) $this->logger->log("whitespace [" . $this->value . "]");
    return false;
    }
    function yy_r2_13($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("real number [" . $this->value . "]");
    $this->token = self::REALNUMBER;
    }
    function yy_r2_14($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("number [" . $this->value . "]");
    $this->token = self::NUMBER;
    }
    function yy_r2_15($yy_subpatterns)
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

$a = new PlayerLexer('(server_param (audio_cut_dist 50)(auto_mode 0)(back_dash_rate 0.6)(back_passes 1)(ball_accel_max 2.7)(ball_decay 0.94)(ball_rand 0.05)(ball_size 0.085)(ball_speed_max 3)(ball_stuck_area 3)(ball_weight 0.2)(catch_ban_cycle 5)(catch_probability 1)(catchable_area_l 1.2)(catchable_area_w 1)(ckick_margin 1)(clang_advice_win 1)(clang_define_win 1)(clang_del_win 1)(clang_info_win 1)(clang_mess_delay 50)(clang_mess_per_cycle 1)(clang_meta_win 1)(clang_rule_win 1)(clang_win_size 300)(coach 0)(coach_msg_file "")(coach_port 6001)(coach_w_referee 0)(connect_wait 300)(control_radius 2)(dash_angle_step 45)(dash_power_rate 0.006)(drop_ball_time 100)(effort_dec 0.005)(effort_dec_thr 0.3)(effort_inc 0.01)(effort_inc_thr 0.6)(effort_init 1)(effort_min 0.6)(extra_half_time 100)(extra_stamina 50)(forbid_kick_off_offside 1)(foul_cycles 5)(foul_detect_probability 0.5)(foul_exponent 10)(free_kick_faults 1)(freeform_send_period 20)(freeform_wait_period 600)(fullstate_l 0)(fullstate_r 0)(game_log_compression 0)(game_log_dated 1)(game_log_dir "./")(game_log_fixed 0)(game_log_fixed_name "rcssserver")(game_log_version 5)(game_logging 1)(game_over_wait 100)(goal_width 14.02)(goalie_max_moves 2)(golden_goal 0)(half_time 300)(hear_decay 1)(hear_inc 1)(hear_max 1)(inertia_moment 5)(keepaway 0)(keepaway_length 20)(keepaway_log_dated 1)(keepaway_log_dir "./")(keepaway_log_fixed 0)(keepaway_log_fixed_name "rcssserver")(keepaway_logging 1)(keepaway_start -1)(keepaway_width 20)(kick_off_wait 100)(kick_power_rate 0.027)(kick_rand 0.1)(kick_rand_factor_l 1)(kick_rand_factor_r 1)(kickable_margin 0.7)(landmark_file "~/.rcssserver-landmark.xml")(log_date_format "%Y%m%d%H%M-")(log_times 0)(max_back_tackle_power 0)(max_dash_angle 180)(max_dash_power 100)(max_goal_kicks 3)(max_monitors -1)(max_tackle_power 100)(maxmoment 180)(maxneckang 90)(maxneckmoment 180)(maxpower 100)(min_dash_angle -180)(min_dash_power -100)(minmoment -180)(minneckang -90)(minneckmoment -180)(minpower -100)(nr_extra_halfs 2)(nr_normal_halfs 2)(offside_active_area_size 2.5)(offside_kick_margin 9.15)(olcoach_port 6002)(old_coach_hear 0)(pen_allow_mult_kicks 1)(pen_before_setup_wait 10)(pen_coach_moves_players 1)(pen_dist_x 42.5)(pen_max_extra_kicks 5)(pen_max_goalie_dist_x 14)(pen_nr_kicks 5)(pen_random_winner 0)(pen_ready_wait 10)(pen_setup_wait 70)(pen_taken_wait 150)(penalty_shoot_outs 1)(player_accel_max 1)(player_decay 0.4)(player_rand 0.1)(player_size 0.3)(player_speed_max 1.05)(player_speed_max_min 0.75)(player_weight 60)(point_to_ban 5)(point_to_duration 20)(port 6000)(prand_factor_l 1)(prand_factor_r 1)(profile 0)(proper_goal_kicks 0)(quantize_step 0.1)(quantize_step_l 0.01)(record_messages 0)(recover_dec 0.002)(recover_dec_thr 0.3)(recover_init 1)(recover_min 0.5)(recv_step 10)(red_card_probability 0)(say_coach_cnt_max 128)(say_coach_msg_size 128)(say_msg_size 10)(send_comms 0)(send_step 150)(send_vi_step 100)(sense_body_step 100)(side_dash_rate 0.4)(simulator_step 100)(slow_down_factor 1)(slowness_on_top_for_left_team 1)(slowness_on_top_for_right_team 1)(stamina_capacity 130600)(stamina_inc_max 45)(stamina_max 8000)(start_goal_l 0)(start_goal_r 0)(stopped_ball_vel 0.01)(synch_micro_sleep 1)(synch_mode 0)(synch_offset 60)(synch_see_offset 0)(tackle_back_dist 0)(tackle_cycles 10)(tackle_dist 2)(tackle_exponent 6)(tackle_power_rate 0.027)(tackle_rand_factor 2)(tackle_width 1.25)(team_actuator_noise 0)(team_l_start "")(team_r_start "")(text_log_compression 0)(text_log_dated 1)(text_log_dir "./")(text_log_fixed 0)(text_log_fixed_name "rcssserver")(text_logging 1)(use_offside 1)(verbose 0)(visible_angle 90)(visible_distance 3)(wind_ang 0)(wind_dir 0)(wind_force 0)(wind_none 0)(wind_rand 0)(wind_random 0))
', new Logger);
$a->debug = true;

while ($a->yylex());
