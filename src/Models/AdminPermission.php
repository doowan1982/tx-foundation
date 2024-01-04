<?php
namespace Tesoon\Foundation\Models;

class AdminPermission extends Data{

    /**
     * @var int
     */
    public $adminId;

    /**
     * @var array
     */
    public $routes = [];

    /**
     * @var array
     */
    public $roles = [];

    public static function create($data): Data{
        $list = new Lists();
        foreach($data as $key => $value){
            $obj = new static();
            $obj->adminId = $key;
            $obj->routes = $value;
            $list->add($obj);
        }
        return $list;
    }

}