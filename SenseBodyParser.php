<?php
namespace ThroughBall;
class SenseBodyParser {
    private $debug;
    function __construct($debug)
    {
        $this->debug = $debug;
    }
    function parse($sb, SenseBody $body)
    {
        $count = substr($sb, 12, strpos($sb, '(', 1) - 13) + 0;
        $body->setTime($count);
        $sb = substr($sb, strpos($sb, '(', 1));
        if (preg_match('/'
                            . '\(view_mode (high|low) (narrow|normal|high)\)'
                            . ' '
                            . '\(stamina (\d+(?:\.\d+)?(?:e-?\d+)?) (\d+(?:\.\d+)?(?:e-?\d+)?) (\d+(?:\.\d+)?(?:e-?\d+)?)\)'
                            . ' '
                            . '\(speed (\d+(?:\.\d+)?(?:e-?\d+)?) (\-?\d+)\)'
                            . ' '
                            . '\(head_angle (\d+)\)'
                            . ' '
                            . '\(kick (\d+)\)'
                            . ' '
                            . '\(dash (\d+)\)'
                            . ' '
                            . '\(turn (\d+)\)'
                            . ' '
                            . '\(say (\d+)\)'
                            . ' '
                            . '\(turn_neck (\d+)\)'
                            . ' '
                            . '\(catch (\d+)\)'
                            . ' '
                            . '\(move (\d+)\)'
                            . ' '
                            . '\(change_view (\d+)\)'
                            . ' '
                            . '\(arm \(movable (\d+)\) \(expires (\d+)\) \(target (\d+(?:\.\d+)?(?:e-?\d+)?) (\-?\d+)\) \(count (\d+)\)\)'
                            . ' '
                            . '\(focus \(target (none|l \d+|r \d+)\) \(count (\d+)\)\)'
                            . ' '
                            . '\(tackle \(expires (\d+)\) \(count (\d+)\)\)'
                            . ' '
                            . '\(collision (none|\(ball\)|\(player\)|\(post\)|\(ball\) \(player\)|\(ball\) \(post\)|\(player\) \(post\)|\(ball\) \(player\) \(post\))\)'
                            . ' '
                            . '\(foul  \(charged (\d+)\) \(card (none|yellow|red)\)\)'
                            . '/'
                            , $sb, $matches)) {
            $body->setParams($matches);
        } else {
            throw new \Exception('could not parse ' . $sb);
        }
        return $body;
    }
}