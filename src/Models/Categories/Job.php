<?php


namespace Tesoon\Foundation\Models\Categories;

use Tesoon\Foundation\Models\Category;
use Tesoon\Foundation\Models\Data;
use Tesoon\Foundation\Models\Lists;

class Job extends Category
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $sequence;

    public function __construct(int $id = 0, string $name = '', int $sequence = 0){
        $this->id = $id;
        $this->name = $name;
        $this->sequence = $sequence;
    }
}