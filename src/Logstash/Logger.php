<?php
namespace Tesoon\Foundation\Logstash;

use DateTimeZone;
use Monolog\Logger as MonoLogger;

class Logger extends MonoLogger{

    private $log;

    public function __construct(LogConfig $config){
        parent::__construct($config->name, $config->handlers, $config->callables, new DateTimeZone('Asia/Shanghai'));
    }


}