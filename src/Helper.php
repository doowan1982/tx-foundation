<?php
namespace Tesoon\Foundation;

use DateTimeImmutable;
use DateTimeZone;
use Tesoon\Foundation\Exceptions\DataException;
use Tesoon\Foundation\Models\Data;

class Helper{

    /**
     * 计算数组的MD5
     * @param array $parameters
     * @return string
     */
    public static function computeMD5(array $parameters): string{
        $strings = [];
        ksort($parameters);
        foreach($parameters as $name=>$parameter){
            if(!is_array($parameter)){
                if(is_numeric($parameter) || is_bool($parameter)){
                    $parameter = (string)$parameter;
                }
                $strings[] = "{$name}={$parameter}";
            }else{
                //此处防止字符过长存在的内存占用问题
                $strings[] = md5(static::computeMD5($parameter));
            }
        }
        return md5(implode('', $strings));
    }

    /**
     * @return DateTimeImmutable
     * @throws Exception
     */
    public static function getDateTimeImmutable(): DateTimeImmutable{
        return new DateTimeImmutable('now', new DateTimeZone('Asia/Shanghai'));
    }

    /**
     * 将给定的$data绑定到$object中
     * @param Data $object
     * @param mixed $data
     * @return Data
     * @throws \ReflectionException
     */
    public static function setObjectValues(Data $object, $data): Data{
        $reflectionClass = new \ReflectionClass($object);
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
                if(!$property instanceof \ReflectionProperty){
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
        return $object;
    }

    private static function convertCamelCase(string $str): string{
        $str = ucwords(str_replace(['-', '_'], ' ', $str));
        return lcfirst(str_replace(' ', '', $str));
    }


}