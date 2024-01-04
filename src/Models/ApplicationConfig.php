<?php


namespace Tesoon\Foundation\Models;

/**
 * 系统配置
 * Class ApplicationConfig
 * @package Tesoon\Foundation\Models
 */
class ApplicationConfig extends Data
{

    /**
     * 所在模块
     * @var string
     */
    public $module;

    /**
     * 配置项列表
     * @var array
     */
    public $items = [];

}