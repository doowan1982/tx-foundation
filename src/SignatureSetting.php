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

    /**
     * @var array
     */
    private $claims;

    public function setSignature(string $signature): SignatureSetting{
        $this->setClaim(Token::SIGNATURE, $signature);
        return $this;
    }

    public function setClaim(string $name, $value): SignatureSetting{
        $this->claims[$name] = $value;
        return $this;
    }

    public function getClaims(): array{
        return $this->claims;
    }

}