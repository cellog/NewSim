<?php
namespace ThroughBall;
class See
{
    protected $time;
    protected $items = array();
    static protected $objectTable;    
    
    function setParams(array $params)
    {
        if (!self::$objectTable) {
            self::$objectTable = new Util\ObjectTable;
        }
        foreach ($params as $i => $param) {
            foreach ($this->stats as $name => $index) {
                if (isset($param[$index])) {
                    $params[$i][$name] = $param[$index];
                }
            }
        }
        $this->items = $params;
    }

    function setTime($time)
    {
        $this->time = $time;
    }

    function getTime()
    {
        return $this->time;
    }

    function getParam($name)
    {
        if (isset($this->items[$name])) {
            return $this->items[$name];
        }
        throw new \Exception('Unknown sense_body: ' . $name . ' requested');
    }

    private $stats = array(
        'team' => 0,
        'unum' => 1,
        'isgoalie' => 2,
        'distance' => 3,
        'direction' => 4,
        'distancedelta' => 5,
        'directionelta' => 6,
        'bodydirection' => 7,
        'headdirection' => 8,
        'pointingdirection' => 9,
        'tackling' => 10,
        'kicking' => 10
    );

    function getPlayer($unum, $team, $stat = null)
    {
        if (isset($this->items['(p "' . $team . '" ' . $unum . ')'])) {
            $ret = $this->items['(p "' . $team . '" ' . $unum . ')'];
            $isgoalie = false;
        }
        if (isset($this->items['(p "' . $team . '" ' . $unum . ' goalie)'])) {
            $ret = $this->items['(p "' . $team . '" ' . $unum . ' goalie)'];
            $isgoalie = true;
        }
        if (!isset($ret)) {
            return false;
        }
        if ($stat) {
            if ($stat == 'goalie') {
                return $isgoalie;
            }
            if ($stat == 'tackling') {
                return $ret[$this->stats['tackling']] == 't';
            }
            if ($stat == 'kicking') {
                return $ret[$this->stats['tackling']] == 'k';
            }
            if (!isset($ret[$this->stats[$stat]])) {
                throw new \Exception('Unknown stat ' . $stat . ' requested for player ' . $unum . ' on team ' .
                                     $team);
            }
            return $ret[$this->stats[$stat]];
        }
        foreach ($this->stats as $name => $stat) {
            $ret[$name] = $ret[$stat];
        }
        $ret['tackling'] = $ret['tackling'] == 't';
        $ret['kicking'] = $ret['kicking'] == 'k';
        $ret['isgoalie'] = $isgoalie;
        return $ret;
    }

    function listSeenItems()
    {
        return array_keys($this->items);
    }

    function listSeenFlags()
    {
        $keys = array_flip(array_keys(Util\ObjectTable::$landmarks));
        return array_values(array_filter(array_keys($this->items), function($a) use ($keys) {return isset($keys[$a]);}));
    }

    function getSeenFlags()
    {
        $flags = $this->listSeenFlags();
        $items = $this->items;
        array_walk($items, function(&$a, $b) use ($flags) {if (!in_array($b, $flags)) $a = false;});
        return array_filter($items); // array_filter removes false values
    }

    function sortedSeenFlags()
    {
        $flags = $this->getSeenFlags();
        uasort($flags, function($a, $b){return ($a['distance'] == $b['distance']) ? 0 :
                                                ($a['distance'] < $b['distance']) ? -1 : 1;});
        return $flags;
    }

    function getItem($name)
    {
        if (isset($this->items[$name])) {
            $ret = $this->items[$name];
        } else {
            return false;
        }
        if ($stat) {
            if (!isset($ret[$this->stats[$stat]])) {
                throw new \Exception('Unknown stat ' . $stat . ' requested for player ' . $unum . ' on team ' .
                                     $team);
            }
            return $ret[$this->stats[$stat]];
        }
        foreach ($this->stats as $name => $stat) {
            $ret[$name] = $ret[$stat];
        }
        return $ret;
    }

    function reset()
    {
        $this->items = array();
    }

    protected $points = array();

    function getDirRange($seen_dir, $self_face, $self_face_err, &$average, &$err )
    {
        $average = $seen_dir + $self_face;
        $err = 0.5 + $self_face_err;
    }

    function bound($low, $x, $high)
    {
        $wtf = (($x < $high) ? (($low > $x) ? $low : $x) : $high);
        return $wtf;
    }

    function generateSeenPoints($seenflag, $id, &$self_face, &$self_face_err)
    {
        // marker must be the nearest one.
    
        ////////////////////////////////////////////////////////////////////
        // clear old points
        $this->points = array();
    
    
        ////////////////////////////////////////////////////////////////////
        // get closest marker info

        if (!isset(Util\ObjectTable::$landmarks[$id])) {
            throw new \Exception("(generatePoints) cannot find flag " . $id);
        }
        $marker_pos = Util\ObjectTable::$landmarks[$id];
    
        ////////////////////////////////////////////////////////////////////
        // get sector range
    
        if ( ! self::$objectTable->getStaticObjInfo( $seenflag['distance'],
                                               $ave_dist, $dist_error ) )
        {
            throw new \Exception("(generatePoints) flag distance calculation error");
        }
    
        $this->getDirRange( $seenflag['direction'], $self_face, $self_face_err, $ave_dir, $dir_error);
    
        // reverse direction, because base point is flag point
        $ave_dir += 180.0;
        $ave_dir = Util\Vector::normalizeAngle($ave_dir);
    
        $min_dist = $ave_dist - $dist_error;
        $dist_range = $dist_error * 2.0;
        $dist_inc = $dist_error / 16;
        if ($dist_inc > 0.01) $dist_inc = 0.01;
        $dist_loop = $this->bound( 2, ceil( $dist_range / $dist_inc ), 16 );
        $dist_inc = $dist_range / ( $dist_loop - 1 );
    
        $dir_range = $dir_error * 2.0;
        $circum = 2.0 * $ave_dist * M_PI * ( $dir_range / 360.0 );
        $circum_inc = max( 0.01, $circum / 32.0 );
        $dir_loop = $this->bound( 2, ceil( $circum / $circum_inc ) , 32 );
        $dir_inc = $dir_range / ( $dir_loop - 1 );
    
        $base_angle = $ave_dir - $dir_error; // left first;
        $base_vec = new Util\PolarVector( 1.0, 1 );
        for ( $idir = 0; $idir < $dir_loop; ++$idir, $base_angle += $dir_inc )
        {
    
            $add_dist = 0.0;
            for ($idist = 0; $idist < $dist_loop; ++$idist, $add_dist += $dist_inc )
            {
                $base_vec->polarAssign($min_dist + $add_dist, $base_angle);
                array_push($this->points, $marker_pos->simplePlus($base_vec) );
            }
        }
    }

    function hasPoints()
    {
        return count($this->points);
    }

    function resamplePoints($flag, $name, $self_face, $self_face_err, $points )
    {
    
        $count = count($this->points);
    
        if ($count >= 50 )
        {
            return;
        }
    
        if ( $count == 0 )
        {
            $this->points = $points;
            return;
        }

        mt_srand( 49827140 );

        // generate additional points using valid points coordinate
        // x & y are generated independently.
        // result may not be within current candidate sector
        
        if ( $count == 1 )
        {    
            for ($i = $count; $i < 50; ++$i )
            {
                array_push($this->points, array( mt_rand(-0.01, 0.01), mt_rand(-0.01, 0.01)) );
            }
    
            return;
        }

        for ($i = $count; $i < 50; ++$i )
        {
            array_push($this->points, array( mt_rand(-0.01, 0.01), mt_rand(-0.01, 0.01) ) );
        }
    }

    function updatePointsByFlags($flags, $self_face, $self_face_err )
    {
        // must check marker container is NOT empty.

        foreach ($flags as $frontflag => $front) {
            break;
        }
        $front = array_shift($flags); // discard first flag, we already used it
        if (!count($flags)) {
            return;
        }
    
        $count = 0;
        foreach ($flags as $name => $flag) {
            if ($count++ == 30) break; // only use 30 flags maximum
            $points = $this->points;
            $this->updatePointsBy( $flag, $name, $self_face, $self_face_err );
            $this->resamplePoints( $front, $frontflag, $self_face, $self_face_err, $points );
        }
    }

    function updatePointsBy($flag, $name, $self_face, $self_face_err )
    {
        ////////////////////////////////////////////////////////////////////
        // get marker global position
        if (!isset(Util\ObjectTable::$landmarks[$name])) {
            throw new \Exception("(updatePointsBy) why cannot find CLOSE flag $name??");
        }
        $marker_pos = Util\ObjectTable::$landmarks[$name];
    
        ////////////////////////////////////////////////////////////////////
        // get polar range info
    
        // get distance range info
        if ( !self::$objectTable->getStaticObjInfo($flag['distance'], $ave_dist, $dist_error ) )
        {
            throw new \Exception("(updatePointsBy) unexpected marker distance = " . $flag['distance']);
        }
    
        // get dir range info
        $this->getDirRange($flag['direction'], $self_face, $self_face_err, $ave_dir, $dir_error );
        // reverse, because base point calculated in above function is marker point.
        $ave_dir += 180.0;
        $ave_dir = Util\Vector::normalizeAngle($ave_dir);

        ////////////////////////////////////////////////////////////////////
        // create candidate sector
        $sector = new Util\FuzzySector($marker_pos, // base point
                               $ave_dist - $dist_error, // min dist
                               $ave_dist + $dist_error, // max dist
                               $ave_dir - $dir_error , // start left angle
                               $ave_dir + $dir_error ); // end right angle
    
        // check whether points are within candidate sector
        // not contained points are erased from container.

        $this->points = array_values(array_filter($this->points, function($a)
                                                  use ($sector) {
                                                    return $sector->has($a);
                                                  }));
    }

    function averagePoints(Util\Vector $ave_pos, Util\Vector $ave_err )
    {
    
        if (!count($this->points)) {
            return;
        }

        $max_x = $min_x = $this->points[0][0];
        $max_y = $min_y = $this->points[0][1];

        $total = array_reduce($this->points, function($r, $p) use (&$max_x, &$min_x, &$max_y, &$min_y) {
            $r[0] += $p[0];
            $r[1] += $p[1];
            if ($min_x > $p[0]) $min_x = $p[0];
            elseif ($max_x < $p[0]) $max_x = $p[0];
            if ($min_y > $p[1]) $min_y = $p[1];
            elseif ($max_y < $p[1]) $max_y = $p[1];
            return $r;
        }, array(0, 0));

        $ave_pos->assign($total[0]/count($this->points), $total[1]/count($this->points));
        $ave_err->assign(($max_x - $min_x ) * 0.5, ($max_y - $min_y ) * 0.5);
    }
}
