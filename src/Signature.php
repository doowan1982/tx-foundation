<?php
namespace Tesoon\Foundation;

use Tesoon\Foundation\Exceptions\TokenException;
use Tesoon\Foundation\Exceptions\TokenVerifyException;

/**
 * 该接口提供一个抽象实现
 * 出于安全考虑，具体实现不在本项目中体现
 * @author doowan
 */
interface Signature{

    /**
     * 计算签名
     * @param Application $application
     * @param SignatureSetting $setting
     * @return Authentication
     * @throws TokenException
     */
    public function encrypt(Application $application, SignatureSetting $setting): Authentication;

    /**
     * 检查指定authentication是否有效
     * @param Authentication $authentication
     * @param Application $application
     * @return bool
     * @throws TokenException
     */
    public function decrypt(Authentication $authentication, Application $application): bool;

    /**
     * 生成jti唯一标识
     * @param Application $application
     * @param int $timestamp
     * @return string
     */
    public function getId(Application $application, int $timestamp): string;

}