<?php
namespace Tesoon\Foundation;

abstract class GeneralObject{

    /**
     * @return Logger
     */
    public static function logger(): Logger{
        return LoggerFactory::logger();
    }

}