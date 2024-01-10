<?php


namespace Tesoon\Foundation\Models;

use Tesoon\Foundation\Helper;

/**
 * 菜单数据
 * Class Menu
 * @package Tesoon\Foundation\Models
 */
class Menu extends Data
{
    const DIR = 'dir';
    const MENU = 'menu';

    public $id;

    public $name;

    public $route;

    public $icon;

    public $order;

    public $type = self::DIR;

    /**
     * @var int
     */
    public $parent;

    /**
     * 子菜单
     * @var Lists
     */
    public $menus;

    public function maps(): array{
        return [
            'href' => 'route',
            'show_order' => 'order',
            'pid' => 'parent',
        ];
    }

    public static function create($data):Data {
        $list = new Lists();
        foreach($data as $value){
            $menu = new Menu();
            Helper::setObjectValues($menu, $value);
            if($value['type'] > 1){
                $menu->type = Menu::MENU;
            }
            $menu->menus = static::create($value['childs'] ?? []);
            $list->add($menu);
        }
        return $list;
    }
}