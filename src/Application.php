<?php
namespace Tesoon\Foundation;

/**
 * 当前应用信息模型
 */
class Application{

    /**
     * 应用id
     */
    public $id;

    /**
     * 应用key
     */
    public $key;

    public function __construct(string $id, string $key)
    {
        $this->id = $id;
        $this->key = $key;
    }

}