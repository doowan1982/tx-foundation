<?php
namespace Tesoon\Foundation;

use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;
use Lcobucci\JWT\Validation\Validator;
use Tesoon\Foundation\Exceptions\DataException;
use Tesoon\Foundation\Exceptions\TokenException;
use Tesoon\Foundation\Exceptions\TokenVerifyException;
use Tesoon\Foundation\Exceptions\SignatureInvalidException;
use Tesoon\Foundation\Response\ResponseBody;

class Decoder{
    private $signature;
    private $application;

    /**
     * @param Application $application 当前应用标识
     * @param Signature $signature
     */
    public function __construct(Application $application, Signature $signature)
    {
        $this->application = $application;
        $this->signature = $signature;
    }

    /**
     * @param Authentication $authentication
     * @param Application|null $outer 外部应用，如果不存在则使用$this->application来进行功能解密、验证、拉取数据
     * @param array $responseParameters 该参数组合顺序需为：$_GET + $_POST形式
     * @return ResponseBody
     * @throws DataException
     * @throws SignatureInvalidException
     * @throws TokenException
     */
    public function get(Authentication $authentication, Application $outer = null, array $responseParameters = []): ResponseBody{
        if($outer === null){
            $outer = $this->application;
        }
        if(!$this->signature->decrypt($authentication, $outer)){
            throw new SignatureInvalidException($authentication, '验证失败', Constant::DATA_VALIDATION_FAILED_RESPONSE_CODE);
        }
        $signature = $authentication->getBody(Token::SIGNATURE);
        if($signature !== Helper::computeMD5($responseParameters)){
            throw new SignatureInvalidException($authentication, '数据校验失败', Constant::DATA_VALIDATION_FAILED_RESPONSE_CODE);
        }

        $this->check($authentication, $outer);

        $responseBody = $this->getResponseBody();
        $responseBody->setResponseParameters($responseParameters);
        return $responseBody;
    }

    /**
     * 验证令牌参数
     * @param Authentication $authentication
     * @param Application $application
     * @return bool
     * @throws TokenVerifyException
     */
    protected function check(Authentication $authentication, Application $application): bool{
        if($this->isValid($authentication, $application)){
            throw new TokenVerifyException('无效的令牌', Constant::INVALID_TOKEN_RESPONSE_CODE);
        }
        if($this->isExpired($authentication)){
            throw new TokenVerifyException('令牌已过期', Constant::TOKEN_HAS_EXPRIED_RESPONSE_CODE);
        }
        if($this->isArrived($authentication)){
            throw new TokenVerifyException('令牌未达到有效使用时间', Constant::TOEKN_NOT_USE_RESPONSE_CODE);
        }
        return true;
    }

    /**
     * 令牌是否有效
     * @param Authentication $authentication
     * @param Application $application
     * @return bool
     */
    protected function isValid(Authentication $authentication, Application $application): bool{
        $token = $authentication->getToken();
        $claims = $token->claims();
        $nbf = $claims->get(RegisteredClaims::ISSUED_AT);
        $validator = new Validator();
        return !$validator->validate($token, new IdentifiedBy($this->signature->getId($application, $nbf->getTimestamp())));
    }

    /**
     * 是否过期
     * @param Authentication $authentication
     * @return bool 如果已过期则为true
     */
    protected function isExpired(Authentication $authentication): bool{
        return $authentication->getToken()->isExpired(Helper::getDateTimeImmutable());
    }

    /**
     * 是否到达预设使用时间
     * @param Authentication $authentication
     * @return bool 如果到达则为true
     */
    protected function isArrived(Authentication $authentication): bool{
        return !$authentication->getToken()->isMinimumTimeBefore(Helper::getDateTimeImmutable());
    }

    protected function getResponseBody(): ResponseBody{
        return new ResponseBody();
    }
}