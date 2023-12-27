<?php
namespace Tesoon\Foundation;

/**
 * 该接口提供一个抽象实现
 * 出于安全考虑，具体实现不在本项目中体现
 * @author doowan
 */
interface Signature{

    /**
     * 通过指定Application以及data来计算签名
     * @param Application $application
     * @param int $timestamp
     * @param array $data
     * @return string
     */
    public function encrypt(Application $application, int $timestamp, array $data): string;


    /**
     * 检查指定authentication是否有效
     * @param Application $application
     * @param Authentication $authentication
     * @return bool
     */
    public function check(Application $application, Authentication $authentication): bool;

}