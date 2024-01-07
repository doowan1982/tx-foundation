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
use Tesoon\Foundation\Exceptions\TokenException;
use Tesoon\Foundation\Exceptions\TokenVerifyException;

class Token extends GeneralObject implements Signature {
    
    const DATA = 'signature';

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
                ->identifiedBy($this->getId($application, $authentication->timestamp));
            foreach($setting->getClaims() as $name => $value){
                $builder->withClaim($name, $value);
            }
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
        }catch(Exception $e){
            throw new TokenException($e->getMessage(), $e->getCode(), $e);
        }
        return $authentication;
    }

    /**
     * @inheritDoc
     */
    public function decrypt(Authentication $authentication, Application $application): bool{
        try{
            $ticket = $authentication->signature;
            $token = $this->parseByTicket($ticket);
            $data = $token->claims()->get(static::DATA);
            if($data instanceof \stdClass){
                //stdClass convert array
                $data = json_decode(json_encode($token->claims()->get(static::DATA)), true);
            }
            $authentication->setBody($token, $data);
        }catch(Exception $e){
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
        return md5($application->key.$timestamp);
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
            throw new TokenVerifyException($e->getMessage(), $e->getCode(), $e);
        }
    }

}