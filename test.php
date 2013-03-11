<?php
function __autoload($class)
{
    include __DIR__ . '/' . substr($class, strrpos($class, '\\')+1) . '.php';
}
$manager = new ThroughBall\Util\UDPManager('testing');
$opponent = new ThroughBall\Util\UDPManager('opponent');

$goalie = $manager->addGoalie();
$player1 = $manager->addPlayer();

$goalie = $opponent->addGoalie();
$player = $opponent->addPlayer();

$player->move(-10, 10);
$player1->move(-10, 10);
$manager->run();
?>