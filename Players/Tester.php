<?php
namespace ThroughBall\Players;
use ThroughBall\Player;
class Tester extends Player {
    protected $goaldirection = 0;
    protected $mygoaldirection = 0;
    protected $visiblegoal;
    function handleSenseBody($sensebody)
    {
        parent::handleSenseBody($sensebody);
        $see = $this->see;
        if (!$see) return;
        $goal = $see->getItem('(g r)');
        if ($goal) {
            echo "goal ", $goal['direction'], ' ', $goal['distance'], "\n";
        }
        $goal = $see->getItem('(f c)');
        if ($goal) {
            echo "f c ", $goal['direction'], ' ', $goal['distance'], "\n";
        }
        $g = $this->getGoalDirection();
        echo "calc angle/distance ", $g['direction'], ' ', $g['distance'], "\n";
        if (!$this->cycle) return;
        $see = $this->see;
        $params = $this->see->listSeenItems();
        if (!count($params)) {
            $this->turn(-180);
            return;
        }
        //var_export($this->toRelativeCoordinates($this->getCoordinates()));
        $goal = $see->getItem($this->ownGoal());
        if ($goal) {
            $this->mygoaldirection = $goal['direction'];
            $this->visiblegoal = $this->side;
            //echo $this->team, " ", $this->side, " see own ", $this->ownGoal(), " goal ", $goal['direction'], "\n";
        }
        $goal = $see->getItem($this->opponentGoal());
        if ($goal) {
            $this->goaldirection = $goal['direction'];
            $this->visiblegoal = $this->opponent();
            //echo $this->team, " ", $this->side, " see opponent ", $this->opponentGoal(), " goal ", $goal['direction'], "\n";
        }
        $ball = $see->getItem('(b)');
        if ($ball) {
            if ($this->isKickable($ball)) {
                if ($this->visiblegoal == $this->opponent()) {
                    $goal = $this->see->getItem($this->opponentGoal());
                    if (!$goal) {
                        $this->turn(-45);
                    } else {
                        if ($goal['distance'] < 20) {
                            $this->shoot();
                        } else {
                            $this->kick(20, 0);
                            $this->dash(0, 20);
                        }
                    }
                } else {
                    $this->kick(100, 180 - $this->mygoaldirection);
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