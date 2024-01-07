<?php
namespace Tesoon\Foundation;

class Logger{

    const DEBUG = 8;
    const INFO = 4;
    const WARNING = 2;
    const ERROR = 1;

    private $message = [];

    public function debug(string $message, array $config = []){
        $this->message[static::DEBUG][] = $message;
    }

    public function info(string $message, array $config = []){
        $this->message[static::INFO][] = $message;
    }

    public function warning(string $message, array $config = []){
        $this->message[static::WARNING][] = $message;
    }

    public function error(string $message, array $config = []){
        $this->message[static::ERROR][] = $message;
    }

    /**
     * @return array
     */
    public function getLogs(): array{
        return $this->message;
    }

    protected function format(string $message, array $config): string{
        return '';  
    }

}