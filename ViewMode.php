<?php
namespace ThroughBall;
class ViewMode extends BodyItem {
    private $currentparam = 0;
    protected $name = 'view_mode';
    private $paramnames = array(
        'quality', // either high or low
        'width', // narrow, normal or wide
    );
    protected $params = array(
        'quality' => false,
        'width' => false,
    );
    function setValue($value)
    {
        $this->params[$this->paramnames[$this->currentparam++]] = $value;
    }
}
