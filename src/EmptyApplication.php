<?php
namespace Tesoon\Foundation;

final class EmptyApplication extends Application{

    public static $instance;

    public static function instance(): EmptyApplication{
        if(self::$instance === null){
            self::$instance = new static('0','empty application');
        }
        return self::$instance;
    }

}