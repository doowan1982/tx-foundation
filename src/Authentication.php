<?php
namespace Tesoon\Foundation;

/**
 * header中的authentication数据模型
 */
class Authentication{

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
    public $data = [];

}