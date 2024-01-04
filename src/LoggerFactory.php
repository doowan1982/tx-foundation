<?php
namespace Tesoon\Foundation;

class LoggerFactory{

    private static $loggers;

    public function export(){
        foreach(static::$loggers as $class => $logger){
            
        }
    }

    public function __construct(){
        
    }

    /**
     * @param string|object
     * @return Logger
     */
    public function getLogger($class): Logger{
        if(is_object($class)){
            $class = get_class($class);
        }
        if(!isset(static::$loggers[$class])){
            static::$loggers[$class] = new Logger($class);
        }
        return static::$loggers[$class];
    }

}