<?php
function __autoload($class)
{
    include __DIR__ . '/' . substr($class, strrpos($class, '\\')+1) . '.php';
}
class Tester extends ThroughBall\Player {
    function handleSee($see)
    {
        parent::handleSee($see);
        if (!$this->cycle) return;
        $ball = $see->getItem('(b)');
        if ($ball) {
            $this->moveTowards($ball);
            return;
        }
        $this->turn(100);
    }
}

$manager = new ThroughBall\Util\UDPManager('testing');
$opponent = new ThroughBall\Util\UDPManager('opponent');

$goalie = $manager->addGoalie();
$player1 = $manager->addPlayer('Tester');

$goalie = $opponent->addGoalie();
$player = $opponent->addPlayer();

$player->move(-10, 10);
$player1->move(-10, 10);
$manager->run();
?>