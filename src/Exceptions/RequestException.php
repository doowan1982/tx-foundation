<?php
namespace Tesoon\Foundation\Exceptions;

use Exception;
use Tesoon\Foundation\Request\Transport;
use Tesoon\Foundation\Response\ResponseBody;

class RequestException extends FoundationException {

    /**
     * @var Transport
     */
    public $transport;

    /**
     * @var ResponseBody
     */
    public $body;

    public function __construct(Transport $transport, string $message, Exception $e = null){
        parent::__construct($e->message, 0, $e);
        $this->transport = $transport;
    }

    public function setResponseBody(ResponseBody $body): RequestException{
        $this->body = $body;
        return $this;
    }

}