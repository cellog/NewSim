<?php
function __autoload($class)
{
    include __DIR__ . '/' . substr($class, strrpos($class, '\\')+1) . '.php';
}
$manager = new ThroughBall\Util\UDPManager('testing');
$opponent = new ThroughBall\Util\UDPManager('opponent');

$goalie = $manager->addGoalie();
$player = $manager->addPlayer();

$goalie = $opponent->addGoalie();
$player = $opponent->addPlayer();

$manager->run();
?>