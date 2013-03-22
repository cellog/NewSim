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
        $params = $this->see->listSeenItems();
        if (!count($params)) {
            $this->turn(-180);
            return;
        }
        $ball = $see->getItem('(b)');
        if ($ball) {
            if ($this->isKickable($ball)) {
                if ($this->cycle - $this->shot < 3) {
                    $this->dash($ball['direction'], 60);
                    return;
                }
                if ($dist > 57) {
                    echo "shoot far ", ($g->angle() - $this->bodydirection), "\n";
                    $this->shoot($g->angle() - $this->bodydirection);
                    $this->shot = $this->cycle;
                    return;
                    // we're defending
                } else {
                    if ($dist < 20) {
                        echo "shoot ",($g->angle()), "\n";
                        $this->shoot($g->angle());
                        $this->shot = $this->cycle;
                    } else {
                            echo "goal stat ", $g->angle(), ' ', $this->bodydirection,"\n";
                        if (abs($g->angle()) - abs($this->bodydirection) > 2) {
                            $this->turn($g->angle() - $this->bodydirection);
                            echo "turn to goal ", ($g->angle()),"\n";
                            return;
                        }
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