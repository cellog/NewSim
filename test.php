<?php
function __autoload($class)
{
    include __DIR__ . '/' . substr($class, strrpos($class, '\\')+1) . '.php';
}
class Tester extends ThroughBall\Player {
    protected $goaldirection = 0;
    function handleSee($see)
    {
        parent::handleSee($see);
        if (!$this->cycle) return;
        $goal = $see->getItem('(g ' . $this->opponent() . ')');
        if ($goal) {
            $this->goaldirection = $goal->direction;
        }
        $ball = $see->getItem('(b)');
        if ($ball) {
            if ($this->isKickable($ball)) {
                $this->kick(100, $this->goaldirection);
            } else {
                $this->moveTowards($ball);
            }
            return;
        }
        $this->turn(70);
    }
}

$manager = new ThroughBall\Util\UDPManager('testing');
$opponent = new ThroughBall\Util\UDPManager('opponent');

$goalie = $manager->addGoalie();
$player1 = $manager->addPlayer('Tester');

$goalie = $opponent->addGoalie();
$player = $opponent->addPlayer('Tester');

$player->move(-10, 10);
$player1->move(-10, 10);
$manager->run();
?>