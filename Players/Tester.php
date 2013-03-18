<?php
namespace ThroughBall\Players;
use ThroughBall\Player;
class Tester extends Player {
    protected $goaldirection = 0;
    protected $mygoaldirection = 0;
    protected $visiblegoal;
    protected $shot = 0;
    function handleSenseBody($sensebody)
    {
        static $foo = false;
        parent::handleSenseBody($sensebody);
        $g = $this->getGoalDirection();
        if (!$this->cycle) return;
        $see = $this->see;
        $rightline = $see->getItem('(l r)');
        $leftline = $see->getItem('(l l)');
        $topline = $see->getItem('(l t)');
        $bottomline = $see->getItem('(l b)');
        switch ($this->cycle) {
            case 1 : $this->turn(20);return;
            case 100 : $this->turn(20);return;
            case 200 : $this->turn(20);return;
            case 300 : $this->turn(20);return;
            case 400 : $this->turn(20);return;
            case 500 : $this->turn(20);return;
            case 600 : $this->turn(20);return;
            case 700 : $this->turn(20);return;
            case 800 : $this->turn(20);return;
        }
        if (0 == $this->cycle % 7) {
            $this->dash(0, 100);
            return;
        }
            echo $this->bodydirection, "\n";
        return;
        $params = $this->see->listSeenItems();
        if (!count($params)) {
            $this->turn(-180);
            return;
        }
        $ball = $see->getItem('(b)');
        if ($ball) {
            if ($this->isKickable($ball)) {
                $goal = $this->toAbsoluteCoordinates($this->knownLocations[$this->opponentGoal()]);
                $dist = $goal[1] - $this->coordinates[1];
                echo "ball kickable dist ", $dist, ' dir ', $g['direction'], ' body dir ',
                    $this->bodydirection, "\n";
                if ($this->cycle - $this->shot < 3) {
                    $this->dash($ball['direction'], 60);
                    return;
                }
                if ($dist > 57) {
                    echo "shoot far\n";
                    $this->shoot($goal['direction']);
                    $this->shot = $this->cycle;
                    return;
                    // we're defending
                } else {
                    if ($dist > 5) {
                        echo "turn to goal\n";
                        $this->turn($g['direction']/2);
                        return;
                    }
                    if ($dist < 20) {
                        echo "shoot\n";
                        $this->shoot($goal['direction']);
                        $this->shot = $this->cycle;
                    } else {
                        echo "dribble\n";
                        // dribble
                        $this->kick(20, 0);
                        $this->dash(0, 20);
                    }
                }
            } else {
                if ($ball['distance'] < 30 && ($ball['direction'] > 5 || $ball['direction'] < -5)) {
                    //echo 'turn to ball ', $ball['direction']/2, ' me', $sensebody->getParam('direction'),"\n";
                    $this->turnTowards($ball['direction']/2);
                } else {
                    //echo 'move to ball ', $ball['direction'], ' me', $sensebody->getParam('direction'),"\n";
                    if ($ball['distance'] > 1) {
                        $this->moveTowards($ball, 60);
                    } else {
                        $this->moveTowards($ball, 10);
                    }
                }
            }
            return;
        } 
        //echo "turn 30\n";
        $this->turn(30);
    }
}