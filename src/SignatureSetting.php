<?php
namespace Tesoon\Foundation;

use Lcobucci\JWT\Signer\Key\Signer;

/**
 * 计算签名参数
 * Class SignatureSetting
 * @package Tesoon\Foundation
 */
class SignatureSetting extends GeneralObject
{

    public $issuer = 'https://www.tesoon.com';

    /**
     * @var array
     */
    public $claims = [];

    /**
     * @var array
     */
    public $headers = [];

    /**
     * @var string
     */
    public $expiredTime = '+2 seconds';

    /**
     * 签名的启用时间，默认为当前时间
     * @var string
     */
    public $enableTime = '';

    /**
     * @var Signer
     */
    public $signer; 

}