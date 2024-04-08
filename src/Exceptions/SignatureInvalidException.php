<?php
namespace Tesoon\Foundation\Exceptions;

use Tesoon\Foundation\Authentication;

class SignatureInvalidException extends FoundationException {

    private $authentication;

    public function __construct(Authentication $authentication, string $message, int $code = 403)
    {
        parent::__construct($message, $code);
        $this->authentication = $authentication;
    }

    /**
     * @return Authentication
     */
    public function getAuthentication(): Authentication{
        return $this->authentication;
    }

}