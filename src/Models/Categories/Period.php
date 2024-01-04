<?php
namespace Tesoon\Foundation\Models\Categories;

use Tesoon\Foundation\Models\Category;

class Period extends Category{

    public function __construct()
    {
        parent::__construct([
            'period_id' => 'id',
            'period_name' => 'name',
        ]);
    }

}