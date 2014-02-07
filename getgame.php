<?php
function __autoload($class)
{
    include __DIR__ . '/../' . str_replace(array('ThroughBall\\', '\\'), array('NewSim/', '/'),
                                        $class) . '.php';
}
$a = new ThroughBall\Util\MonitorParser;
$a->setup(file_get_contents(__DIR__ . '/games/game1.rcg'));
header('Content-type: application/json');
echo json_encode($a->parse());