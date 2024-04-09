<?php
namespace Tesoon\Foundation\Request\Transports;

use Tesoon\Foundation\Header;
use Tesoon\Foundation\Parameter;
use Tesoon\Foundation\Request\QueryParameter;
use Tesoon\Foundation\Request\Transport;
use Tesoon\Foundation\Response\ResponseBody;

class Abilities extends Transport{

    /**
     * 验证请求参数
     * @param string $authentication
     * @param array $body
     * @return ResponseBody
     */
    public function verifySign(string $authentication, array $body): ResponseBody{
        $this->setUri("v2/abilities/verify-sign")
                ->setParameter(Header::create("authentication={$authentication}"));
        foreach($body as $name => $value){
            $this->setParameter(new Parameter($name, $value));
        }
        return $this->lastSend();
    }

    /**
     * 需要与外部应用通讯时自动进行签名以及参数hash
     * @param array $query get参数 键值形式
     * @param array $body post参数 键值形式
     * @param array $headers 请求头信息 键值形式
     */
    public function sendWarpper(array $query, array $body = [], array $headers = []): ResponseBody{
        foreach($query as $name=>$value){
            $this->setParameter(QueryParameter::create("{$name}={$value}"));
        }
        foreach($body as $name=>$value){
            $this->setParameter(new Parameter($name, $value));
        }
        foreach($headers as $name=>$value){
            $this->setParameter(Header::create("{$name}={$value}"));
        }
        return $this->send();
    }

}