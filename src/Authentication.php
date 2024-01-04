<?php
namespace Tesoon\Foundation;

/**
 * header中的authentication数据模型
 */
class Authentication extends GeneralObject{

    /**
     * @var string
     */
    public $signature = '';

    /**
     * @var int
     */
    public $timestamp = 0;

    /**
     * @var array
     */
    private $data = [];

    public function setBody(array $data){
        $this->data = $data;
    }

    public function getBody():array{
        return $this->data;
    }

}