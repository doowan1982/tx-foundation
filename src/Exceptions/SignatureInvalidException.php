<?php
namespace Tesoon\Foundation\Exceptions;

use Tesoon\Foundation\Authentication;

class SignatureInvalidException extends \Exception{

    private $authentication;

    public function __construct(Authentication $authentication, string $message)
    {
        parent::__construct($message, 403);
        $this->authentication = $authentication;
    }

    /**
     * @return Authentication
     */
    public function getAuthentication(): Authentication{
        return $this->authentication;
    }

}