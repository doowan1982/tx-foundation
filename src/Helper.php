<?php
namespace Tesoon\Foundation;

use DateTimeImmutable;
use DateTimeZone;

class Helper{

    /**
     * 计算数组的MD5
     * @return string
     */
    public static function computeMD5(array $parameters): string{
        $string = '';
        ksort($parameters);
        foreach($parameters as $name=>$parameter){
            if(!is_array($parameter)){
                $string .= "{$name}={$parameter}";
            }else{
                //此处防止字符过长存在的内存占用问题
                $string = md5($string.static::computeMD5($parameter));
            }
        }
        return md5($string);
    }

    /**
     * @return DateTimeImmutable
     * @throws Exception
     */
    public static function getDateTimeImmutable(): DateTimeImmutable{
        return new DateTimeImmutable('now', new DateTimeZone('Asia/Shanghai'));
    }

}