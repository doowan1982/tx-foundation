<?php
namespace Tesoon\Foundation;

class Parameter extends GeneralObject{

    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string $query
     * @return Parameter
     */
    public static function create(string $query): Parameter{
        $query = explode('=', preg_replace('/\s/', '', $query));
        return new static($query[0], $query[1]);
    }

    public function __construct(string $name, $value, $isGet = true){
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName(): string{
        return $this->name;
    }

    /**
     * @var mixed
     */
    public function getValue(){
        return $this->value;
    }

}