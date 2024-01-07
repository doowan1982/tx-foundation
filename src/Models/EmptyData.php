<?php
namespace Tesoon\Foundation\Models;

/**
 * 通过返回的数据未找到匹配的Data，则返回该对象
 * Class EmptyData
 * @package Tesoon\Foundation\Models
 * @see DataFactory build()
 */
final class EmptyData extends Data
{
    private static $instance;

    private function __construct(){}

    /**
     * @inheritDoc
     */
    public static function create($data = []): Data{
        if(static::$instance === null){
            static::$instance = new static();
        }
        return static::$instance;
    }
}