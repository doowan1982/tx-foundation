<?php
namespace Tesoon\Foundation;

class LoggerFactory{

    private static $logger;

    /**
     * @return Logger
     */
    public static function logger(): Logger{
        if(static::$logger === null){
            static::$logger = new Logger();
        }
        return static::$logger;
    }

}