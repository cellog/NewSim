<?php
namespace ThroughBall;
include __DIR__ . '/PlayerLexer.php';
include __DIR__ . '/ServerParams.php';
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
$lex = new PlayerLexer('(server_param (audio_cut_dist 50)(auto_mode 0)(back_dash_rate 0.6)(back_passes 1)(ball_accel_max 2.7)(ball_decay 0.94)(ball_rand 0.05)(ball_size 0.085)(ball_speed_max 3)(ball_stuck_area 3)(ball_weight 0.2)(catch_ban_cycle 5)(catch_probability 1)(catchable_area_l 1.2)(catchable_area_w 1)(ckick_margin 1)(clang_advice_win 1)(clang_define_win 1)(clang_del_win 1)(clang_info_win 1)(clang_mess_delay 50)(clang_mess_per_cycle 1)(clang_meta_win 1)(clang_rule_win 1)(clang_win_size 300)(coach 0)(coach_msg_file "")(coach_port 6001)(coach_w_referee 0)(connect_wait 300)(control_radius 2)(dash_angle_step 45)(dash_power_rate 0.006)(drop_ball_time 100)(effort_dec 0.005)(effort_dec_thr 0.3)(effort_inc 0.01)(effort_inc_thr 0.6)(effort_init 1)(effort_min 0.6)(extra_half_time 100)(extra_stamina 50)(forbid_kick_off_offside 1)(foul_cycles 5)(foul_detect_probability 0.5)(foul_exponent 10)(free_kick_faults 1)(freeform_send_period 20)(freeform_wait_period 600)(fullstate_l 0)(fullstate_r 0)(game_log_compression 0)(game_log_dated 1)(game_log_dir "./")(game_log_fixed 0)(game_log_fixed_name "rcssserver")(game_log_version 5)(game_logging 1)(game_over_wait 100)(goal_width 14.02)(goalie_max_moves 2)(golden_goal 0)(half_time 300)(hear_decay 1)(hear_inc 1)(hear_max 1)(inertia_moment 5)(keepaway 0)(keepaway_length 20)(keepaway_log_dated 1)(keepaway_log_dir "./")(keepaway_log_fixed 0)(keepaway_log_fixed_name "rcssserver")(keepaway_logging 1)(keepaway_start -1)(keepaway_width 20)(kick_off_wait 100)(kick_power_rate 0.027)(kick_rand 0.1)(kick_rand_factor_l 1)(kick_rand_factor_r 1)(kickable_margin 0.7)(landmark_file "~/.rcssserver-landmark.xml")(log_date_format "%Y%m%d%H%M-")(log_times 0)(max_back_tackle_power 0)(max_dash_angle 180)(max_dash_power 100)(max_goal_kicks 3)(max_monitors -1)(max_tackle_power 100)(maxmoment 180)(maxneckang 90)(maxneckmoment 180)(maxpower 100)(min_dash_angle -180)(min_dash_power -100)(minmoment -180)(minneckang -90)(minneckmoment -180)(minpower -100)(nr_extra_halfs 2)(nr_normal_halfs 2)(offside_active_area_size 2.5)(offside_kick_margin 9.15)(olcoach_port 6002)(old_coach_hear 0)(pen_allow_mult_kicks 1)(pen_before_setup_wait 10)(pen_coach_moves_players 1)(pen_dist_x 42.5)(pen_max_extra_kicks 5)(pen_max_goalie_dist_x 14)(pen_nr_kicks 5)(pen_random_winner 0)(pen_ready_wait 10)(pen_setup_wait 70)(pen_taken_wait 150)(penalty_shoot_outs 1)(player_accel_max 1)(player_decay 0.4)(player_rand 0.1)(player_size 0.3)(player_speed_max 1.05)(player_speed_max_min 0.75)(player_weight 60)(point_to_ban 5)(point_to_duration 20)(port 6000)(prand_factor_l 1)(prand_factor_r 1)(profile 0)(proper_goal_kicks 0)(quantize_step 0.1)(quantize_step_l 0.01)(record_messages 0)(recover_dec 0.002)(recover_dec_thr 0.3)(recover_init 1)(recover_min 0.5)(recv_step 10)(red_card_probability 0)(say_coach_cnt_max 128)(say_coach_msg_size 128)(say_msg_size 10)(send_comms 0)(send_step 150)(send_vi_step 100)(sense_body_step 100)(side_dash_rate 0.4)(simulator_step 100)(slow_down_factor 1)(slowness_on_top_for_left_team 1)(slowness_on_top_for_right_team 1)(stamina_capacity 130600)(stamina_inc_max 45)(stamina_max 8000)(start_goal_l 0)(start_goal_r 0)(stopped_ball_vel 0.01)(synch_micro_sleep 1)(synch_mode 0)(synch_offset 60)(synch_see_offset 0)(tackle_back_dist 0)(tackle_cycles 10)(tackle_dist 2)(tackle_exponent 6)(tackle_power_rate 0.027)(tackle_rand_factor 2)(tackle_width 1.25)(team_actuator_noise 0)(team_l_start "")(team_r_start "")(text_log_compression 0)(text_log_dated 1)(text_log_dir "./")(text_log_fixed 0)(text_log_fixed_name "rcssserver")(text_logging 1)(use_offside 1)(verbose 0)(visible_angle 90)(visible_distance 3)(wind_ang 0)(wind_dir 0)(wind_force 0)(wind_none 0)(wind_rand 0)(wind_random 0))', new Logger);
$lex->debug = true;
$parser = new PlayerParser();
$parser->setup($lex);
$ret = $parser->parse();
var_dump($ret);