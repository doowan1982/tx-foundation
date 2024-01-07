<?php
namespace Tesoon\Foundation;

abstract class GeneralObject{

    /**
     * @return Logger
     */
    public function logger(): Logger{
        return LoggerFactory::logger();
    }

}