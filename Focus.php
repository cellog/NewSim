<?php
namespace ThroughBall;
class Focus extends BodyItem {
    private $currentparam = 0;
    private $paramnames = array(
        'target',
        'count',
    );
    protected $params = array(
        'target' => false,
        'count' => false,
        'unum' => false
    );

    function setValue($value)
    {
        if ($this->params['target'] == 'l' || $this->params['target'] == 'r') {
            $this->params['unum'] = $value + 0;
            return; // a little hack allowing the odd shaping of (target none) and (target l 3)
        }
        $this->params[$this->paramnames[$this->currentparam++]] = $value + 0;
    }
}
