<?php
namespace Tesoon\Foundation\Response;

use Tesoon\Foundation\Application;
use Tesoon\Foundation\Authentication;
use Tesoon\Foundation\Models\DataFactory;
use Tesoon\Foundation\Signature;
use Tesoon\Foundation\Exceptions\SignatureInvalidException;
use Tesoon\Foundation\Models\Data;

class Reach{

    private $signature;
    private $application;

    public function __construct(Application $application, Signature $signature)
    {
        $this->application = $application;
        $this->signature = $signature;
    }

    /**
     * @param Authentication $authentication
     * @return Data|null
     * @throws SignatureInvalidException
     */
    public function get(Authentication $authentication): ?Data{
        if(!$this->signature->check($authentication)){
            throw new SignatureInvalidException($authentication, '验证失败');
        }
        return $this->createBy($authentication->getBody());
    }

    /**
     * 将data数据转为具体的Data模型对象
     * @param array $data
     * @return Data|null
     */
    protected function createBy(array $data): ?Data{
        return DataFactory::build($data['type'], $data['content']);
    }

}