<?php

/*
 Copyright (C) Gregory Beaver 2013
 Based on librsc, Copyright (C) Hidehisa AKIYAMA

 This code is free software; you can redistribute it and/or
 modify it under the terms of the GNU Lesser General Public
 License as published by the Free Software Foundation; either
 version 3 of the License, or (at your option) any later version.

 This library is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 Lesser General Public License for more details.

 You should have received a copy of the GNU Lesser General Public
 License along with this library; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

 */

namespace ThroughBall\Util;
use ThroughBall\ServerParams as ServerParam;

class ObjectTable
{
    const SERVER_EPS = 1.0e-10;
    protected $landmarks = array();
    protected $staticitems = array();
    protected $dynamicitems = array();

    function __construct()
    {
        $this->createLandmarkMap();
        $this->createTable();
        $this->cmp = function($a, $b) {return $a[0] == $b[0] ? 0 : ($a[0] < $b[0] ? -1 : 1);};
    }

    function createLandmarkMap()
    {
        ///////////////////////////////////////////////////////////////////////
        $pitch_half_w   = ServerParam::$params['pitch_half_width'];
        $pitch_half_l   = ServerParam::$params['pitch_half_length'];
        $penalty_l      = ServerParam::$params['penalty_area_length'];
        $penalty_half_w = ServerParam::$params['penalty_area_half_width'];
        $goal_half_w    = ServerParam::$params['goal_half_width'];
        ///////////////////////////////////////////////////////////////////////
    
        self::$landmarks['(g l)']    = new Vector( -$pitch_half_l,   0.0 );
        self::$landmarks['(g r)']    = new Vector( +$pitch_half_l,   0.0 );
    
        self::$landmarks['(f c)']    = new Vector(           0.0,           0.0 );
        self::$landmarks['(f c t)']   = new Vector(           0.0, -$pitch_half_w );
        self::$landmarks['(f c b)']   = new Vector(           0.0, +$pitch_half_w );
        self::$landmarks['(f l t)']   = new Vector( -$pitch_half_l, -$pitch_half_w );
        self::$landmarks['(f l b)']   = new Vector( -$pitch_half_l, +$pitch_half_w );
        self::$landmarks['(f r t)']   = new Vector( +$pitch_half_l, -$pitch_half_w );
        self::$landmarks['(f r b)']   = new Vector( +$pitch_half_l, +$pitch_half_w );
    
        self::$landmarks['(f p l t)']  = new Vector( -($pitch_half_l - $penalty_l), -$penalty_half_w );
        self::$landmarks['(f p l c)']  = new Vector( -($pitch_half_l - $penalty_l),             0.0 );
        self::$landmarks['(f p l b)']  = new Vector( -($pitch_half_l - $penalty_l), +$penalty_half_w );
        self::$landmarks['(f p r t)']  = new Vector( +($pitch_half_l - $penalty_l), -$penalty_half_w );
        self::$landmarks['(f p r c)']  = new Vector( +($pitch_half_l - $penalty_l),             0.0 );
        self::$landmarks['(f p r b)']  = new Vector( +($pitch_half_l - $penalty_l), +$penalty_half_w );
    
        self::$landmarks['(f g l t)']  = new Vector( -$pitch_half_l, -$goal_half_w );
        self::$landmarks['(f g l b )']  = new Vector( -$pitch_half_l, +$goal_half_w );
        self::$landmarks['(f g r t)']  = new Vector( +$pitch_half_l, -$goal_half_w );
        self::$landmarks['(f g r b)']  = new Vector( +$pitch_half_l, +$goal_half_w );
    
        self::$landmarks['(f t l 50)'] = new Vector( -50.0, -$pitch_half_w - 5.0 );
        self::$landmarks['(f t l 40)'] = new Vector( -40.0, -$pitch_half_w - 5.0 );
        self::$landmarks['(f t l 30)'] = new Vector( -30.0, -$pitch_half_w - 5.0 );
        self::$landmarks['(f t l 20)'] = new Vector( -20.0, -$pitch_half_w - 5.0 );
        self::$landmarks['(f t l 10)'] = new Vector( -10.0, -$pitch_half_w - 5.0 );
    
        self::$landmarks['(f t 0)']   = new Vector(  0.0, -$pitch_half_w - 5.0 );
    
        self::$landmarks['(f t l 10)'] = new Vector( +10.0, -$pitch_half_w - 5.0 );
        self::$landmarks['(f t l 20)'] = new Vector( +20.0, -$pitch_half_w - 5.0 );
        self::$landmarks['(f t l 30)'] = new Vector( +30.0, -$pitch_half_w - 5.0 );
        self::$landmarks['(f t l 40)'] = new Vector( +40.0, -$pitch_half_w - 5.0 );
        self::$landmarks['(f t l 50)'] = new Vector( +50.0, -$pitch_half_w - 5.0 );
    
        self::$landmarks['(f b l 50)'] = new Vector( -50.0,  $pitch_half_w + 5.0 );
        self::$landmarks['(f b l 40)'] = new Vector( -40.0,  $pitch_half_w + 5.0 );
        self::$landmarks['(f b l 30)'] = new Vector( -30.0,  $pitch_half_w + 5.0 );
        self::$landmarks['(f b l 20)'] = new Vector( -20.0,  $pitch_half_w + 5.0 );
        self::$landmarks['(f b l 10)'] = new Vector( -10.0,  $pitch_half_w + 5.0 );
    
        self::$landmarks['(f b 0)']   = new Vector(   0.0,  $pitch_half_w + 5.0);
    
        self::$landmarks['(f b r 10)'] = new Vector( +10.0,  $pitch_half_w + 5.0 );
        self::$landmarks['(f b r 20)'] = new Vector( +20.0,  $pitch_half_w + 5.0 );
        self::$landmarks['(f b r 30)'] = new Vector( +30.0,  $pitch_half_w + 5.0 );
        self::$landmarks['(f b r 40)'] = new Vector( +40.0,  $pitch_half_w + 5.0 );
        self::$landmarks['(f b r 50)'] = new Vector( +50.0,  $pitch_half_w + 5.0 );
    
        self::$landmarks['(f l t 30)'] = new Vector( -$pitch_half_l - 5.0, -30.0 );
        self::$landmarks['(f l t 20)'] = new Vector( -$pitch_half_l - 5.0, -20.0 );
        self::$landmarks['(f l t 10)'] = new Vector( -$pitch_half_l - 5.0, -10.0 );
    
        self::$landmarks['(f l 0)']   = new Vector( -$pitch_half_l - 5.0,   0.0 );
    
        self::$landmarks['(f l b 10)'] = new Vector( -$pitch_half_l - 5.0,  10.0 );
        self::$landmarks['(f l b 20)'] = new Vector( -$pitch_half_l - 5.0,  20.0 );
        self::$landmarks['(f l b 30)'] = new Vector( -$pitch_half_l - 5.0,  30.0 );
    
        self::$landmarks['(f r t 30)'] = new Vector( +$pitch_half_l + 5.0, -30.0 );
        self::$landmarks['(f r t 20)'] = new Vector( +$pitch_half_l + 5.0, -20.0 );
        self::$landmarks['(f r t 10)'] = new Vector( +$pitch_half_l + 5.0, -10.0 );
    
        self::$landmarks['(f r 0)']   = new Vector( +$pitch_half_l + 5.0,   0.0 );
    
        self::$landmarks['(f r b 10)'] = new Vector( +$pitch_half_l + 5.0,  10.0 );
        self::$landmarks['(f r b 20)'] = new Vector( +$pitch_half_l + 5.0,  20.0 );
        self::$landmarks['(f r b 30)'] = new Vector( +$pitch_half_l + 5.0,  30.0 );
    }
    
    function createTable($static_qstep = null, $movable_qstep = null)
    {
        if (null !== $static_qstep && null !== $movable_qstep) {
            $this->createTable2( $static_qstep, 'staticitems' );
            $this->createTable( $movable_qstep, 'dynamicitems' );
            return;
        }
        $this->staticitems = $this->dynamicitems = array();

        $this->staticitems = array(
        array(0.00, 0.025019, 0.025019),
        array(0.10, 0.100178, 0.050142),
        array(0.20, 0.200321, 0.050003),
        array(0.30, 0.301007, 0.050684),
        array(0.40, 0.401636, 0.049945),
        array(0.50, 0.501572, 0.049991),
        array(0.60, 0.599413, 0.047852),
        array(0.70, 0.699639, 0.052376),
        array(0.80, 0.799954, 0.047940),
        array(0.90, 0.897190, 0.049297),
        array(1.00, 0.996257, 0.049771),
        array(1.10, 1.095282, 0.049255),
        array(1.20, 1.198429, 0.053893),
        array(1.30, 1.304474, 0.052152),
        array(1.40, 1.405808, 0.049183),
        array(1.50, 1.499977, 0.044986),
        array(1.60, 1.600974, 0.056011),
        array(1.70, 1.699463, 0.042478),
        array(1.80, 1.795798, 0.053858),
        array(1.90, 1.897073, 0.047417),
        array(2.00, 1.994338, 0.049849),
        array(2.10, 2.096590, 0.052405),
        array(2.20, 2.204085, 0.055091),
        array(2.30, 2.305275, 0.046100),
        array(2.40, 2.399355, 0.047981),
        array(2.50, 2.497274, 0.049940),
        array(2.60, 2.599191, 0.051978),
        array(2.70, 2.705266, 0.054099),
        array(2.80, 2.801381, 0.042018),
        array(2.90, 2.901419, 0.058021),
        array(3.00, 3.004504, 0.045065),
        array(3.10, 3.096005, 0.046437),
        array(3.20, 3.190292, 0.047851),
        array(3.30, 3.287451, 0.049309),
        array(3.40, 3.387569, 0.050810),
        array(3.50, 3.490736, 0.052358),
        array(3.60, 3.597044, 0.053952),
        array(3.70, 3.706591, 0.055596),
        array(3.80, 3.800186, 0.038001),
        array(3.90, 3.896632, 0.058446),
        array(4.00, 3.995026, 0.039950),
        array(4.10, 4.096416, 0.061442),
        array(4.20, 4.199855, 0.041998),
        array(4.30, 4.306444, 0.064592),
        array(4.40, 4.415186, 0.044151),
        array(4.50, 4.504379, 0.045043),
        array(4.60, 4.595374, 0.045953),
        array(4.70, 4.688206, 0.046881),
        array(4.80, 4.782914, 0.047828),
        array(4.90, 4.879536, 0.048795),
        array(5.00, 4.978109, 0.049780),
        array(5.10, 5.078673, 0.050786),
        array(5.20, 5.181269, 0.051812),
        array(5.30, 5.285938, 0.052859),
        array(5.40, 5.392721, 0.053926),
        array(5.50, 5.501661, 0.055016),
        array(5.60, 5.612802, 0.056127),
        array(5.70, 5.697415, 0.028488),
        array(5.80, 5.783737, 0.057836),
        array(5.90, 5.900576, 0.059004),
        array(6.00, 6.019776, 0.060197),
        array(6.10, 6.110524, 0.030553),
        array(6.20, 6.203106, 0.062030),
        array(6.30, 6.296617, 0.031483),
        array(6.40, 6.392018, 0.063919),
        array(6.50, 6.488378, 0.032443),
        array(6.60, 6.586684, 0.065865),
        array(6.70, 6.685978, 0.033430),
        array(6.80, 6.787278, 0.067871),
        array(6.90, 6.889597, 0.034449),
        array(7.00, 6.993982, 0.069938),
        array(7.10, 7.099416, 0.035497),
        array(7.20, 7.206980, 0.072068),
        array(7.30, 7.315626, 0.036579),
        array(7.40, 7.389149, 0.036946),
        array(7.50, 7.501102, 0.075009),
        array(7.60, 7.614182, 0.038072),
        array(7.70, 7.690706, 0.038454),
        array(7.80, 7.807228, 0.078070),
        array(7.90, 7.924922, 0.039625),
        array(8.00, 8.004569, 0.040023),
        array(8.10, 8.085016, 0.040425),
        array(8.20, 8.207513, 0.082073),
        array(8.30, 8.331242, 0.041656),
        array(8.40, 8.414972, 0.042075),
        array(8.50, 8.499544, 0.042498),
        array(8.60, 8.584966, 0.042925),
        array(8.70, 8.671246, 0.043356),
        array(8.80, 8.802625, 0.088024),
        array(8.90, 8.935325, 0.044677),
        array(9.00, 9.025126, 0.045125),
        array(9.10, 9.115830, 0.045579),
        array(9.20, 9.207446, 0.046037),
        array(9.30, 9.299982, 0.046500),
        array(9.40, 9.393448, 0.046967),
        array(9.50, 9.487854, 0.047439),
        array(9.60, 9.583209, 0.047916),
        array(9.70, 9.679522, 0.048398),
        array(9.80, 9.776803, 0.048884),
        array(9.90, 9.875061, 0.049375),
        array(10.00, 9.974307, 0.049871),
        array(10.10, 10.074550, 0.050372),
        array(10.20, 10.175801, 0.050879),
        array(10.30, 10.278070, 0.051390),
        array(10.40, 10.381366, 0.051907),
        array(10.50, 10.485700, 0.052428),
        array(10.60, 10.591083, 0.052955),
        array(10.70, 10.697526, 0.053488),
        array(10.80, 10.805038, 0.054025),
        array(10.90, 10.913630, 0.054568),
        array(11.00, 11.023314, 0.055116),
        array(11.10, 11.134100, 0.055670),
        array(11.20, 11.246000, 0.056230),
        array(11.40, 11.359024, 0.056795),
        array(11.50, 11.473184, 0.057366),
        array(11.60, 11.588491, 0.057942),
        array(11.70, 11.704958, 0.058525),
        array(11.80, 11.822595, 0.059113),
        array(11.90, 11.941414, 0.059707),
        array(12.10, 12.061427, 0.060307),
        array(12.20, 12.182646, 0.060913),
        array(12.30, 12.305083, 0.061525),
        array(12.40, 12.428752, 0.062144),
        array(12.60, 12.553663, 0.062768),
        array(12.70, 12.679829, 0.063399),
        array(12.80, 12.807264, 0.064036),
        array(12.90, 12.935979, 0.064680),
        array(13.10, 13.065988, 0.065330),
        array(13.20, 13.197303, 0.065986),
        array(13.30, 13.329938, 0.066649),
        array(13.50, 13.463906, 0.067319),
        array(13.60, 13.599221, 0.067996),
        array(13.70, 13.735895, 0.068679),
        array(13.90, 13.873943, 0.069369),
        array(14.00, 14.013379, 0.070067),
        array(14.20, 14.154216, 0.070771),
        array(14.30, 14.296468, 0.071482),
        array(14.40, 14.440149, 0.072200),
        array(14.60, 14.585275, 0.072926),
        array(14.70, 14.731860, 0.073659),
        array(14.90, 14.879917, 0.074399),
        array(15.00, 15.029463, 0.075147),
        array(15.20, 15.180512, 0.075902),
        array(15.30, 15.333078, 0.076665),
        array(15.50, 15.487178, 0.077435),
        array(15.60, 15.642827, 0.078214),
        array(15.80, 15.800040, 0.079000),
        array(16.00, 15.958833, 0.079794),
        array(16.10, 16.119222, 0.080596),
        array(16.30, 16.281223, 0.081406),
        array(16.40, 16.444852, 0.082224),
        array(16.60, 16.610125, 0.083051),
        array(16.80, 16.777060, 0.083885),
        array(16.90, 16.945672, 0.084729),
        array(17.10, 17.115979, 0.085580),
        array(17.30, 17.287998, 0.086440),
        array(17.50, 17.461745, 0.087309),
        array(17.60, 17.637239, 0.088186),
        array(17.80, 17.814496, 0.089072),
        array(18.00, 17.993534, 0.089968),
        array(18.20, 18.174372, 0.090872),
        array(18.40, 18.357028, 0.091785),
        array(18.50, 18.541519, 0.092708),
        array(18.70, 18.727865, 0.093639),
        array(18.90, 18.916083, 0.094580),
        array(19.10, 19.106192, 0.095531),
        array(19.30, 19.298213, 0.096491),
        array(19.50, 19.492163, 0.097461),
        array(19.70, 19.688063, 0.098440),
        array(19.90, 19.885931, 0.099429),
        array(20.10, 20.085788, 0.100429),
        array(20.30, 20.287653, 0.101438),
        array(20.50, 20.491547, 0.102458),
        array(20.70, 20.697491, 0.103487),
        array(20.90, 20.905504, 0.104528),
        array(21.10, 21.115609, 0.105578),
        array(21.30, 21.327824, 0.106639),
        array(21.50, 21.542172, 0.107711),
        array(21.80, 21.758674, 0.108793),
        array(22.00, 21.977353, 0.109887),
        array(22.20, 22.198229, 0.110991),
        array(22.40, 22.421325, 0.112107),
        array(22.60, 22.646663, 0.113233),
        array(22.90, 22.874266, 0.114371),
        array(23.10, 23.104156, 0.115521),
        array(23.30, 23.336357, 0.116682),
        array(23.60, 23.570891, 0.117854),
        array(23.80, 23.807782, 0.119038),
        array(24.00, 24.047054, 0.120235),
        array(24.30, 24.288731, 0.121443),
        array(24.50, 24.532837, 0.122664),
        array(24.80, 24.779396, 0.123896),
        array(25.00, 25.028433, 0.125142),
        array(25.30, 25.279973, 0.126399),
        array(25.50, 25.534041, 0.127670),
        array(25.80, 25.790663, 0.128953),
        array(26.00, 26.049863, 0.130249),
        array(26.30, 26.311668, 0.131558),
        array(26.60, 26.576105, 0.132880),
        array(26.80, 26.843200, 0.134216),
        array(27.10, 27.112978, 0.135564),
        array(27.40, 27.385468, 0.136927),
        array(27.70, 27.660696, 0.138303),
        array(27.90, 27.938691, 0.139693),
        array(28.20, 28.219480, 0.141097),
        array(28.50, 28.503090, 0.142515),
        array(28.80, 28.789551, 0.143947),
        array(29.10, 29.078891, 0.145394),
        array(29.40, 29.371138, 0.146855),
        array(29.70, 29.666323, 0.148331),
        array(30.00, 29.964475, 0.149822),
        array(30.30, 30.265623, 0.151328),
        array(30.60, 30.569797, 0.152848),
        array(30.90, 30.877029, 0.154385),
        array(31.20, 31.187348, 0.155936),
        array(31.50, 31.500786, 0.157503),
        array(31.80, 31.817374, 0.159086),
        array(32.10, 32.137144, 0.160685),
        array(32.50, 32.460128, 0.162300),
        array(32.80, 32.786358, 0.163930),
        array(33.10, 33.115866, 0.165578),
        array(33.40, 33.448686, 0.167242),
        array(33.80, 33.784851, 0.168923),
        array(34.10, 34.124394, 0.170621),
        array(34.50, 34.467350, 0.172336),
        array(34.80, 34.813753, 0.174067),
        array(35.20, 35.163637, 0.175817),
        array(35.50, 35.517037, 0.177584),
        array(35.90, 35.873989, 0.179369),
        array(36.20, 36.234529, 0.181171),
        array(36.60, 36.598691, 0.182992),
        array(37.00, 36.966514, 0.184831),
        array(37.30, 37.338034, 0.186689),
        array(37.70, 37.713288, 0.188565),
        array(38.10, 38.092312, 0.190460),
        array(38.50, 38.475147, 0.192375),
        array(38.90, 38.861829, 0.194308),
        array(39.30, 39.252396, 0.196260),
        array(39.60, 39.646889, 0.198233),
        array(40.00, 40.045347, 0.200225),
        array(40.40, 40.447810, 0.202238),
        array(40.90, 40.854317, 0.204270),
        array(41.30, 41.264910, 0.206323),
        array(41.70, 41.679629, 0.208397),
        array(42.10, 42.098516, 0.210491),
        array(42.50, 42.521613, 0.212606),
        array(42.90, 42.948962, 0.214743),
        array(43.40, 43.380607, 0.216902),
        array(43.80, 43.816589, 0.219081),
        array(44.30, 44.256953, 0.221283),
        array(44.70, 44.701743, 0.223507),
        array(45.20, 45.151003, 0.225753),
        array(45.60, 45.604778, 0.228022),
        array(46.10, 46.063114, 0.230314),
        array(46.50, 46.526056, 0.232629),
        array(47.00, 46.993650, 0.234966),
        array(47.50, 47.465944, 0.237328),
        array(47.90, 47.942985, 0.239713),
        array(48.40, 48.424820, 0.242122),
        array(48.90, 48.911498, 0.244556),
        array(49.40, 49.403066, 0.247013),
        array(49.90, 49.899575, 0.249496),
        array(50.40, 50.401075, 0.252004),
        array(50.90, 50.907614, 0.254536),
        array(51.40, 51.419244, 0.257095),
        array(51.90, 51.936016, 0.259678),
        array(52.50, 52.457981, 0.262288),
        array(53.00, 52.985193, 0.264924),
        array(53.50, 53.517703, 0.267587),
        array(54.10, 54.055565, 0.270276),
        array(54.60, 54.598832, 0.272992),
        array(55.10, 55.147560, 0.275736),
        array(55.70, 55.701802, 0.278507),
        array(56.30, 56.261614, 0.281306),
        array(56.80, 56.827053, 0.284133),
        array(57.40, 57.398175, 0.286989),
        array(58.00, 57.975036, 0.289873),
        array(58.60, 58.557694, 0.292786),
        array(59.10, 59.146209, 0.295729),
        array(59.70, 59.740638, 0.298701),
        array(60.30, 60.341042, 0.301703),
        array(60.90, 60.947479, 0.304735),
        array(61.60, 61.560012, 0.307798),
        array(62.20, 62.178700, 0.310891),
        array(62.80, 62.803606, 0.314015),
        array(63.40, 63.434793, 0.317172),
        array(64.10, 64.072323, 0.320359),
        array(64.70, 64.716261, 0.323579),
        array(65.40, 65.366670, 0.326831),
        array(66.00, 66.023616, 0.330116),
        array(66.70, 66.687164, 0.333433),
        array(67.40, 67.357381, 0.336784),
        array(68.00, 68.034334, 0.340169),
        array(68.70, 68.718091, 0.343588),
        array(69.40, 69.408719, 0.347041),
        array(70.10, 70.106289, 0.350529),
        array(70.80, 70.810869, 0.354052),
        array(71.50, 71.522530, 0.357610),
        array(72.20, 72.241343, 0.361204),
        array(73.00, 72.967380, 0.364834),
        array(73.70, 73.700715, 0.368501),
        array(74.40, 74.441419, 0.372204),
        array(75.20, 75.189568, 0.375945),
        array(75.90, 75.945235, 0.379723),
        array(76.70, 76.708498, 0.383540),
        array(77.50, 77.479431, 0.387394),
        array(78.30, 78.258113, 0.391288),
        array(79.00, 79.044620, 0.395220),
        array(79.80, 79.839031, 0.399192),
        array(80.60, 80.641427, 0.403204),
        array(81.50, 81.451886, 0.407256),
        array(82.30, 82.270492, 0.411350),
        array(83.10, 83.097324, 0.415483),
        array(83.90, 83.932466, 0.419659),
        array(84.80, 84.776001, 0.423876),
        array(85.60, 85.628014, 0.428137),
        array(86.50, 86.488590, 0.432440),
        array(87.40, 87.357815, 0.436786),
        array(88.20, 88.235775, 0.441175),
        array(89.10, 89.122560, 0.445610),
        array(90.00, 90.018257, 0.450088),
        array(90.90, 90.922955, 0.454611),
        array(91.80, 91.836746, 0.459180),
        array(92.80, 92.759720, 0.463795),
        array(93.70, 93.691971, 0.468456),
        array(94.60, 94.633591, 0.473164),
        array(95.60, 95.584675, 0.477920),
        array(96.50, 96.545317, 0.482723),
        array(97.50, 97.515613, 0.487574),
        array(98.50, 98.495661, 0.492474),
        array(99.50, 99.485559, 0.497424),
        array(100.50, 100.485406, 0.502423),
        array(101.50, 101.495301, 0.507473),
        array(102.50, 102.515346, 0.512573),
        array(103.50, 103.545642, 0.517724),
        array(104.60, 104.586293, 0.522928),
        array(105.60, 105.637403, 0.528183),
        array(106.70, 106.699076, 0.533491),
        array(107.80, 107.771420, 0.538853),
        array(108.90, 108.854540, 0.544268),
        array(109.90, 109.948547, 0.549739),
        array(111.10, 111.053548, 0.555263),
        array(112.20, 112.169655, 0.560844),
        array(113.30, 113.296978, 0.566480),
        array(114.40, 114.435632, 0.572174),
        array(115.60, 115.585729, 0.577924),
        array(116.70, 116.747385, 0.583732),
        array(117.90, 117.920716, 0.589599),
        array(119.10, 119.105839, 0.595525),
        array(120.30, 120.302872, 0.601509),
        array(121.50, 121.511936, 0.607555),
        array(122.70, 122.733152, 0.613661),
        array(124.00, 123.966640, 0.619828),
        array(125.20, 125.212526, 0.626058),
        array(126.50, 126.470933, 0.632350),
        array(127.70, 127.741987, 0.638705),
        array(129.00, 129.025815, 0.645124),
        array(130.30, 130.322546, 0.651608),
        array(131.60, 131.632309, 0.658156),
        array(133.00, 132.955236, 0.664771),
        array(134.30, 134.291458, 0.671452),
        array(135.60, 135.641110, 0.678200),
        array(137.00, 137.004326, 0.685016),
        array(138.40, 138.381242, 0.691901),
        array(139.80, 139.771997, 0.698855),
        array(141.20, 141.176729, 0.705878),
        array(142.60, 142.595578, 0.712972),
        array(144.00, 144.028688, 0.720138),
        array(145.50, 145.476200, 0.727375),
        array(146.90, 146.938260, 0.734685),
        array(148.40, 148.415014, 0.742069),
        array(149.90, 149.906610, 0.749527),
        array(151.40, 151.413197, 0.757060),
        array(152.90, 152.934924, 0.764668),
        array(154.50, 154.471946, 0.772354),
        array(156.00, 156.024415, 0.780116),
        array(157.60, 157.592486, 0.787956),
        array(159.20, 159.176317, 0.795875),
        array(160.80, 160.776066, 0.803874),
        array(162.40, 162.391892, 0.811953),
        array(164.00, 164.023958, 0.820113),
        array(165.70, 165.672426, 0.828355),
        array(167.30, 167.337461, 0.836680),
        array(169.00, 169.019231, 0.845089),
        array(170.70, 170.717902, 0.853582),
        array(172.40, 172.433646, 0.862161),
        array(174.20, 174.166633, 0.870826),
        array(175.90, 175.917036, 0.879578),
        array(177.70, 177.685032, 0.888418),
        array(179.50, 179.470796, 0.897347),
        );
    
    
        $this->dynamicitems = array(
        array(0.00, 0.026170, 0.026170),
        array(0.10, 0.104789, 0.052450),
        array(0.20, 0.208239, 0.051002),
        array(0.30, 0.304589, 0.045349),
        array(0.40, 0.411152, 0.061215),
        array(0.50, 0.524658, 0.052292),
        array(0.60, 0.607289, 0.030340),
        array(0.70, 0.708214, 0.070587),
        array(0.80, 0.819754, 0.040954),
        array(0.90, 0.905969, 0.045262),
        array(1.00, 1.001251, 0.050021),
        array(1.10, 1.106553, 0.055282),
        array(1.20, 1.222930, 0.061096),
        array(1.30, 1.351546, 0.067521),
        array(1.50, 1.493690, 0.074623),
        array(1.60, 1.650783, 0.082471),
        array(1.80, 1.824397, 0.091144),
        array(2.00, 2.016270, 0.100731),
        array(2.20, 2.228323, 0.111324),
        array(2.50, 2.462678, 0.123032),
        array(2.70, 2.721681, 0.135972),
        array(3.00, 3.007922, 0.150271),
        array(3.30, 3.324268, 0.166076),
        array(3.70, 3.673884, 0.183542),
        array(4.10, 4.060270, 0.202845),
        array(4.50, 4.487293, 0.224179),
        array(5.00, 4.959225, 0.247755),
        array(5.50, 5.480791, 0.273812),
        array(6.00, 6.057211, 0.302609),
        array(6.70, 6.694254, 0.334435),
        array(7.40, 7.398295, 0.369608),
        array(8.20, 8.176380, 0.408479),
        array(9.00, 9.036297, 0.451439),
        array(10.00, 9.986652, 0.498917),
        array(11.00, 11.036958, 0.551389),
        array(12.20, 12.197725, 0.609379),
        array(13.50, 13.480571, 0.673468),
        array(14.90, 14.898335, 0.744297),
        array(16.40, 16.465206, 0.822576),
        array(18.20, 18.196867, 0.909087),
        array(20.10, 20.110649, 1.004696),
        array(22.20, 22.225705, 1.110361),
        array(24.50, 24.563202, 1.227138),
        array(27.10, 27.146537, 1.356198),
        array(30.00, 30.001563, 1.498830),
        array(33.10, 33.156855, 1.656463),
        array(36.60, 36.643992, 1.830675),
        array(40.40, 40.497874, 2.023208),
        array(44.70, 44.757073, 2.235991),
        array(49.40, 49.464215, 2.471152),
        array(54.60, 54.666412, 2.731046),
        array(60.30, 60.415729, 3.018272),
        array(66.70, 66.769706, 3.335706),
        array(73.70, 73.791938, 3.686526),
        array(81.50, 81.552704, 4.074241),
        array(90.00, 90.129676, 4.502732),
        array(99.50, 99.608697, 4.976289),
        array(109.90, 110.084635, 5.499650),
        array(121.50, 121.662337, 6.078053),
        array(134.30, 134.457677, 6.717287),
        array(148.40, 148.598714, 7.423750),
        array(164.00, 163.226977, 8.204513),
        array(181.30, 181.498879, 9.067389),
        );
    
        // sort(M_static_table.begin(), M_static_table.end( ) );
        // sort(M_movable_table.begin(), M_dunamic_table.end( ) );
    }

    function getStaticObjInfo( $see_dist,
                               &$ave,
                               &$err )
    {
        $test = $see_dist - .001;
        for ($i = 0; $i < count($this->staticitems); $i++) {
            if ($this->staticitems[$i][0] > $see_dist) {
                --$i;
                break;
            }
        }
        if ($i >= count($this->staticitems)) {
            throw new \Exception("ObjectTable::getStaticObjInfo : illegal dist : " . $see_dist);
        }

        $ave = $this->staticitems[$i][1];
        $err = $this->staticitems[$i][2];
    
        return true;
    }


    function getMoveableObjInfo( $see_dist,
                               &$ave,
                               &$err )
    {
        $test = $see_dist - .001;
        for ($i = 0; $i < count($this->dynamicitems); $i++) {
            if ($this->dynamicitems[$i][0] > $see_dist) {
                --$i;
                break;
            }
        }
        if ($i >= count($this->dynamicitems)) {
            throw new \Exception("ObjectTable::getStaticObjInfo : illegal dist : " . $see_dist);
        }

        $ave = $this->dynamicitems[$i][1];
        $err = $this->dynamicitems[$i][2];
    
        return true;
    }

    function quantize( $value,
                       $qstep )
    {
        return round( $value / $qstep ) * $qstep;
    }

    function quantize_dist( $unq_dist,
                            $qstep )
    {
        return $this->quantize( exp
                         ( $this->quantize( log
                                     ( $unq_dist + self::SERVER_EPS ), $qstep ) ),
                         0.1);
    }

    function createTable2( $qstep,
                          $table )
    {
        $this->$table = array();
    
        $prev_val = -0.1;
    
        for ( $dist = 0.0; $dist < 180.0; $dist += 0.01 ) {
    
            $see_dist = $this->quantize_dist( $dist, $qstep );
    
            if ( abs( $prev_val - $see_dist ) < 0.001 ) {
                continue;
            }
            $prev_val = $see_dist;
    
    
            // unquantize min
            $min_dist = $see_dist - 0.05;
            if ( $min_dist < self::SERVER_EPS ) $min_dist = self::SERVER_EPS;
            $min_dist = log( $min_dist );
            $min_dist = ( round( $min_dist / $qstep ) - 0.5 ) * $qstep;
            $min_dist = exp( $min_dist ) - self::SERVER_EPS;
            if ( $min_dist < 0 ) $min_dist = 0;
    
            // unquantize max
            $max_dist = $see_dist + 0.049999;
            $max_dist = log( $max_dist );
            $max_dist = ( round( $max_dist / $qstep ) + 0.49999 ) * $qstep;
            $max_dist = exp( $max_dist ) - self::SERVER_EPS;
    
            //double ave_dist = (max_dist + min_dist) * 0.5;
            //double err_dist = (max_dist - min_dist) * 0.5;
    
            array_push($this->$table, array($see_dist, // quantized dist
                                       ($max_dist + $min_dist) * 0.5,   // average
                                       ($max_dist - $min_dist) * 0.5 ) ); // error
        }
    }

}