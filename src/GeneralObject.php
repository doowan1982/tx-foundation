<?php
namespace Tesoon\Foundation;

use Tesoon\Foundation\Logstash\Logger;
use Tesoon\Foundation\Logstash\LoggerFactory;

abstract class GeneralObject{

    /**
     * @return Logger
     */
    public static function logger(): Logger{
        return LoggerFactory::logger(static::class);
    }

}