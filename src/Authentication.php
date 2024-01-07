<?php
namespace Tesoon\Foundation;

use Lcobucci\JWT\Token;

/**
 * header中的authentication数据模型
 */
class Authentication extends GeneralObject{

    /**
     * @var string
     */
    public $signature = '';

    /**
     * @var int
     */
    public $timestamp = 0;

    /**
     * jwt中的claims数据
     * @var mixed
     */
    private $data = [];

    /**
     * @var Token
     */
    private $token = null;

    public function setBody(Token $token, $data){
        $this->token = $token;
        $this->data = $data;
    }

    /**
     * @return Token
     */
    public function getToken(): Token{
        return $this->token;
    }

    public function getBody(){
        return $this->data;
    }

}