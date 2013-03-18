<?php
function __autoload($class)
{
    include __DIR__ . '/' . str_replace(array('ThroughBall\\', '\\'), array('', '/'),
                                        $class) . '.php';
}

$manager = new ThroughBall\Util\UDPManager('testing');
$opponent = new ThroughBall\Util\UDPManager('opponent');

//$goalie = $manager->addGoalie();
$a = 'ThroughBall\\Players\\Tester';
$player1 = $manager->addPlayer($a);
$player2 = $manager->addPlayer($a);
$player4 = $manager->addPlayer($a);
$player6 = $manager->addPlayer($a);
$player8 = $manager->addPlayer($a);
$player10 = $manager->addPlayer($a);
$player12 = $manager->addPlayer($a);
$player14 = $manager->addPlayer($a);
$player16 = $manager->addPlayer($a);
$player18 = $manager->addPlayer($a);

//$goalie = $opponent->addGoalie();
//$player = $opponent->addPlayer($a);
//$player3 = $opponent->addPlayer($a);
//$player5 = $opponent->addPlayer($a);
//$player7 = $opponent->addPlayer($a);
//$player9 = $opponent->addPlayer($a);
//$player11 = $opponent->addPlayer($a);
//$player13 = $opponent->addPlayer($a);
//$player15 = $opponent->addPlayer($a);
//$player17 = $opponent->addPlayer($a);
//$player19 = $opponent->addPlayer($a);

//$player->move(0, 0);
$player1->move(0, 0);
$player2->move(-20, 20);
//$player3->move(-30, 30);
$player4->move(-25, 0);
//$player5->move(-25, 0);
$player6->move(1, 0);
//$player7->move(1, 0);
$manager->run();
?>