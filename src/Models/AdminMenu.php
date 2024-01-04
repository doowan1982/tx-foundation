<?php
namespace Tesoon\Foundation\Models;

class AdminMenu extends Data
{
    /**
     * @var int
     */
    public $adminId;

    /**
     * @var array
     */
    public $menus = [];
    
    public static function create($data): Data{
        $list = new Lists();
        foreach($data as $key => $value){
            $obj = new static();
            $obj->adminId = $key;
            $obj->menus = $value;
            $list->add($obj);
        }
        return $list;
    }

}