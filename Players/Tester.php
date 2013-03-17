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
        echo "body direction ", $this->bodydirection, "\n";
        $g = $this->getGoalDirection();
        if (!$this->cycle) return;
        $see = $this->see;
        $params = $this->see->listSeenItems();
        if (!count($params)) {
            $this->turn(-180);
            return;
        }
        $ball = $see->getItem('(b)');
        if ($ball) {
            if ($this->isKickable($ball)) {
                echo "ball kickable dist ", $g['distance'], ' dir ', $g['direction'], ' ',
                    $this->bodydirection, "\n";
                if ($this->cycle - $this->shot < 3) {
                    $this->dash($ball['direction'], 60);
                    return;
                }
                if ($g['distance'] > 70) {
                    echo "shoot far\n";
                    $this->shoot($goal['direction']);
                    $this->shot = $this->cycle;
                    return;
                    // we're defending
                } else {
                    if ($g['direction'] > 5) {
                        echo "turn to goal\n";
                        $this->turn($g['direction']/2);
                        return;
                    }
                    if ($goal['distance'] < 20) {
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