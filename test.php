<?php
function __autoload($class)
{
    include __DIR__ . '/' . str_replace('ThroughBall\\', '', $class) . '.php';
}
$manager = new ThroughBall\Util\UDPManager('testing');
$opponent = new ThroughBall\Util\UDPManager('opponent');

$goalie = $manager->addGoalie();
$player = $manager->addPlayer();

$goalie = $opponent->addGoalie();
$player = $opponent->addPlayer();

$manager->run();
?>