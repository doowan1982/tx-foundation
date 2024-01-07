<?php
namespace Tesoon\Foundation\Models;

use ReflectionClass;
use ReflectionProperty;
use stdClass;
use Tesoon\Foundation\Exceptions\DataException;

/**
 * 对于该子类的的实现构造方法的参数需为无参或者具有默认值的参数
 */
abstract class Data{
    
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
        static::setObjectValues($object, $data);
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
     * 将给定的$data绑定到$object中
     * @param Data $object
     * @param mixed $data
     */
    public static function setObjectValues(Data $object, $data){
        $reflectionClass = new ReflectionClass($object);
        $mapper = $object->maps();
        if($data instanceof stdClass){
            $data = (array)$data;
        }
        if(is_array($data)){
            foreach($data as $key => $value){
                $key = $mapper[$key] ?? $key;
                $key = static::convertCamelCase($key);
                $property = null;
                if(!$reflectionClass->hasProperty($key)){
                    continue; //不存在的属性不再进行绑定
                }

                $property = $reflectionClass->getProperty($key);
                if(!$property instanceof ReflectionProperty){
                    throw new DataException(static::class, $data, "{$key}不是一个有效的类属性");
                }
                if(!$property->isPublic()){
                    throw new DataException(static::class, $data, "{$key}不是一个公共属性");
                }
                $object->setValue($key, $value);
            }
        }else{
            throw new DataException(static::class, $data, "仅支持数组动态赋值");
        }
    }

    /**
     * 追加Data模型到列表中
     * @param string $key
     * @param array $value
     */
    protected function setListValues(string $key, array $values){
        $class = $this->$key->getClass();
        if(!$class || !is_subclass_of($class, Data::class)){
            throw new DataException(static::class, $values, "设置列表数据需指明类型为Data的子类[{$class}]");
        }
        foreach($values as $value){
            $obj = new $class();
            static::setObjectValues($obj, $value);
            $this->$key->add($obj);
        }
    }

    protected function createByArray(string $key, array $value){
        if($this->$key instanceof Data){
            static::setObjectValues($this->$key, $value);
            return;
        }
        $this->$key = $value;
    }

    protected function createByClass(string $key, object $value){
        if($this->$key instanceof Data){
            static::setObjectValues($this->$key, $value);
            return;
        }
        $this->$key = $value;
    }


    private static function convertCamelCase(string $str): string{
        $str = ucwords(str_replace(['-', '_'], ' ', $str));
        return lcfirst(str_replace(' ', '', $str));
    }

}