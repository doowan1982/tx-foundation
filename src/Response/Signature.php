<?php
namespace Tesoon\Foundation\Response;

use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;
use Lcobucci\JWT\Validation\Validator;
use Tesoon\Foundation\Authentication;
use Tesoon\Foundation\Exceptions\TokenVerifyException;

/**
 * 默认实现
 * Class Signature
 * @package Tesoon\Tests
 */
class Signature extends \Tesoon\Foundation\Token {

    /**
     * @inheritDoc
     */
    protected function getId(int $timestamp): string{
        return $this->application->key.$timestamp;
    }

    /**
     * @inheritDoc
     */
    public function check(Authentication $authentication): bool{
        $token = $this->parseByTicket($authentication->signature);
        $claims = $token->claims();
        $nbf = $claims->get(RegisteredClaims::ISSUED_AT);
        $validator = new Validator();
        if(!$validator->validate($token, new IdentifiedBy($this->getId($nbf->getTimestamp())))){
            throw new TokenVerifyException('无效的令牌');
        }
        return parent::check($authentication);
    }

}