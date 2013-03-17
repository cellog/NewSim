<?php
function __autoload($class)
{
    include __DIR__ . '/' . str_replace(array('ThroughBall\\', '\\'), array('', '/'),
                                        $class) . '.php';
}

$manager = new ThroughBall\Util\UDPManager('testing');
$opponent = new ThroughBall\Util\UDPManager('opponent');


//$goalie = $manager->addGoalie();
$player1 = $manager->addPlayer('ThroughBall\\Players\\Tester');
//$player2 = $manager->addPlayer('Tester');

//$goalie = $opponent->addGoalie();
//$player = $opponent->addPlayer('Tester');
//$player3 = $opponent->addPlayer('Tester');

//$player->move(-10, 10);
$player1->move(-10, -10);
//$player2->move(-20, 20);
//$player3->move(-30, 30);
$manager->run();
?>