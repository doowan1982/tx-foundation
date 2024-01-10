<?php
namespace Tesoon\Foundation\Logstash;

use Tesoon\Foundation\Context;

class LoggerFactory{

    const MAIN = 'main';

    private static $loggers;

    /**
     * @param string $name
     * @return Logger
     */
    public static function logger(string $name = LoggerFactory::MAIN): Logger{
        if(!isset(static::$loggers[static::MAIN])){
            static::$loggers[LoggerFactory::MAIN] = new Logger(Context::instance()->getLogConfig());
        }
        if($name !== LoggerFactory::MAIN && !isset($static[$name])){
            static::$loggers[$name] = static::$loggers[LoggerFactory::MAIN]->withName($name);
        }
        return static::$loggers[$name];
    }

}