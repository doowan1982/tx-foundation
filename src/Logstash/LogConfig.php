<?php

namespace Tesoon\Foundation\Logstash;

class LogConfig
{
    /**
     * 日志名称
     * @var string
     */
    public $name = 'main';

    public $level = \Monolog\Logger::DEBUG;

    /**
     * 定义的handler
     * @var array
     * @see \Monolog\Logger setHandler()
     */
    public $handlers = [];

    /**
     * @var array
     * @see \Monolog\Logger pushProcessor()
     */
    public $callables = [];

}