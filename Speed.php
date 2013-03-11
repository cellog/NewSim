<?php
namespace ThroughBall;
class Speed extends Stamina {
    private $currentparam = 0;
    protected $name = 'speed';
    private $paramnames = array(
        'speed', // speed
        'direction', // -180 to 180 degrees
    );
    protected $params = array(
        'speed' => false,
        'direction' => false,
    );
}
