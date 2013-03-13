<?php
function __autoload($class)
{
    include __DIR__ . '/' . substr($class, strrpos($class, '\\')+1) . '.php';
}
class Tester extends ThroughBall\Player {
    protected $goaldirection = 0;
    protected $mygoaldirection = 0;
    protected $visiblegoal;
    function handleSee($see)
    {
        parent::handleSee($see);
        if (!$this->cycle) return;
        $goal = $see->getItem($this->ownGoal());
        if ($goal) {
            $this->mygoaldirection = $goal->direction;
            $this->visiblegoal = $this->side;
            echo $this->team, " ", $this->side, " see own ", $this->ownGoal(), " goal ", $goal->direction, "\n";
        }
        $goal = $see->getItem($this->opponentGoal());
        if ($goal) {
            $this->goaldirection = $goal->direction;
            $this->visiblegoal = $this->opponent();
            echo $this->team, " ", $this->side, " see opponent ", $this->opponentGoal(), " goal ", $goal->direction, "\n";
        }
        $ball = $see->getItem('(b)');
        if ($ball) {
            if ($this->isKickable($ball)) {
                if ($this->visiblegoal == $this->opponent()) {
                    $this->turn($this->goaldirection);
                    $this->kick(100, 0);
                } else {
                    $this->turn(-$this->mygoaldirection);
                    $this->kick(100, 0);
                }
            } else {
                $this->moveTowards($ball, 60);
            }
            return;
        }
        $this->turn(70);
    }
}

$manager = new ThroughBall\Util\UDPManager('testing');
$opponent = new ThroughBall\Util\UDPManager('opponent');

//$goalie = $manager->addGoalie();
$player1 = $manager->addPlayer('Tester');
$player2 = $manager->addPlayer('Tester');
$player4 = $manager->addPlayer('Tester');
$player6 = $manager->addPlayer('Tester');
$player8 = $manager->addPlayer('Tester');
$player10 = $manager->addPlayer('Tester');
$player12 = $manager->addPlayer('Tester');
$player14 = $manager->addPlayer('Tester');
$player16 = $manager->addPlayer('Tester');
$player18 = $manager->addPlayer('Tester');

//$goalie = $opponent->addGoalie();
$player = $opponent->addPlayer('Tester');
$player3 = $opponent->addPlayer('Tester');
$player5 = $opponent->addPlayer('Tester');
$player7 = $opponent->addPlayer('Tester');
$player9 = $opponent->addPlayer('Tester');
$player11 = $opponent->addPlayer('Tester');
$player13 = $opponent->addPlayer('Tester');
$player15 = $opponent->addPlayer('Tester');
$player17 = $opponent->addPlayer('Tester');
$player19 = $opponent->addPlayer('Tester');

$player->move(0, 0);
$player1->move(0, 0);
$player2->move(-20, 20);
$player3->move(-30, 30);
$player4->move(-25, 0);
$player5->move(-25, 0);
$player6->move(1, 0);
$player7->move(1, 0);
$manager->run();
?>