<?php
namespace Tesoon\Foundation\Models;

abstract class Category extends Data{

    public $id;

    public $name;

    public $uniqueName;

    public $sequence = 0;

    private $maps = [];

    public function __construct($maps = [])
    {
        $this->maps = $maps;
    }

    /**
     * @inheritdoc
     */
    public function maps(): array{
        return $this->maps;
    }

}