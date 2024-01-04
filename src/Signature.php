<?php
namespace Tesoon\Foundation;

use Tesoon\Foundation\Exceptions\TokenVerifyException;

/**
 * 该接口提供一个抽象实现
 * 出于安全考虑，具体实现不在本项目中体现
 * @author doowan
 */
interface Signature{

    /**
     * 计算签名
     * @parma SignatureSetting $setting
     * @return Authentication
     * @throw TokenException
     */
    public function encrypt(SignatureSetting $setting): Authentication;

    /**
     * 检查指定authentication是否有效
     * @param Authentication $authentication
     * @return bool
     * @throws TokenVerifyException
     */
    public function check(Authentication $authentication): bool;

}