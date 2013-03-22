<?php
namespace ThroughBall;
class ServerParams
{

    const DEFAULT_MAX_PLAYER = 11;
    const DEFAULT_PITCH_LENGTH = 105.0;
    const DEFAULT_PITCH_WIDTH = 68.0;
    const DEFAULT_PITCH_MARGIN = 5.0;
    const DEFAULT_CENTER_CIRCLE_R = 9.15;
    const DEFAULT_PENALTY_AREA_LENGTH = 16.5;
    const DEFAULT_PENALTY_AREA_WIDTH = 40.32;
    const DEFAULT_PENALTY_CIRCLE_R = 9.15;
    const DEFAULT_PENALTY_SPOT_DIST = 11.0;
    const DEFAULT_GOAL_AREA_LENGTH = 5.5;
    const DEFAULT_GOAL_AREA_WIDTH = 18.32;
    const DEFAULT_GOAL_DEPTH = 2.44;
    const DEFAULT_CORNER_ARC_R = 1.0;
//    const DEFAULT_KICK_OFF_CLEAR_DISTANCE = ServerParam::CENTER_CIRCLE_R;
    const DEFAULT_GOAL_POST_RADIUS = 0.06;

    const DEFAULT_WIND_WEIGHT = 10000.0;



    const DEFAULT_GOAL_WIDTH = 14.02;
    const DEFAULT_INERTIA_MOMENT = 5.0;

    const DEFAULT_PLAYER_SIZE = 0.3;
    const DEFAULT_PLAYER_DECAY = 0.4;
    const DEFAULT_PLAYER_RAND = 0.05;
    const DEFAULT_PLAYER_WEIGHT = 60.0;
    const DEFAULT_PLAYER_SPEED_MAX = 1.2;
    const DEFAULT_PLAYER_ACCEL_MAX = 1.0;

    const DEFAULT_STAMINA_MAX = 4000.0;
    const DEFAULT_STAMINA_INC_MAX = 45.0;

    const DEFAULT_RECOVER_INIT = 1.0;
    const DEFAULT_RECOVER_DEC_THR = 0.3;
    const DEFAULT_RECOVER_MIN = 0.5;
    const DEFAULT_RECOVER_DEC = 0.002;

    const DEFAULT_EFFORT_INIT = 1.0;
    const DEFAULT_EFFORT_DEC_THR = 0.3;
    const DEFAULT_EFFORT_MIN = 0.6;
    const DEFAULT_EFFORT_DEC = 0.005;
    const DEFAULT_EFFORT_INC_THR = 0.6;
    const DEFAULT_EFFORT_INC = 0.01;

    const DEFAULT_KICK_RAND = 0.0;
    const DEFAULT_TEAM_ACTUATOR_NOISE = false;
    const DEFAULT_PLAYER_RAND_FACTOR_L = 1.0;
    const DEFAULT_PLAYER_RAND_FACTOR_R = 1.0;
    const DEFAULT_KICK_RAND_FACTOR_L = 1.0;
    const DEFAULT_KICK_RAND_FACTOR_R = 1.0;

    const DEFAULT_BALL_SIZE = 0.085;
    const DEFAULT_BALL_DECAY = 0.94;
    const DEFAULT_BALL_RAND = 0.05;
    const DEFAULT_BALL_WEIGHT = 0.2;
    const DEFAULT_BALL_SPEED_MAX = 2.7;
    const DEFAULT_BALL_ACCEL_MAX = 2.7;

    const DEFAULT_DASH_POWER_RATE = 0.006;
    const DEFAULT_KICK_POWER_RATE = 0.027;
    const DEFAULT_KICKABLE_MARGIN = 0.7;
    const DEFAULT_CONTROL_RADIUS = 2.0;
//    const DEFAULT_CONTROL_RADIUS_WIDTH
// = DEFAULT_CONTROL_RADIUS - DEFAULT_PLAYER_SIZE;

    const DEFAULT_MAX_POWER = 100.0;
    const DEFAULT_MIN_POWER = -100.0;
    const DEFAULT_MAX_MOMENT = 180.0;
    const DEFAULT_MIN_MOMENT = -180.0;
    const DEFAULT_MAX_NECK_MOMENT = 180.0;
    const DEFAULT_MIN_NECK_MOMENT = -180.0;
    const DEFAULT_MAX_NECK_ANGLE = 90.0;
    const DEFAULT_MIN_NECK_ANGLE = -90.0;

    const DEFAULT_VISIBLE_ANGLE = 90.0;
    const DEFAULT_VISIBLE_DISTANCE = 3.0;

    const DEFAULT_WIND_DIR = 0.0;
    const DEFAULT_WIND_FORCE = 0.0;
    const DEFAULT_WIND_ANGLE = 0.0;
    const DEFAULT_WIND_RAND = 0.0;

//    const DEFAULT_KICKABLE_AREA
// = KICKABLE_MARGIN + PLAYER_SIZE + BALL_SIZE;

    const DEFAULT_CATCH_AREA_L = 2.0;
    const DEFAULT_CATCH_AREA_W = 1.0;
    const DEFAULT_CATCH_PROBABILITY = 1.0;
    const DEFAULT_GOALIE_MAX_MOVES = 2;

    const DEFAULT_CORNER_KICK_MARGIN = 1.0;
    const DEFAULT_OFFSIDE_ACTIVE_AREA_SIZE = 2.5;

    const DEFAULT_WIND_NONE = false;
    const DEFAULT_USE_WIND_RANDOM = false;

    const DEFAULT_COACH_SAY_COUNT_MAX = 128;
// defined as DEF_SAY_COACH_CNT_MAX
    const DEFAULT_COACH_SAY_MSG_SIZE = 128;

    const DEFAULT_CLANG_WIN_SIZE = 300;
    const DEFAULT_CLANG_DEFINE_WIN = 1;
    const DEFAULT_CLANG_META_WIN = 1;
    const DEFAULT_CLANG_ADVICE_WIN = 1;
    const DEFAULT_CLANG_INFO_WIN = 1;
    const DEFAULT_CLANG_MESS_DELAY = 50;
    const DEFAULT_CLANG_MESS_PER_CYCLE = 1;

    const DEFAULT_HALF_TIME = 300;
    const DEFAULT_SIMULATOR_STEP = 100;
    const DEFAULT_SEND_STEP = 150;
    const DEFAULT_RECV_STEP = 10;
    const DEFAULT_SENSE_BODY_STEP = 100;
//    const DEFAULT_LCM_STEP
// = lcm(sim_st, lcm(send_st, lcm(recv_st, lcm(sb_step, sv_st))))) of sync_offset;

    const DEFAULT_PLAYER_SAY_MSG_SIZE = 10;
    const DEFAULT_PLAYER_HEAR_MAX = 1;
    const DEFAULT_PLAYER_HEAR_INC = 1;
    const DEFAULT_PLAYER_HEAR_DECAY = 1;

    const DEFAULT_CATCH_BAN_CYCLE = 5;

    const DEFAULT_SLOW_DOWN_FACTOR = 1;

    const DEFAULT_USE_OFFSIDE = true;
    const DEFAULT_KICKOFF_OFFSIDE = true;
    const DEFAULT_OFFSIDE_KICK_MARGIN = 9.15;

    const DEFAULT_AUDIO_CUT_DIST = 50.0;

    const DEFAULT_DIST_QUANTIZE_STEP = 0.1;
    const DEFAULT_LANDMARK_DIST_QUANTIZE_STEP = 0.01;
    const DEFAULT_DIR_QUANTIZE_STEP = 0.1;
//    const DEFAULT_DIST_QUANTIZE_STEP_L;
//    const DEFAULT_DIST_QUANTIZE_STEP_R;
//    const DEFAULT_LANDMARK_DIST_QUANTIZE_STEP_L;
//    const DEFAULT_LANDMARK_DIST_QUANTIZE_STEP_R;
//    const DEFAULT_DIR_QUANTIZE_STEP_L;
//    const DEFAULT_DIR_QUANTIZE_STEP_R;

    const DEFAULT_COACH_MODE = false;
    const DEFAULT_COACH_WITH_REFEREE_MODE = false;
    const DEFAULT_USE_OLD_COACH_HEAR = false;

    const DEFAULT_SLOWNESS_ON_TOP_FOR_LEFT_TEAM = 1.0;
    const DEFAULT_SLOWNESS_ON_TOP_FOR_RIGHT_TEAM = 1.0;


    const DEFAULT_START_GOAL_L = 0;
    const DEFAULT_START_GOAL_R = 0;

    const DEFAULT_FULLSTATE_L = false;
    const DEFAULT_FULLSTATE_R = false;

    const DEFAULT_DROP_BALL_TIME = 200;

    const DEFAULT_SYNC_MODE = false;
    const DEFAULT_SYNC_OFFSET = 60;
    const DEFAULT_SYNC_MICRO_SLEEP = 1;

    const DEFAULT_POINT_TO_BAN = 5;
    const DEFAULT_POINT_TO_DURATION = 20;



    const DEFAULT_PLAYER_PORT = 6000;
    const DEFAULT_TRAINER_PORT = 6001;
    const DEFAULT_ONLINE_COACH_PORT = 6002;

    const DEFAULT_VERBOSE_MODE = false;

    const DEFAULT_COACH_SEND_VI_STEP = 100;

    const DEFAULT_REPLAY_FILE = ""; // unused after rcsserver-9+
    const DEFAULT_LANDMARK_FILE = "~/.rcssserver-landmark.xml";

    const DEFAULT_SEND_COMMS = false;

    const DEFAULT_TEXT_LOGGING = true;
    const DEFAULT_GAME_LOGGING = true;
    const DEFAULT_GAME_LOG_VERSION = 3;
    const DEFAULT_TEXT_LOG_DIR = "./";
    const DEFAULT_GAME_LOG_DIR = "./";
    const DEFAULT_TEXT_LOG_FIXED_NAME = "rcssserver";
    const DEFAULT_GAME_LOG_FIXED_NAME = "rcssserver";
    const DEFAULT_USE_TEXT_LOG_FIXED = false;
    const DEFAULT_USE_GAME_LOG_FIXED = false;
    const DEFAULT_USE_TEXT_LOG_DATED = true;
    const DEFAULT_USE_GAME_LOG_DATED = true;
    const DEFAULT_LOG_DATE_FORMAT = "%Y%m%d%H%M-";
    const DEFAULT_LOG_TIMES = false;
    const DEFAULT_RECORD_MESSAGES = false;
    const DEFAULT_TEXT_LOG_COMPRESSION = 0;
    const DEFAULT_GAME_LOG_COMPRESSION = 0;

    const DEFAULT_USE_PROFILE = false;

    const DEFAULT_TACKLE_DIST = 2.0;
    const DEFAULT_TACKLE_BACK_DIST = 0.5;
    const DEFAULT_TACKLE_WIDTH = 1.0;
    const DEFAULT_TACKLE_EXPONENT = 6.0;
    const DEFAULT_TACKLE_CYCLES = 10;
    const DEFAULT_TACKLE_POWER_RATE = 0.027;

    const DEFAULT_FREEFORM_WAIT_PERIOD = 600;
    const DEFAULT_FREEFORM_SEND_PERIOD = 20;

    const DEFAULT_FREE_KICK_FAULTS = true;
    const DEFAULT_BACK_PASSES = true;

    const DEFAULT_PROPER_GOAL_KICKS = false;
    const DEFAULT_STOPPED_BALL_VEL = 0.01;
    const DEFAULT_MAX_GOAL_KICKS = 3;

    const DEFAULT_CLANG_DEL_WIN = 1;
    const DEFAULT_CLANG_RULE_WIN = 1;

    const DEFAULT_AUTO_MODE = false;
    const DEFAULT_KICK_OFF_WAIT = 100;
    const DEFAULT_CONNECT_WAIT = 300;
    const DEFAULT_GAME_OVER_WAIT = 100;
    const DEFAULT_TEAM_L_START = "";
    const DEFAULT_TEAM_R_START = "";


    const DEFAULT_KEEPAWAY_MODE = false;
// these value are defined in rcssserver/param.h
    const DEFAULT_KEEPAWAY_LENGTH = 20.0;
    const DEFAULT_KEEPAWAY_WIDTH = 20.0;

    const DEFAULT_KEEPAWAY_LOGGING = true;
    const DEFAULT_KEEPAWAY_LOG_DIR = "./";
    const DEFAULT_KEEPAWAY_LOG_FIXED_NAME = "rcssserver";
    const DEFAULT_KEEPAWAY_LOG_FIXED = false;
    const DEFAULT_KEEPAWAY_LOG_DATED = true;

    const DEFAULT_KEEPAWAY_START = -1;

    const DEFAULT_NR_NORMAL_HALFS = 2;
    const DEFAULT_NR_EXTRA_HALFS = 2;
    const DEFAULT_PENALTY_SHOOT_OUTS = true;

    const DEFAULT_PEN_BEFORE_SETUP_WAIT = 30;
    const DEFAULT_PEN_SETUP_WAIT = 100;
    const DEFAULT_PEN_READY_WAIT = 50;
    const DEFAULT_PEN_TAKEN_WAIT = 200;
    const DEFAULT_PEN_NR_KICKS = 5;
    const DEFAULT_PEN_MAX_EXTRA_KICKS = 10;
    const DEFAULT_PEN_DIST_X = 42.5;
    const DEFAULT_PEN_RANDOM_WINNER = false;
    const DEFAULT_PEN_ALLOW_MULT_KICKS = true;
    const DEFAULT_PEN_MAX_GOALIE_DIST_X = 14.0;
    const DEFAULT_PEN_COACH_MOVES_PLAYERS = true;

    const DEFAULT_MODULE_DIR = "";

    const DEFAULT_BALL_STUCK_AREA = 3.0;

    const DEFAULT_MAX_TACKLE_POWER = 100.0;
    const DEFAULT_MAX_BACK_TACKLE_POWER = 50.0;
    const DEFAULT_PLAYER_SPEED_MAX_MIN = 0.8;
    const DEFAULT_EXTRA_STAMINA = 0.0;
    const DEFAULT_SYNCH_SEE_OFFSET = 30;

    const EXTRA_HALF_TIME = 100;

    const STAMINA_CAPACITY = -1.0; //148600.0;
    const MAX_DASH_ANGLE = 0.0; //180.0;
    const MIN_DASH_ANGLE = 0.0; //-180.0;
    const DASH_ANGLE_STEP = 180.0; // 90.0
    const SIDE_DASH_RATE = 0.25;
    const BACK_DASH_RATE = 0.5;
    const MAX_DASH_POWER = 100.0;
    const MIN_DASH_POWER = -100.0;

// 14.0.0
    const TACKLE_RAND_FACTOR = 2.0;
    const FOUL_DETECT_PROBABILITY = 0.5;
    const FOUL_EXPONENT = 10.0;
    const FOUL_CYCLES = 5;
    public static $params = array();
    static function addParam(Param $param)
    {
        self::$params[$param->getName()] = $param->getValue();
        switch ($param->getName()) {
            case 'half_time' :
                self::$params['actual_half_time'] = $param->getValue() * 10;
                break;
            case 'extra_half_time' :
                self::$params['actual_extra_half_time'] = $param->getValue() * 10;
                break;
            case 'pitch_length' :
                self::$params['pitch_half_length'] = $param->getValue() / 2;
                self::$params['our_team_goal_line'] = -$param->getValue() / 2;
                self::$params['their_team_goal_line'] = $param->getValue() / 2;
                self::$params['our_team_goal_pos'] = new Util\Vector(self::$params['our_team_goal_line'], 0);
                self::$params['their_team_goal_pos'] = new Util\Vector(self::$params['their_team_goal_line'], 0);
                self::$params['our_penalty_area_line_x'] = self::$params['our_team_goal_line']
                        + self::$params['penalty_area_length'];
                self::$params['their_penalty_area_line_x'] = self::$params['their_team_goal_line']
                        + self::$params['penalty_area_length'];
                break;
            case 'pitch_width' :
                self::$params['pitch_half_width'] = $param->getValue() / 2;
                break;
            case 'penalty_area_length' :
                self::$params['penalty_area_half_length'] = $param->getValue() / 2;
                self::$params['our_penalty_area_line_x'] = self::$params['our_team_goal_line']
                        + self::$params['penalty_area_length'];
                self::$params['their_penalty_area_line_x'] = self::$params['their_team_goal_line']
                        + self::$params['penalty_area_length'];
                break;
            case 'penalty_area_width' :
                self::$params['penalty_area_half_width'] = $param->getValue() / 2;
                break;
            case 'goal_area_length' :
                self::$params['goal_area_half_length'] = $param->getValue() / 2;
                break;
            case 'goal_area_width' :
                self::$params['goal_area_half_width'] = $param->getValue() / 2;
                break;
        }
    }

    function __construct()
    {
        static $init = 0;
        if ($init++) return;
        self::$params['max_player'] = self::DEFAULT_MAX_PLAYER;
        self::$params['pitch_width'] = self::DEFAULT_PITCH_WIDTH;
        self::$params['pitch_half_width'] = self::DEFAULT_PITCH_WIDTH / 2;
        self::$params['pitch_length'] = self::DEFAULT_PITCH_LENGTH;
        self::$params['pitch_margin'] = self::DEFAULT_PITCH_MARGIN;
        self::$params['center_circle_r'] = self::DEFAULT_CENTER_CIRCLE_R;
        self::$params['penalty_area_length'] = self::DEFAULT_PENALTY_AREA_LENGTH;
        self::$params['penalty_area_half_length'] = self::DEFAULT_PENALTY_AREA_LENGTH / 2;
        self::$params['penalty_area_width'] = self::DEFAULT_PENALTY_AREA_WIDTH;
        self::$params['penalty_area_half_width'] = self::DEFAULT_PENALTY_AREA_WIDTH / 2;
        self::$params['goal_area_length'] = self::DEFAULT_GOAL_AREA_LENGTH;
        self::$params['goal_area_half_length'] = self::DEFAULT_GOAL_AREA_LENGTH / 2;
        self::$params['goal_area_width'] = self::DEFAULT_GOAL_AREA_WIDTH;
        self::$params['goal_area_half_width'] = self::DEFAULT_GOAL_AREA_WIDTH / 2;
        self::$params['goal_depth'] = self::DEFAULT_GOAL_DEPTH;
        self::$params['penalty_circle_r'] = self::DEFAULT_PENALTY_CIRCLE_R;
        self::$params['penalty_spot_dist'] = self::DEFAULT_PENALTY_SPOT_DIST;
        self::$params['corner_arc_r'] = self::DEFAULT_CORNER_ARC_R;
        self::$params['kickoff_clear_distance'] = self::DEFAULT_CENTER_CIRCLE_R;
        self::$params['wind_weight'] = self::DEFAULT_WIND_WEIGHT;
        self::$params['goal_post_radius'] = self::DEFAULT_GOAL_POST_RADIUS;
        self::$params['pitch_half_length'] = self::$params['pitch_length'] / 2;
        self::$params['our_team_goal_line'] = -self::$params['pitch_length'] / 2;
        self::$params['their_team_goal_line'] = self::$params['pitch_length'] / 2;
        self::$params['our_team_goal_pos'] = new Util\Vector(self::$params['our_team_goal_line'], 0);
        self::$params['their_team_goal_pos'] = new Util\Vector(self::$params['their_team_goal_line'], 0);
        self::$params['our_penalty_area_line_x'] = self::$params['our_team_goal_line']
                + self::$params['penalty_area_length'];
        self::$params['their_penalty_area_line_x'] = self::$params['their_team_goal_line']
                + self::$params['penalty_area_length'];
        self::$params['penalty_area_half_length'] = DEFAULT_PENALTY_AREA_LENGTH / 2;
        self::$params['our_penalty_area_line_x'] = self::$params['our_team_goal_line']
                + self::$params['penalty_area_length'];
        self::$params['their_penalty_area_line_x'] = self::$params['their_team_goal_line']
                + self::$params['penalty_area_length'];

        self::$params['goal_width'] = self::DEFAULT_GOAL_WIDTH;
        self::$params['inertia_moment'] = self::DEFAULT_INERTIA_MOMENT;
    
        self::$params['player_size'] = self::DEFAULT_PLAYER_SIZE;
        self::$params['player_decay'] = self::DEFAULT_PLAYER_DECAY;
        self::$params['player_rand'] = self::DEFAULT_PLAYER_RAND;
        self::$params['player_weight'] = self::DEFAULT_PLAYER_WEIGHT;
        self::$params['player_speed_max'] = self::DEFAULT_PLAYER_SPEED_MAX;
        self::$params['player_accel_max'] = self::DEFAULT_PLAYER_ACCEL_MAX;
    
        self::$params['stamina_max'] = self::DEFAULT_STAMINA_MAX;
        self::$params['stamina_inc_max'] = self::DEFAULT_STAMINA_INC_MAX;
    
        self::$params['recover_init'] = self::DEFAULT_RECOVER_INIT;
        self::$params['recover_dec_thr'] = self::DEFAULT_RECOVER_DEC_THR;
        self::$params['recover_min'] = self::DEFAULT_RECOVER_MIN;
        self::$params['recover_dec'] = self::DEFAULT_RECOVER_DEC;
    
        self::$params['effort_init'] = self::DEFAULT_EFFORT_INIT;
        self::$params['effort_dec_thr'] = self::DEFAULT_EFFORT_DEC_THR;
        self::$params['effort_min'] = self::DEFAULT_EFFORT_MIN;
        self::$params['effort_dec'] = self::DEFAULT_EFFORT_DEC;
        self::$params['effort_inc_thr'] = self::DEFAULT_EFFORT_INC_THR;
        self::$params['effort_inc'] = self::DEFAULT_EFFORT_INC;
    
        self::$params['kick_rand'] = self::DEFAULT_KICK_RAND;
        self::$params['team_actuator_noise'] = self::DEFAULT_TEAM_ACTUATOR_NOISE;
        self::$params['player_rand_factor_l'] = self::DEFAULT_PLAYER_RAND_FACTOR_L;
        self::$params['player_rand_factor_r'] = self::DEFAULT_PLAYER_RAND_FACTOR_R;
        self::$params['kick_rand_factor_l'] = self::DEFAULT_KICK_RAND_FACTOR_L;
        self::$params['kick_rand_factor_r'] = self::DEFAULT_KICK_RAND_FACTOR_R;
    
        self::$params['ball_size'] = self::DEFAULT_BALL_SIZE;
        self::$params['ball_decay'] = self::DEFAULT_BALL_DECAY;
        self::$params['ball_rand'] = self::DEFAULT_BALL_RAND;
        self::$params['ball_weight'] = self::DEFAULT_BALL_WEIGHT;
        self::$params['ball_speed_max'] = self::DEFAULT_BALL_SPEED_MAX;
        self::$params['ball_accel_max'] = self::DEFAULT_BALL_ACCEL_MAX;
    
        self::$params['dash_power_rate'] = self::DEFAULT_DASH_POWER_RATE;
        self::$params['kick_power_rate'] = self::DEFAULT_KICK_POWER_RATE;
        self::$params['kickable_margin'] = self::DEFAULT_KICKABLE_MARGIN;
        self::$params['control_radius'] = self::DEFAULT_CONTROL_RADIUS;
        self::$params['control_radius_width'] = self::DEFAULT_CONTROL_RADIUS - DEFAULT_PLAYER_SIZE;
    
        self::$params['max_power'] = self::DEFAULT_MAX_POWER;
        self::$params['min_power'] = self::DEFAULT_MIN_POWER;
        self::$params['max_moment'] = self::DEFAULT_MAX_MOMENT;
        self::$params['min_moment'] = self::DEFAULT_MIN_MOMENT;
        self::$params['max_neck_moment'] = self::DEFAULT_MAX_NECK_MOMENT;
        self::$params['min_neck_moment'] = self::DEFAULT_MIN_NECK_MOMENT;
        self::$params['max_neck_angle'] = self::DEFAULT_MAX_NECK_ANGLE;
        self::$params['min_neck_angle'] = self::DEFAULT_MIN_NECK_ANGLE;
    
        self::$params['visible_angle'] = self::DEFAULT_VISIBLE_ANGLE;
        self::$params['visible_distance'] = self::DEFAULT_VISIBLE_DISTANCE;
    
        self::$params['wind_dir'] = self::DEFAULT_WIND_DIR;
        self::$params['wind_force'] = self::DEFAULT_WIND_FORCE;
        self::$params['wind_angle'] = self::DEFAULT_WIND_ANGLE;
        self::$params['wind_rand'] = self::DEFAULT_WIND_RAND;
    
        self::$params['kickable_area'] = self::DEFAULT_PLAYER_SIZE + DEFAULT_KICKABLE_MARGIN + DEFAULT_BALL_SIZE;
    
        self::$params['catch_area_l'] = self::DEFAULT_CATCH_AREA_L;
        self::$params['catch_area_w'] = self::DEFAULT_CATCH_AREA_W;
        self::$params['catch_probability'] = self::DEFAULT_CATCH_PROBABILITY;
        self::$params['goalie_max_moves'] = self::DEFAULT_GOALIE_MAX_MOVES;
    
        self::$params['corner_kick_margin'] = self::DEFAULT_CORNER_KICK_MARGIN;
        self::$params['offside_active_area_size'] = self::DEFAULT_OFFSIDE_ACTIVE_AREA_SIZE;
    
        self::$params['wind_none'] = self::DEFAULT_WIND_NONE;
        self::$params['use_wind_random'] = self::DEFAULT_USE_WIND_RANDOM;
    
        self::$params['coach_say_count_max'] = self::DEFAULT_COACH_SAY_COUNT_MAX;
        self::$params['coach_say_msg_size'] = self::DEFAULT_COACH_SAY_MSG_SIZE;
    
        self::$params['clang_win_size'] = self::DEFAULT_CLANG_WIN_SIZE;
        self::$params['clang_define_win'] = self::DEFAULT_CLANG_DEFINE_WIN;
        self::$params['clang_meta_win'] = self::DEFAULT_CLANG_META_WIN;
        self::$params['clang_advice_win'] = self::DEFAULT_CLANG_ADVICE_WIN;
        self::$params['clang_info_win'] = self::DEFAULT_CLANG_INFO_WIN;
        self::$params['clang_mess_delay'] = self::DEFAULT_CLANG_MESS_DELAY;
        self::$params['clang_mess_per_cycle'] = self::DEFAULT_CLANG_MESS_PER_CYCLE;
    
        self::$params['half_time'] = self::DEFAULT_HALF_TIME;
        self::$params['simulator_step'] = self::DEFAULT_SIMULATOR_STEP;
        self::$params['send_step'] = self::DEFAULT_SEND_STEP;
        self::$params['recv_step'] = self::DEFAULT_RECV_STEP;
        self::$params['sense_body_step'] = self::DEFAULT_SENSE_BODY_STEP;
        self::$params['lcm_step'] = 300; //lcm(simulator_step, send_step, recv_step, sense_body_step, send_vi_step);
    
        self::$params['player_say_msg_size'] = self::DEFAULT_PLAYER_SAY_MSG_SIZE;
        self::$params['player_hear_max'] = self::DEFAULT_PLAYER_HEAR_MAX;
        self::$params['player_hear_inc'] = self::DEFAULT_PLAYER_HEAR_INC;
        self::$params['player_hear_decay'] = self::DEFAULT_PLAYER_HEAR_DECAY;
    
        self::$params['catch_ban_cycle'] = self::DEFAULT_CATCH_BAN_CYCLE;
    
        self::$params['slow_down_factor'] = self::DEFAULT_SLOW_DOWN_FACTOR;
    
        self::$params['use_offside'] = self::DEFAULT_USE_OFFSIDE;
        self::$params['kickoff_offside'] = self::DEFAULT_KICKOFF_OFFSIDE;
        self::$params['offside_kick_margin'] = self::DEFAULT_OFFSIDE_KICK_MARGIN;
    
        self::$params['audio_cut_dist'] = self::DEFAULT_AUDIO_CUT_DIST;
    
        self::$params['dist_quantize_step'] = self::DEFAULT_DIST_QUANTIZE_STEP;
        self::$params['landmark_dist_quantize_step'] = self::DEFAULT_LANDMARK_DIST_QUANTIZE_STEP;
        self::$params['dir_quantize_step'] = self::DEFAULT_DIR_QUANTIZE_STEP;
        self::$params['dist_quantize_step_l'] = self::DEFAULT_DIST_QUANTIZE_STEP;
        self::$params['dist_quantize_step_r'] = self::DEFAULT_DIST_QUANTIZE_STEP;
        self::$params['landmark_dist_quantize_step_l'] = self::DEFAULT_LANDMARK_DIST_QUANTIZE_STEP;
        self::$params['landmark_dist_quantize_step_r'] = self::DEFAULT_LANDMARK_DIST_QUANTIZE_STEP;
        self::$params['dir_quantize_step_l'] = self::DEFAULT_DIR_QUANTIZE_STEP;
        self::$params['dir_quantize_step_r'] = self::DEFAULT_DIR_QUANTIZE_STEP;
    
        self::$params['coach_mode'] = self::DEFAULT_COACH_MODE;
        self::$params['coach_with_referee_mode'] = self::DEFAULT_COACH_WITH_REFEREE_MODE;
        self::$params['use_old_coach_hear'] = self::DEFAULT_USE_OLD_COACH_HEAR;
    
        self::$params['slowness_on_top_for_left_team'] = self::DEFAULT_SLOWNESS_ON_TOP_FOR_LEFT_TEAM;
        self::$params['slowness_on_top_for_right_team'] = self::DEFAULT_SLOWNESS_ON_TOP_FOR_RIGHT_TEAM;
    
        self::$params['start_goal_l'] = self::DEFAULT_START_GOAL_L;
        self::$params['start_goal_r'] = self::DEFAULT_START_GOAL_R;
    
        self::$params['fullstate_l'] = self::DEFAULT_FULLSTATE_L;
        self::$params['fullstate_r'] = self::DEFAULT_FULLSTATE_R;
    
        self::$params['drop_ball_time'] = self::DEFAULT_DROP_BALL_TIME;
    
        self::$params['synch_mode'] = self::DEFAULT_SYNC_MODE;
        self::$params['synch_offset'] = self::DEFAULT_SYNC_OFFSET;
        self::$params['synch_micro_sleep'] = self::DEFAULT_SYNC_MICRO_SLEEP;
    
        self::$params['point_to_ban'] = self::DEFAULT_POINT_TO_BAN;
        self::$params['point_to_duration'] = self::DEFAULT_POINT_TO_DURATION;
    
        // not defined in server_param_t
        self::$params['player_port'] = self::DEFAULT_PLAYER_PORT;
        self::$params['trainer_port'] = self::DEFAULT_TRAINER_PORT;
        self::$params['online_coach_port'] = self::DEFAULT_ONLINE_COACH_PORT;
    
        self::$params['verbose_mode'] = self::DEFAULT_VERBOSE_MODE;
    
        self::$params['coach_send_vi_step'] = self::DEFAULT_COACH_SEND_VI_STEP;
    
        self::$params['replay_file'] = self::DEFAULT_REPLAY_FILE;
        self::$params['landmark_file'] = self::DEFAULT_LANDMARK_FILE;
    
        self::$params['send_comms'] = self::DEFAULT_SEND_COMMS;
    
        self::$params['text_logging'] = self::DEFAULT_TEXT_LOGGING;
        self::$params['game_logging'] = self::DEFAULT_GAME_LOGGING;
        self::$params['game_log_version'] = self::DEFAULT_GAME_LOG_VERSION;
        self::$params['text_log_dir'] = self::DEFAULT_TEXT_LOG_DIR;
        self::$params['game_log_dir'] = self::DEFAULT_GAME_LOG_DIR;
        self::$params['text_log_fixed_name'] = self::DEFAULT_TEXT_LOG_FIXED_NAME;
        self::$params['game_log_fixed_name'] = self::DEFAULT_GAME_LOG_FIXED_NAME;
        self::$params['use_text_log_fixed'] = self::DEFAULT_USE_TEXT_LOG_FIXED;
        self::$params['use_game_log_fixed'] = self::DEFAULT_USE_GAME_LOG_FIXED;
        self::$params['use_text_log_dated'] = self::DEFAULT_USE_TEXT_LOG_DATED;
        self::$params['use_game_log_dated'] = self::DEFAULT_USE_GAME_LOG_DATED;
        self::$params['log_date_format'] = self::DEFAULT_LOG_DATE_FORMAT;
        self::$params['log_times'] = self::DEFAULT_LOG_TIMES;
        self::$params['record_message'] = self::DEFAULT_RECORD_MESSAGES;
        self::$params['text_log_compression'] = self::DEFAULT_TEXT_LOG_COMPRESSION;
        self::$params['game_log_compression'] = self::DEFAULT_GAME_LOG_COMPRESSION;
    
        self::$params['use_profile'] = self::DEFAULT_USE_PROFILE;
    
        self::$params['tackle_dist'] = self::DEFAULT_TACKLE_DIST;
        self::$params['tackle_back_dist'] = self::DEFAULT_TACKLE_BACK_DIST;
        self::$params['tackle_width'] = self::DEFAULT_TACKLE_WIDTH;
        self::$params['tackle_exponent'] = self::DEFAULT_TACKLE_EXPONENT;
        self::$params['tackle_cycles'] = self::DEFAULT_TACKLE_CYCLES;
        self::$params['tackle_power_rate'] = self::DEFAULT_TACKLE_POWER_RATE;
    
        self::$params['freeform_wait_period'] = self::DEFAULT_FREEFORM_WAIT_PERIOD;
        self::$params['freeform_send_period'] = self::DEFAULT_FREEFORM_SEND_PERIOD;
    
        self::$params['free_kick_faults'] = self::DEFAULT_FREE_KICK_FAULTS;
        self::$params['back_passes'] = self::DEFAULT_BACK_PASSES;
    
        self::$params['proper_goal_kicks'] = self::DEFAULT_PROPER_GOAL_KICKS;
        self::$params['stopped_ball_vel'] = self::DEFAULT_STOPPED_BALL_VEL;
        self::$params['max_goal_kicks'] = self::DEFAULT_MAX_GOAL_KICKS;
    
        self::$params['clang_del_win']  = DEFAULT_CLANG_DEL_WIN;
        self::$params['clang_rule_win'] = self::DEFAULT_CLANG_RULE_WIN;
    
        self::$params['auto_mode'] = self::DEFAULT_AUTO_MODE;
        self::$params['kick_off_wait'] = self::DEFAULT_KICK_OFF_WAIT;
        self::$params['connect_wait'] = self::DEFAULT_CONNECT_WAIT;
        self::$params['game_over_wait'] = self::DEFAULT_GAME_OVER_WAIT;
        self::$params['team_l_start'] = self::DEFAULT_TEAM_L_START;
        self::$params['team_r_start'] = self::DEFAULT_TEAM_R_START;
    
        self::$params['keepaway_mode'] = self::DEFAULT_KEEPAWAY_MODE;
        self::$params['keepaway_length'] = self::DEFAULT_KEEPAWAY_LENGTH;
        self::$params['keepaway_width'] = self::DEFAULT_KEEPAWAY_WIDTH;
    
        self::$params['keepaway_logging'] = self::DEFAULT_KEEPAWAY_LOGGING;
        self::$params['keepaway_log_dir'] = self::DEFAULT_KEEPAWAY_LOG_DIR;
        self::$params['keepaway_log_fixed_name'] = self::DEFAULT_KEEPAWAY_LOG_FIXED_NAME;
        self::$params['keepaway_log_fixed'] = self::DEFAULT_KEEPAWAY_LOG_FIXED;
        self::$params['keepaway_log_dated'] = self::DEFAULT_KEEPAWAY_LOG_DATED;
    
        self::$params['keepaway_start'] = self::DEFAULT_KEEPAWAY_START;
    
        self::$params['nr_normal_halfs'] = self::DEFAULT_NR_NORMAL_HALFS;
        self::$params['nr_extra_halfs'] = self::DEFAULT_NR_EXTRA_HALFS;
        self::$params['penalty_shoot_outs'] = self::DEFAULT_PENALTY_SHOOT_OUTS;
    
        self::$params['pen_before_setup_wait'] = self::DEFAULT_PEN_BEFORE_SETUP_WAIT;
        self::$params['pen_setup_wait'] = self::DEFAULT_PEN_SETUP_WAIT;
        self::$params['pen_ready_wait'] = self::DEFAULT_PEN_READY_WAIT;
        self::$params['pen_taken_wait'] = self::DEFAULT_PEN_TAKEN_WAIT;
        self::$params['pen_nr_kicks'] = self::DEFAULT_PEN_NR_KICKS;
        self::$params['pen_max_extra_kicks'] = self::DEFAULT_PEN_MAX_EXTRA_KICKS;
        self::$params['pen_dist_x'] = self::DEFAULT_PEN_DIST_X;
        self::$params['pen_random_winner'] = self::DEFAULT_PEN_RANDOM_WINNER;
        self::$params['pen_allow_mult_kicks'] = self::DEFAULT_PEN_ALLOW_MULT_KICKS;
        self::$params['pen_max_goalie_dist_x'] = self::DEFAULT_PEN_MAX_GOALIE_DIST_X;
        self::$params['pen_coach_moves_players'] = self::DEFAULT_PEN_COACH_MOVES_PLAYERS;
    
        self::$params['module_dir'] = self::DEFAULT_MODULE_DIR;
    
        // 11.0.0
        self::$params['ball_stuck_area'] = self::DEFAULT_BALL_STUCK_AREA;
        // self::$params['coach_msg_file'] = "";
    
        // 12.0.0
        self::$params['max_tackle_power'] = self::DEFAULT_MAX_TACKLE_POWER;
        self::$params['max_back_tackle_power'] = self::DEFAULT_MAX_BACK_TACKLE_POWER;
        self::$params['player_speed_max_min'] = self::DEFAULT_PLAYER_SPEED_MAX_MIN;
        self::$params['extra_stamina'] = self::DEFAULT_EXTRA_STAMINA;
        self::$params['synch_see_offset'] = self::DEFAULT_SYNCH_SEE_OFFSET;
    
        self::$params['max_monitors'] = -1;
    
        // 12.1.3
        self::$params['extra_half_time'] = self::EXTRA_HALF_TIME;
    
        // 13.0.0
        self::$params['stamina_capacity'] = self::STAMINA_CAPACITY;
        self::$params['max_dash_angle'] = self::MAX_DASH_ANGLE;
        self::$params['min_dash_angle'] = self::MIN_DASH_ANGLE;
        self::$params['dash_angle_step'] = self::DASH_ANGLE_STEP;
        self::$params['side_dash_rate'] = self::SIDE_DASH_RATE;
        self::$params['back_dash_rate'] = self::BACK_DASH_RATE;
        self::$params['max_dash_power'] = self::MAX_DASH_POWER;
        self::$params['min_dash_power'] = self::MIN_DASH_POWER;
    
        // 14.0.0
        self::$params['tackle_rand_factor'] = self::TACKLE_RAND_FACTOR;
        self::$params['foul_detect_probability'] = self::FOUL_DETECT_PROBABILITY;
        self::$params['foul_exponent'] = self::FOUL_EXPONENT;
        self::$params['foul_cycles'] = self::FOUL_CYCLES;
        self::$params['random_seed'] = -1;
        self::$params['golden_goal'] = true;
    
        self::$params['kickable_area'] = self::$params['kickable_margin'] + self::$params['ball_size'] + self::$params['player_size'];
        self::$params['catchable_area'] = sqrt( pow( self::$params['catch_area_width'] * 0.5, 2 )
                                      + pow( self::$params['catch_area_length'], 2 ) );
        self::$params['control_radius_width'] = self::$params['control_radius'] - self::$params['player_size'];

        // real speed max
        $accel_max = self::$params['max_dash_power'] * self::$params['default_dash_power_rate'] * self::$params['default_effort_max'];
        self::$params['real_speed_max'] = $accel_max / ( 1.0 - self::$params['default_player_decay']);  // sum inf geom series
        if ( self::$params['real_speed_max'] > self::$params['default_player_speed_max'] )
        {
            self::$params['real_speed_max'] = self::$params['default_player_speed_max'];
        }
    }
}
