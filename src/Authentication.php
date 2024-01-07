<?php
namespace Tesoon\Foundation;

use Lcobucci\JWT\Token;

/**
 * header中的authentication数据模型
 */
class Authentication extends GeneralObject{

    /**
     * encrypt之后的签名字符串
     * @var string
     * @see \Tesoon\Foundation\Token encrypt()
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

    /**
     * @param Token $token
     */
    public function setToken(Token $token){
        $this->token = $token;
    }

    /**
     * @return Token
     */
    public function getToken(): Token{
        return $this->token;
    }

    /**
     * 获取claims数据
     * @param string $name 如果为空则返回所有
     * @return mixed|null
     */
    public function getBody(string $name = ''){
        $claims = $this->token->claims()->get(\Tesoon\Foundation\Token::DATA);
        if(!$name){
            return $claims;
        }
        if(isset($claims->$name)){
            return $claims->$name;
        }
        return null;
    }

}