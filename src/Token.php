<?php
namespace Tesoon\Foundation;

use \Exception;
use \DateTimeZone;
use DateTimeImmutable;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Parsing\Decoder;
use Lcobucci\JWT\Parsing\Encoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token as JWTToken;
use Lcobucci\JWT\Token\DataSet;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\Validation\Validator;
use Tesoon\Foundation\Exceptions\TokenException;
use Tesoon\Foundation\Exceptions\TokenVerifyException;
use Tesoon\Foundation\Models\Data;

abstract class Token extends GeneralObject implements Signature {
    /**
     * @var Application
     */
    public $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * 创建签名
     * @param SignatureSetting $setting
     * @return Authentication
     * @throws TokenException
     */
    public function encrypt(SignatureSetting $setting): Authentication{
        $authentication = new Authentication();
        $builder = new Builder(new Encoder());
        try{
            $now = $this->getDateTimeImmutable();
            $authentication->timestamp = $now->getTimestamp();
            $builder->issuedBy($setting->issuer)
                ->issuedAt($now)
                ->identifiedBy($this->getId($authentication->timestamp))
                ->withClaim(Data::DATA, $setting->claims);
            if($setting->enableTime){
                $builder->canOnlyBeUsedAfter($now->modify($setting->enableTime));
            }
            if($setting->expiredTime){
                $builder->expiresAt($now->modify($setting->expiredTime));
            }
            foreach($setting->headers as $name => $value){
                $builder->withHeader($name, $value);
            }
            $authentication->signature = $builder->getToken($setting->signer ?? new Sha256(), InMemory::plainText(random_bytes(32), $this->application->key));
        }catch(Exception $e){
            throw new TokenException($e->getMessage(), $e->getCode(), $e);
        }
        return $authentication;
    }

    /**
     * @inheritDoc
     */
    public function check(Authentication $authentication): bool{
        try{
            $ticket = $authentication->signature;
            $token = $this->parseByTicket($ticket);
            $validator = new Validator();
            if($this->isExpired($token)){
                throw new TokenVerifyException('令牌已过期');
            }
            if($this->isArrived($token)){
                throw new TokenVerifyException('令牌未达到有效使用时间');
            }
            //stdClass convert array
            $authentication->setBody(json_decode(json_encode($token->claims()->get(Data::DATA)), true));
        }catch(Exception $e){
            throw new TokenVerifyException($e->getMessage(), $e->getCode(), $e);
        }
        return true;
    }


    /**
     * 如果失败返回false
     * @param string $ticket
     * @return DataSet
     * @throws TokenVerifyException
     */
    protected function parse(string $ticket): DataSet{
        try{
            return $this->parseByTicket($ticket)->claims();
        }catch(CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e){
            throw new TokenVerifyException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private $token;

    /**
     * @param string $ticket
     * @return JWTToken
     * @throws TokenVerifyException
     */
    public function parseByTicket(string $ticket): JWTToken{
        if($this->token != null){
            return $this->token;
        }
        try{
            return $this->token = (new Parser(new Decoder()))->parse($ticket);
        }catch(CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e){
            throw new TokenVerifyException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $timestamp
     * @return string
     */
    protected abstract function getId(int $timestamp): string;

    /**
     * 是否过期
     * @param JWTToken $token
     * @return bool 如果已过期则为true
     * @throws Exception
     */
    protected function isExpired(JWTToken $token): bool{
        return $token->isExpired($this->getDateTimeImmutable());
    }

    /**
     * 是否到达预设使用时间
     * @param JWTToken $token
     * @return bool 如果到达则为true
     * @throws Exception
     */
    protected function isArrived(JWTToken $token): bool{
        return !$token->isMinimumTimeBefore($this->getDateTimeImmutable());
    }

    /**
     * @return DateTimeImmutable
     * @throws Exception
     */
    private function getDateTimeImmutable(): DateTimeImmutable{
        return new DateTimeImmutable('now', new DateTimeZone('Asia/Shanghai'));
    }

}