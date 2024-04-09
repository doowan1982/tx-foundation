<?php
namespace Tesoon\Foundation\Response;

use Tesoon\Foundation\Constant;
use Tesoon\Foundation\Exceptions\DataException;
use Tesoon\Foundation\GeneralObject;
use Tesoon\Foundation\Models\Data;
use Tesoon\Foundation\Models\DataFactory;

class ResponseBody extends GeneralObject
{

    const STATUS = [
        '400000' => '',
        '400001' => '',
        '400002' => '',
        '400003' => '',
        '400010' => '',
        '400011' => '',
        '400012' => '',
    ];

    private $code = 20000;

    /**
     * @var Data
     */
    private $data;

    private $message = '';

    public function setCode(int $code, string $message = ''): ResponseBody{
        $this->code = (int)$code;
        $this->message = $message;
        return $this;
    }

    public function ok(): bool{
        return $this->code === Constant::RESPONSE_CODE_OK;
    }

    public function getCode(): int{
        return $this->code;
    }

    public function getMessage(): string{
        return $this->message;
    }

    /**
     * 设置外部的请求参数
     * @param array $parameters
     * @throws DataException
     */
    public function setResponseParameters(array $parameters){
        $this->data = DataFactory::build($parameters['type'] ?? 0, $parameters['content'] ?? $parameters);
    }

    public function getData(): ?Data{
        return $this->data;
    }

}