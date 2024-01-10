<?php
namespace Tesoon\Foundation\Models;

use stdClass;
use Tesoon\Foundation\Exceptions\DataException;
use Tesoon\Foundation\GeneralObject;
use Tesoon\Foundation\Helper;

/**
 * 对于该子类的的实现构造方法的参数需为无参或者具有默认值的参数
 */
abstract class Data extends GeneralObject {
    
    /**
     * @param array|string $data
     * @return Data
     * @throws DataException
     */
    public static function create($data): Data{
        $object = new static();
        if($data instanceof stdClass){
            $data = (array)$data;
        }
        Helper::setObjectValues($object, $data);
        return $object;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @throws DataException
     */
    public function setValue(string $key, $value){
        if($this->$key instanceof Lists){
            $this->setListValues($key, $value);
        }if(is_array($value)){
            $this->createByArray($key, $value);
        }else if(is_object($value)){
            $this->createByClass($key, $value);
        }else{
            $this->$key = $value;
        }
    }

    public function __set($name, $value){
        $class = get_class($this);
        throw new DataException(static::class, $value, "{$class}中不存在或者无法访问{$name}属性");
    }

    public function __toString(){
        return get_class($this);
    }

    /**
     * 返回create()时属性名称与参数的映射关系
     * [
     *     'adminId' => 'id'
     * ]
     * @return array
     */
    public function maps(): array{
        return [];
    }

    /**
     * 追加Data模型到列表中
     * @param string $key
     * @param array $value
     * @throws DataException
     */
    protected function setListValues(string $key, array $values){
        $class = $this->$key->getClass();
        if(!$class || !is_subclass_of($class, Data::class)){
            throw new DataException(static::class, $values, "设置列表数据需指明类型为Data的子类[{$class}]");
        }
        foreach($values as $value){
            $obj = new $class();
            Helper::setObjectValues($obj, $value);
            $this->$key->add($obj);
        }
    }

    protected function createByArray(string $key, array $value){
        if($this->$key instanceof Data){
            Helper::setObjectValues($this->$key, $value);
            return;
        }
        $this->$key = $value;
    }

    protected function createByClass(string $key, object $value){
        if($this->$key instanceof Data){
            Helper::setObjectValues($this->$key, $value);
            return;
        }
        $this->$key = $value;
    }

}