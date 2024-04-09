<?php
namespace Tesoon\Foundation\Exceptions;

use Exception;

/**
 * 数据异常
 */
class DataException extends Exception{

    /**
     * Data 子类名
     * @var string
     */
    public $class;

    /**
     * @var array|string
     */
    public $data;

    public function __construct(string $class, $data, string $message){
        $this->class = $class;
        $this->data = $data;
        parent::__construct($message);
    }

}