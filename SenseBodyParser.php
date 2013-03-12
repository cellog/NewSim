<?php
namespace ThroughBall;
class SeeParser {
    private $debug;
    function __construct($debug)
    {
        $this->debug = $debug;
    }
    function parse($see, See $seen)
    {
        $count = substr($see, 5, strpos($see, '(', 1) - 6) + 0;
        $see = substr($see, strpos($see, '(', 1));
        if (preg_match_all('/\('
                        . '('
                        . '\(g [lr]\)'
                        . '|'
                        . '\([bBPFG]\)'
                        . '|'
                        . '\(p "([\-_a-zA-Z0-9]+)" (\d+)(?: (goalie))?\)'
                        . '|'
                        . '\(f [pg] [lcr] [tcb]\)'
                        . '|'
                        . '\(f [lcrtb] [tb0]\)'
                        . '|'
                        . '\(f [tblr] [tblr] [1-5]0\)'
                        . '|'
                        . '\(f c\)'
                        . '|'
                        . '\(l [btlr]\)'
                        . ')'
                        . ' (-?\d+(?:\.\d+)?(?:e-?\d+)?) (-?\d+(?:\.\d+)?(?:e-?\d+)?)(?: (-?\d+(?:\.\d+)?(?:e-?\d+)?))?(?: (-?\d+(?:\.\d+)?(?:e-?\d+)?))?(?: (-?\d+(?:\.\d+)?(?:e-?\d+)?))?(?: (-?\d+(?:\.\d+)?(?:e-?\d+)?))?(?: (-?\d+(?:\.\d+)?(?:e-?\d+)?))?(?: ([tk]))?'
                        . '\)/'
                        , $see, $matches)) {
            $seen->reset();
            $seen->setTime($count);
            for ($i = 0; $i < count($matches[0]);$i++) {
                if ($matches[2][$i]) {
                    $item = new SeenPlayer;
                    $item->setTeam($matches[2][$i]);
                    $item->setUnum($matches[3][$i]);
                    if ($matches[4][$i]) $item->setIsgoalie();
                    if ($matches[12][$i]) {
                        $matches[12][$i] == 'k' ? $item->setIsKicking() : $item->setIsTackling();
                    }
                } else {
                    $item = new Item;
                    $item->setName($matches[1][$i]);
                }
                for ($j = 5; $j < 12; $j++) {
                    $item->setValue($matches[$j][$i]);
                }
                $seen->addItem($item);
            }
        } else {
            throw new \Exception('could not parse ' . $see);
        }
        return $seen;
    }
}