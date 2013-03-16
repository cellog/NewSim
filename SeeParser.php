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
        if (preg_match('/\(see (\d+)\)/', $see, $match)) {
            $seen->setTime($match[1]);
            $seen->setParams(array());
            return $seen;
        }
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
            $items = array();
            for ($i = 0; $i < count($matches[0]);$i++) {
                for ($j = 2; $j < 12; $j++) {
                    $items[$matches[1][$i]][] = $matches[$j][$i];
                }
            }
            $seen->setParams($items);
        } else {
            throw new \Exception('could not parse ' . $see);
        }
        return $seen;
    }
}