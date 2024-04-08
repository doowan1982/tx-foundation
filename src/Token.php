<?php
namespace Tesoon\Foundation;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Parsing\Decoder;
use Lcobucci\JWT\Parsing\Encoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token as JWTToken;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Tesoon\Foundation\Exceptions\TokenException;
use Tesoon\Foundation\Exceptions\TokenVerifyException;

class Token extends GeneralObject implements Signature {

    const DATA = 'data';
    const SIGNATURE = 'signature';

    /**
     * @inheritDoc
     */
    public function encrypt(Application $application, SignatureSetting $setting): Authentication{
        $authentication = new Authentication();
        $builder = new Builder(new Encoder());
        try{
            $now = Helper::getDateTimeImmutable();
            $authentication->timestamp = $now->getTimestamp();
            $builder->issuedBy($setting->issuer)
                ->issuedAt($now)
                ->identifiedBy($this->getId($application, $authentication->timestamp))
                ->withClaim(static::DATA, $setting->getClaims());
            if($setting->enableTime){
                $builder->canOnlyBeUsedAfter($now->modify($setting->enableTime));
            }
            if($setting->expiredTime){
                $builder->expiresAt($now->modify($setting->expiredTime));
            }
            foreach($setting->headers as $name => $value){
                $builder->withHeader($name, $value);
            }
            $authentication->signature = $builder->getToken($setting->signer ?? new Sha256(), InMemory::plainText(random_bytes(32), $application->key));
            static::logger()->info('JWT加密', [
                'application_id' => $application->id,
                'signature' => $authentication->signature
            ]);
        }catch(\Exception $e){
            throw new TokenException($e->getMessage(), $e->getCode(), $e);
        }
        return $authentication;
    }

    /**
     * @inheritDoc
     */
    public function decrypt(Authentication $authentication, Application $application): bool{
        try{
            $authentication->setToken($this->parseByTicket($authentication->signature));
            static::logger()->info('JWT解密', [
                'application_id' => $application->id,
                'signature' => $authentication->signature
            ]);
        }catch(\Exception $e){
            throw new TokenException($e->getMessage(), $e->getCode(), $e);
        }
        return true;
    }

    /**
     * @param Application $application
     * @param int $timestamp
     * @return string
     */
    public function getId(Application $application, int $timestamp): string{
        return md5(hash_hmac('sha384', $application->key.$timestamp, $application->key));
    }

    /**
     * @param string $ticket
     * @return JWTToken
     * @throws TokenVerifyException
     */
    private function parseByTicket(string $ticket): JWTToken{
        try{
            return (new Parser(new Decoder()))->parse($ticket);
        }catch(CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e){
            static::logger()->warning('解析JWT数据失败', [
                '$ticket' => $ticket,
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
            throw new TokenVerifyException($e->getMessage(), $e->getCode(), $e);
        }
    }

}