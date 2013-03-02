<?php
include 'UDP.php';
include 'Player.php';
include 'UDPManager.php';
$manager = new ThroughBall\Util\UDPManager('testing');
$opponent = new ThroughBall\Util\UDPManager('opponent');

$goalie = $manager->addGoalie();
$player = $manager->addPlayer();

$goalie = $opponent->addGoalie();
$player = $opponent->addPlayer();

$manager->run();
?>