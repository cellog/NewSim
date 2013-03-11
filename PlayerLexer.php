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
        self::SEE => 'see',
        self::BALL => '< ball (b)>', // ball inside view cone
        self::UNCLEARBALL => '<unclear ball (B)>', // ball near but outside view cone
        self::UNCLEARFLAG => '<unclear flag (B)>',
        self::UNCLEARPLAYER => '<unclear player (P)>',
        self::UNCLEARGOAL => '<unclear goal (G)>',
        self::GOALL => 'left goal center point (g l)',
        self::GOALR => 'right goal center point (g r)',
        self::CENTERFLAG => 'center flag (f c)',
        self::LEFTTOPFLAG => 'corner flag left/top (f l t)',
        self::CENTERTOPFLAG => 'flag center/top (f c t)',
        self::RIGHTTOPFLAG => 'corner flag right/top (f r t)',
        self::LEFTBOTTOMFLAG => 'corner flag left/bottom (f l b)',
        self::CENTERBOTTOMFLAG => 'flag center/bottom (f c b)',
        self::RIGHTBOTTOMFLAG => 'corner flag right/bottom (f r b)',
        self::PENALTYLEFTTOP => 'penalty box left/top corner (f p l t)',
        self::PENALTYLEFTCENTER => 'penalty box left/center point (f p l c)',
        self::PENALTYLEFTBOTTOM => 'penalty box left/bottom corner (f p l b)',
        self::PENALTYRIGHTTOP => 'penalty box right/top corner (f p r t)',
        self::PENALTYRIGHTCENTER => 'penalty box right/center point (f p r c)',
        self::PENALTYRIGHTBOTTOM => 'penalty box right/bottom corner (f p r b)',
        self::GOALLEFTTOP => 'left goal, top corner (f g l t)',
        self::GOALLEFTBOTTOM => 'left goal, bottom corner (f g l b)',
        self::GOALRIGHTTOP => 'right goal, top corner (f g r t)',
        self::GOALRIGHTBOTTOM => 'right goal, bottom corner (f g r b)',
        self::LINERIGHT => 'right line (l r)',
        self::LINETOP => 'top line (l t)',
        self::LINELEFT => 'left line (l l)',
        self::LINEBOTTOM => 'bottom line (l b)',
        self::FLAGRIGHT => 'right flag (f r 0)',
        self::FLAGTOP => 'top flag (f t 0)',
        self::FLAGLEFT => 'left flag (f l 0)',
        self::FLAGBOTTOM => 'bottom flag (f b 0)',
	self::VIRTUALFLAGLT30 => 'virtual flag left top 30 (f l t 30)',
	self::VIRTUALFLAGLT20 => 'virtual flag left top 20 (f l t 20)',
	self::VIRTUALFLAGLT10 => 'virtual flag left top 10 (f l t 10)',
	self::VIRTUALFLAGLB10 => 'virtual flag left bottom 10 (f l b 10)',
	self::VIRTUALFLAGLB20 => 'virtual flag left bottom 20 (f l b 20)',
	self::VIRTUALFLAGLB30 => 'virtual flag left bottom 30 (f l b 30)',

	self::VIRTUALFLAGBL50 => 'virtual flag bottom left 50 (f b l 50)',
	self::VIRTUALFLAGBL40 => 'virtual flag bottom left 40 (f b l 40)',
	self::VIRTUALFLAGBL30 => 'virtual flag bottom left 30 (f b l 30)',
	self::VIRTUALFLAGBL20 => 'virtual flag bottom left 20 (f b l 20)',
	self::VIRTUALFLAGBL10 => 'virtual flag bottom left 10 (f b l 10)',
	self::VIRTUALFLAGBR10 => 'virtual flag bottom right 10 (f b r 10)',
	self::VIRTUALFLAGBR20 => 'virtual flag bottom right 20 (f b r 20)',
	self::VIRTUALFLAGBR30 => 'virtual flag bottom right 30 (f b r 30)',
	self::VIRTUALFLAGBR40 => 'virtual flag bottom right 40 (f b r 40)',
	self::VIRTUALFLAGBR50 => 'virtual flag bottom right 50 (f b r 50)',

	self::VIRTUALFLAGRT30 => 'virtual flag right top 30 (f r t 30)',
	self::VIRTUALFLAGRT20 => 'virtual flag right top 20 (f r t 20)',
	self::VIRTUALFLAGRT10 => 'virtual flag right top 10 (f r t 10)',
	self::VIRTUALFLAGRB10 => 'virtual flag right bottom 10 (f l t 10)',
	self::VIRTUALFLAGRB20 => 'virtual flag right bottom 20 (f l t 20)',
	self::VIRTUALFLAGRB30 => 'virtual flag right bottom 30 (f l t 30)',

	self::VIRTUALFLAGTL50 => 'virtual flag top left 50 (f t l 50)',
	self::VIRTUALFLAGTL40 => 'virtual flag top left 40 (f t l 40)',
	self::VIRTUALFLAGTL30 => 'virtual flag top left 30 (f t l 30)',
	self::VIRTUALFLAGTL20 => 'virtual flag top left 20 (f t l 20)',
	self::VIRTUALFLAGTL10 => 'virtual flag top left 10 (f t l 10)',
	self::VIRTUALFLAGTR10 => 'virtual flag top right 10 (f t r 10)',
	self::VIRTUALFLAGTR20 => 'virtual flag top right 20 (f t r 20)',
	self::VIRTUALFLAGTR30 => 'virtual flag top right 30 (f t r 30)',
	self::VIRTUALFLAGTR40 => 'virtual flag top right 40 (f t r 40)',
	self::VIRTUALFLAGTR50 => 'virtual flag top right 50 (f t r 50)',
        
        self::PLAYER => 'player (p)',
        self::STAMINA => 'stamina',
        self::SPEED => 'speed',
        self::ARM => 'arm',
        self::TARGET => 'target',
        self::FOCUS => 'focus',
        self::TACKLE => 'tackle',
        self::FOUL => 'foul',
        self::COLLISION => 'collision',
        self::HEAR => 'hear',
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
    const SEE = 23;
    const UNCLEARBALL = 24;
    const UNCLEARFLAG = 25;
    const UNCLEARPLAYER = 26;
    const UNCLEARGOAL = 27;
    const GOALL = 28;
    const GOALR = 29;
    const CENTERFLAG = 30;
    const LEFTTOPFLAG = 31;
    const CENTERTOPFLAG = 32;
    const RIGHTTOPFLAG = 33;
    const LEFTBOTTOMFLAG = 34;
    const CENTERBOTTOMFLAG = 35;
    const RIGHTBOTTOMFLAG = 36;
    const PENALTYLEFTTOP = 37;
    const PENALTYLEFTCENTER = 38;
    const PENALTYLEFTBOTTOM = 39;
    const GOALLEFTTOP = 40;
    const GOALLEFTBOTTOM = 41;
    const GOALRIGHTTOP = 42;
    const GOALRIGHTBOTTOM = 43;
    const LINERIGHT = 44;
    const LINETOP = 45;
    const LINELEFT = 46;
    const LINEBOTTOM = 47;
    const FLAGRIGHT = 48;
    const FLAGTOP = 49;
    const FLAGLEFT = 50;
    const FLAGBOTTOM = 51;
    const VIRTUALFLAGLT30 = 52;
    const VIRTUALFLAGLT20 = 53;
    const VIRTUALFLAGLT10 = 54;
    const VIRTUALFLAGLB10 = 55;
    const VIRTUALFLAGLB20 = 56;
    const VIRTUALFLAGLB30 = 57;

    const VIRTUALFLAGBL50 = 58;
    const VIRTUALFLAGBL40 = 59;
    const VIRTUALFLAGBL30 = 60;
    const VIRTUALFLAGBL20 = 61;
    const VIRTUALFLAGBL10 = 62;
    const VIRTUALFLAGBR10 = 63;
    const VIRTUALFLAGBR20 = 64;
    const VIRTUALFLAGBR30 = 65;
    const VIRTUALFLAGBR40 = 66;
    const VIRTUALFLAGBR50 = 67;

    const VIRTUALFLAGRT30 = 68;
    const VIRTUALFLAGRT20 = 69;
    const VIRTUALFLAGRT10 = 70;
    const VIRTUALFLAGRB10 = 71;
    const VIRTUALFLAGRB20 = 72;
    const VIRTUALFLAGRB30 = 73;

    const VIRTUALFLAGTL50 = 74;
    const VIRTUALFLAGTL40 = 75;
    const VIRTUALFLAGTL30 = 76;
    const VIRTUALFLAGTL20 = 77;
    const VIRTUALFLAGTL10 = 78;
    const VIRTUALFLAGTR10 = 79;
    const VIRTUALFLAGTR20 = 80;
    const VIRTUALFLAGTR30 = 81;
    const VIRTUALFLAGTR40 = 82;
    const VIRTUALFLAGTR50 = 83;
    
    const PENALTYRIGHTTOP = 84;
    const PENALTYRIGHTCENTER = 85;
    const PENALTYRIGHTBOTTOM = 86;
    const PLAYER = 87;
    const BALL = 88;
    const STAMINA = 89;
    const SPEED = 90;
    const ARM = 91;
    const TARGET = 92;
    const FOCUS = 93;
    const TACKLE = 94;
    const FOUL = 95;
    const COLLISION = 96;
    const HEAR = 97;

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
              18 => 0,
              19 => 0,
              20 => 0,
              21 => 0,
              22 => 0,
              23 => 0,
              24 => 0,
              25 => 0,
              26 => 0,
              27 => 0,
              28 => 0,
              29 => 0,
              30 => 0,
              31 => 0,
              32 => 0,
              33 => 0,
              34 => 0,
              35 => 0,
              36 => 0,
              37 => 0,
              38 => 0,
              39 => 0,
              40 => 0,
              41 => 0,
              42 => 0,
              43 => 0,
              44 => 0,
              45 => 0,
              46 => 0,
              47 => 0,
              48 => 0,
              49 => 0,
              50 => 0,
              51 => 0,
              52 => 0,
              53 => 0,
              54 => 0,
              55 => 0,
              56 => 0,
              57 => 0,
              58 => 0,
              59 => 0,
              60 => 0,
              61 => 0,
              62 => 0,
              63 => 0,
              64 => 0,
            );
        if ($this->N >= strlen($this->input)) {
            return false; // end of input
        }
        $yy_global_pattern = '/\G(\\(B\\))|\G(\\(b\\))|\G(\\(P\\))|\G(\\(F\\))|\G(\\(G\\))|\G(\\(g l\\))|\G(\\(g r\\))|\G(\\(f c\\))|\G(\\(f p l t\\))|\G(\\(f p l c\\))|\G(\\(f p l b\\))|\G(\\(f p r t\\))|\G(\\(f p r c\\))|\G(\\(f p r b\\))|\G(\\(f l t\\))|\G(\\(f c t\\))|\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)/';

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
        1 => array(0, "\G(\\(b\\))|\G(\\(P\\))|\G(\\(F\\))|\G(\\(G\\))|\G(\\(g l\\))|\G(\\(g r\\))|\G(\\(f c\\))|\G(\\(f p l t\\))|\G(\\(f p l c\\))|\G(\\(f p l b\\))|\G(\\(f p r t\\))|\G(\\(f p r c\\))|\G(\\(f p r b\\))|\G(\\(f l t\\))|\G(\\(f c t\\))|\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        2 => array(0, "\G(\\(P\\))|\G(\\(F\\))|\G(\\(G\\))|\G(\\(g l\\))|\G(\\(g r\\))|\G(\\(f c\\))|\G(\\(f p l t\\))|\G(\\(f p l c\\))|\G(\\(f p l b\\))|\G(\\(f p r t\\))|\G(\\(f p r c\\))|\G(\\(f p r b\\))|\G(\\(f l t\\))|\G(\\(f c t\\))|\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        3 => array(0, "\G(\\(F\\))|\G(\\(G\\))|\G(\\(g l\\))|\G(\\(g r\\))|\G(\\(f c\\))|\G(\\(f p l t\\))|\G(\\(f p l c\\))|\G(\\(f p l b\\))|\G(\\(f p r t\\))|\G(\\(f p r c\\))|\G(\\(f p r b\\))|\G(\\(f l t\\))|\G(\\(f c t\\))|\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        4 => array(0, "\G(\\(G\\))|\G(\\(g l\\))|\G(\\(g r\\))|\G(\\(f c\\))|\G(\\(f p l t\\))|\G(\\(f p l c\\))|\G(\\(f p l b\\))|\G(\\(f p r t\\))|\G(\\(f p r c\\))|\G(\\(f p r b\\))|\G(\\(f l t\\))|\G(\\(f c t\\))|\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        5 => array(0, "\G(\\(g l\\))|\G(\\(g r\\))|\G(\\(f c\\))|\G(\\(f p l t\\))|\G(\\(f p l c\\))|\G(\\(f p l b\\))|\G(\\(f p r t\\))|\G(\\(f p r c\\))|\G(\\(f p r b\\))|\G(\\(f l t\\))|\G(\\(f c t\\))|\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        6 => array(0, "\G(\\(g r\\))|\G(\\(f c\\))|\G(\\(f p l t\\))|\G(\\(f p l c\\))|\G(\\(f p l b\\))|\G(\\(f p r t\\))|\G(\\(f p r c\\))|\G(\\(f p r b\\))|\G(\\(f l t\\))|\G(\\(f c t\\))|\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        7 => array(0, "\G(\\(f c\\))|\G(\\(f p l t\\))|\G(\\(f p l c\\))|\G(\\(f p l b\\))|\G(\\(f p r t\\))|\G(\\(f p r c\\))|\G(\\(f p r b\\))|\G(\\(f l t\\))|\G(\\(f c t\\))|\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        8 => array(0, "\G(\\(f p l t\\))|\G(\\(f p l c\\))|\G(\\(f p l b\\))|\G(\\(f p r t\\))|\G(\\(f p r c\\))|\G(\\(f p r b\\))|\G(\\(f l t\\))|\G(\\(f c t\\))|\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        9 => array(0, "\G(\\(f p l c\\))|\G(\\(f p l b\\))|\G(\\(f p r t\\))|\G(\\(f p r c\\))|\G(\\(f p r b\\))|\G(\\(f l t\\))|\G(\\(f c t\\))|\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        10 => array(0, "\G(\\(f p l b\\))|\G(\\(f p r t\\))|\G(\\(f p r c\\))|\G(\\(f p r b\\))|\G(\\(f l t\\))|\G(\\(f c t\\))|\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        11 => array(0, "\G(\\(f p r t\\))|\G(\\(f p r c\\))|\G(\\(f p r b\\))|\G(\\(f l t\\))|\G(\\(f c t\\))|\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        12 => array(0, "\G(\\(f p r c\\))|\G(\\(f p r b\\))|\G(\\(f l t\\))|\G(\\(f c t\\))|\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        13 => array(0, "\G(\\(f p r b\\))|\G(\\(f l t\\))|\G(\\(f c t\\))|\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        14 => array(0, "\G(\\(f l t\\))|\G(\\(f c t\\))|\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        15 => array(0, "\G(\\(f c t\\))|\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        16 => array(0, "\G(\\(f r t\\))|\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        17 => array(0, "\G(\\(f l b\\))|\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        18 => array(0, "\G(\\(f c b\\))|\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        19 => array(0, "\G(\\(f r b\\))|\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        20 => array(0, "\G(\\(f g l t\\))|\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        21 => array(0, "\G(\\(f g l b\\))|\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        22 => array(0, "\G(\\(f g r t\\))|\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        23 => array(0, "\G(\\(f g r b\\))|\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        24 => array(0, "\G(\\(l r\\))|\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        25 => array(0, "\G(\\(l t\\))|\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        26 => array(0, "\G(\\(l l\\))|\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        27 => array(0, "\G(\\(l b\\))|\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        28 => array(0, "\G(\\(f r 0\\))|\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        29 => array(0, "\G(\\(f t 0\\))|\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        30 => array(0, "\G(\\(f l 0\\))|\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        31 => array(0, "\G(\\(f b 0\\))|\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        32 => array(0, "\G(\\(f [tblr] [tblr] [1-5]0\\))|\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        33 => array(0, "\G(p )|\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        34 => array(0, "\G(\\()|\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        35 => array(0, "\G(\\))|\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        36 => array(0, "\G(init)|\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        37 => array(0, "\G(\")|\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        38 => array(0, "\G(reconnect)|\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        39 => array(0, "\G(version)|\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        40 => array(0, "\G(hear)|\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        41 => array(0, "\G(clang )|\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        42 => array(0, "\G(goalie )|\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        43 => array(0, "\G(goalie\\))|\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        44 => array(0, "\G(view_mode)|\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        45 => array(0, "\G(error)|\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        46 => array(0, "\G(warning)|\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        47 => array(0, "\G(server_param)|\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        48 => array(0, "\G(player_param)|\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        49 => array(0, "\G(player_type)|\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        50 => array(0, "\G(see)|\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        51 => array(0, "\G(sense_body)|\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        52 => array(0, "\G(stamina)|\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        53 => array(0, "\G(speed)|\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        54 => array(0, "\G(arm)|\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        55 => array(0, "\G(target)|\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        56 => array(0, "\G(focus)|\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        57 => array(0, "\G(collision)|\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        58 => array(0, "\G(tackle)|\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        59 => array(0, "\G(foul)|\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        60 => array(0, "\G(\\s+|\r|\n)|\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        61 => array(0, "\G(-?[0-9]+\\.[0-9]+)|\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        62 => array(0, "\G(-?[0-9]+)|\G([\-_a-zA-Z0-9]+)"),
        63 => array(0, "\G([\-_a-zA-Z0-9]+)"),
        64 => array(0, ""),
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

    if ($this->debug) $this->logger->log("unclear ball [" . $this->value . "]");
    $this->token = self::UNCLEARBALL;
    }
    function yy_r2_2($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("ball [" . $this->value . "]");
    $this->token = self::BALL;
    }
    function yy_r2_3($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("unclear player [" . $this->value . "]");
    $this->token = self::UNCLEARPLAYER;
    }
    function yy_r2_4($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("unclear flag [" . $this->value . "]");
    $this->token = self::UNCLEARFLAG;
    }
    function yy_r2_5($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("unclear goal [" . $this->value . "]");
    $this->token = self::UNCLEARGOAL;
    }
    function yy_r2_6($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("left goal [" . $this->value . "]");
    $this->token = self::GOALL;
    }
    function yy_r2_7($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("right goal [" . $this->value . "]");
    $this->token = self::GOALR;
    }
    function yy_r2_8($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("center flag [" . $this->value . "]");
    $this->token = self::CENTERFLAG;
    }
    function yy_r2_9($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("penalty box left/top [" . $this->value . "]");
    $this->token = self::PENALTYLEFTTOP;
    }
    function yy_r2_10($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("penalty box left/center [" . $this->value . "]");
    $this->token = self::PENALTYLEFTCENTER;
    }
    function yy_r2_11($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("penalty box left/bottom [" . $this->value . "]");
    $this->token = self::PENALTYLEFTBOTTOM;
    }
    function yy_r2_12($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("penalty box right/top [" . $this->value . "]");
    $this->token = self::PENALTYRIGHTTOP;
    }
    function yy_r2_13($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("penalty box right/center [" . $this->value . "]");
    $this->token = self::PENALTYRIGHTCENTER;
    }
    function yy_r2_14($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("penalty box right/bottom [" . $this->value . "]");
    $this->token = self::PENALTYRIGHTBOTTOM;
    }
    function yy_r2_15($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("corner flag left/top [" . $this->value . "]");
    $this->token = self::LEFTTOPFLAG;
    }
    function yy_r2_16($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("flag center/top [" . $this->value . "]");
    $this->token = self::CENTERTOPFLAG;
    }
    function yy_r2_17($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("corner flag right/top [" . $this->value . "]");
    $this->token = self::RIGHTTOPFLAG;
    }
    function yy_r2_18($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("corner flag left/bottom [" . $this->value . "]");
    $this->token = self::LEFTBOTTOMFLAG;
    }
    function yy_r2_19($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("flag center/bottom [" . $this->value . "]");
    $this->token = self::CENTERBOTTOMFLAG;
    }
    function yy_r2_20($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("corner flag right/bottom [" . $this->value . "]");
    $this->token = self::RIGHTBOTTOMFLAG;
    }
    function yy_r2_21($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("left goal top corner [" . $this->value . "]");
    $this->token = self::GOALLEFTTOP;
    }
    function yy_r2_22($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("left goal bottom corner [" . $this->value . "]");
    $this->token = self::GOALLEFTBOTTOM;
    }
    function yy_r2_23($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("right goal top corner [" . $this->value . "]");
    $this->token = self::GOALRIGHTTOP;
    }
    function yy_r2_24($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("right goal bottom corner [" . $this->value . "]");
    $this->token = self::GOALRIGHTBOTTOM;
    }
    function yy_r2_25($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("right line [" . $this->value . "]");
    $this->token = self::LINERIGHT;
    }
    function yy_r2_26($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("top line [" . $this->value . "]");
    $this->token = self::LINETOP;
    }
    function yy_r2_27($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("left line [" . $this->value . "]");
    $this->token = self::LINELEFT;
    }
    function yy_r2_28($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("bottom line [" . $this->value . "]");
    $this->token = self::LINEBOTTOM;
    }
    function yy_r2_29($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("right flag [" . $this->value . "]");
    $this->token = self::FLAGRIGHT;
    }
    function yy_r2_30($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("top flag [" . $this->value . "]");
    $this->token = self::FLAGTOP;
    }
    function yy_r2_31($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("left flag [" . $this->value . "]");
    $this->token = self::FLAGLEFT;
    }
    function yy_r2_32($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("bottom flag [" . $this->value . "]");
    $this->token = self::FLAGBOTTOM;
    }
    function yy_r2_33($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("virtual flag [" . $this->value . "]");
    $flags = array(
        // left side of the field
        '(f l t 30)' => self::VIRTUALFLAGLT30,
        '(f l t 20)' => self::VIRTUALFLAGLT20,
        '(f l t 10)' => self::VIRTUALFLAGLT10,
        '(f l b 10)' => self::VIRTUALFLAGLB10,
        '(f l b 20)' => self::VIRTUALFLAGLB20,
        '(f l b 30)' => self::VIRTUALFLAGLB30,
        // bottom side of the field
        '(f b l 50)' => self::VIRTUALFLAGBL50,
        '(f b l 40)' => self::VIRTUALFLAGBL40,
        '(f b l 30)' => self::VIRTUALFLAGBL30,
        '(f b l 20)' => self::VIRTUALFLAGBL20,
        '(f b l 10)' => self::VIRTUALFLAGBL10,
        '(f b r 10)' => self::VIRTUALFLAGBR10,
        '(f b r 20)' => self::VIRTUALFLAGBR30,
        '(f b r 30)' => self::VIRTUALFLAGBR30,
        '(f b r 40)' => self::VIRTUALFLAGBR40,
        '(f b r 50)' => self::VIRTUALFLAGBR50,
        // right side of the field
        '(f r t 30)' => self::VIRTUALFLAGRT30,
        '(f r t 20)' => self::VIRTUALFLAGRT20,
        '(f r t 10)' => self::VIRTUALFLAGRT10,
        '(f r b 10)' => self::VIRTUALFLAGRB10,
        '(f r b 20)' => self::VIRTUALFLAGRB20,
        '(f r b 30)' => self::VIRTUALFLAGRB30,
        // top side of the field
        '(f t l 50)' => self::VIRTUALFLAGTL50,
        '(f t l 40)' => self::VIRTUALFLAGTL40,
        '(f t l 30)' => self::VIRTUALFLAGTL30,
        '(f t l 20)' => self::VIRTUALFLAGTL20,
        '(f t l 10)' => self::VIRTUALFLAGTL10,
        '(f t r 10)' => self::VIRTUALFLAGTR10,
        '(f t r 20)' => self::VIRTUALFLAGTR30,
        '(f t r 30)' => self::VIRTUALFLAGTR30,
        '(f t r 40)' => self::VIRTUALFLAGTR40,
        '(f t r 50)' => self::VIRTUALFLAGTR50,
    );
    $this->token = $flags[$this->value];
    }
    function yy_r2_34($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("player [" . $this->value . "]");
    $this->token = self::PLAYER;
    $this->N--;
    }
    function yy_r2_35($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("parenthesis [" . $this->value . "]");
    $this->yypushstate(self::INTAG);
    $this->token = self::OPENPAREN;
    }
    function yy_r2_36($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("parenthesis [" . $this->value . "]");
    $this->token = self::CLOSEPAREN;
    $this->yypopstate();
    }
    function yy_r2_37($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("init [" . $this->value . "]");
    $this->token = self::INIT;
    }
    function yy_r2_38($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("begin string");
    $this->yypushstate(self::INSTRING);
    $this->_string = '';
    $this->N++; // skip the opening quote
    return true;
    }
    function yy_r2_39($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("reconnect [" . $this->value . "]");
    $this->token = self::RECONNECT;
    }
    function yy_r2_40($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("version [" . $this->value . "]");
    $this->token = self::VERSION;
    }
    function yy_r2_41($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("hear [" . $this->value . "]");
    $this->token = self::HEAR;
    }
    function yy_r2_42($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("clang [" . $this->value . "]");
    $this->token = self::CLANG;
    $this->N--;
    }
    function yy_r2_43($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("goalie [" . $this->value . "]");
    $this->token = self::GOALIE;
    $this->N--;
    }
    function yy_r2_44($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("goalie [" . $this->value . "]");
    $this->token = self::GOALIE;
    $this->N--;
    }
    function yy_r2_45($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("view_mode [" . $this->value . "]");
    $this->token = self::VIEWMODE;
    }
    function yy_r2_46($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("error [" . $this->value . "]");
    $this->yypushstate(self::INERROR);
    $this->token = self::ERROR;
    }
    function yy_r2_47($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("warning [" . $this->value . "]");
    $this->yypushstate(self::INERROR);
    $this->token = self::WARNING;
    }
    function yy_r2_48($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("server_param [" . $this->value . "]");
    $this->token = self::SERVERPARAM;
    }
    function yy_r2_49($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("player_param [" . $this->value . "]");
    $this->token = self::PLAYERPARAM;
    }
    function yy_r2_50($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("player_type [" . $this->value . "]");
    $this->token = self::PLAYERTYPE;
    }
    function yy_r2_51($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("see [" . $this->value . "]");
    $this->token = self::SEE;
    }
    function yy_r2_52($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("sense_body [" . $this->value . "]");
    $this->token = self::SENSEBODY;
    }
    function yy_r2_53($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("stamina [" . $this->value . "]");
    $this->token = self::STAMINA;
    }
    function yy_r2_54($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("speed [" . $this->value . "]");
    $this->token = self::SPEED;
    }
    function yy_r2_55($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("arm [" . $this->value . "]");
    $this->token = self::ARM;
    }
    function yy_r2_56($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("target [" . $this->value . "]");
    $this->token = self::TARGET;
    }
    function yy_r2_57($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("focus [" . $this->value . "]");
    $this->token = self::FOCUS;
    }
    function yy_r2_58($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("collision [" . $this->value . "]");
    $this->token = self::COLLISION;
    }
    function yy_r2_59($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("tackle [" . $this->value . "]");
    $this->token = self::TACKLE;
    }
    function yy_r2_60($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("foul [" . $this->value . "]");
    $this->token = self::FOUL;
    }
    function yy_r2_61($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("whitespace [" . $this->value . "]");
    return false;
    }
    function yy_r2_62($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("real number [" . $this->value . "]");
    $this->token = self::REALNUMBER;
    }
    function yy_r2_63($yy_subpatterns)
    {

    if ($this->debug) $this->logger->log("number [" . $this->value . "]");
    $this->token = self::NUMBER;
    }
    function yy_r2_64($yy_subpatterns)
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