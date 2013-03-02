<?php
namespace ThroughBall\Util;
abstract class UDP
{
    protected $sock;
    protected $port;
    protected $host;
    protected $team;
    protected $initialized = false;
    function __construct($team, $host = '127.0.0.1', $port = 6000)
    {
        $this->team = $team;
        $this->host = $host;
        $this->port = $port;
        $this->sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    }

    function __destruct()
    {
        $this->send('(bye)');
        socket_close($this->sock);
    }

    function send($text)
    {
        $len = strlen($text);

        socket_sendto($this->sock, $text, $len, 0, $this->host, $this->port);
    }

    function receive()
    {
        socket_recvfrom($this->sock, $buf, 5000, 0, $this->host, $this->port);
        return $buf;
    }

    function init()
    {
        if ($this->initialized) return;
        $this->send($this->getInitString());
        $this->initialized = true;
    }

    function parse($string)
    {
        echo $string, "\n\n\n";
    }

    abstract function getInitString();
}
