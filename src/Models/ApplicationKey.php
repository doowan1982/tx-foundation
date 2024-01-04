<?php


namespace Tesoon\Foundation\Models;

use Tesoon\Foundation\Exceptions\DataException;

class ApplicationKey extends Data
{

    public $key;

    public function __construct(string $key){
        $this->key = $key;
    }

    public static function create($data): Data{
        if(!is_string($data)){
            throw new DataException(static::class, $data, "Key值必须为一个字符串");
        }
        return new ApplicationKey($data);
    }

}