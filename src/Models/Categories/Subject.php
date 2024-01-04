<?php
namespace Tesoon\Foundation\Models\Categories;

use Tesoon\Foundation\Models\Category;

class Subject extends Category{

    public function __construct()
    {
        parent::__construct([
            'subject_name' => 'name',
            'subject_id' => 'id'
        ]);
    }

}