<?php
namespace Tesoon\Foundation;

use Tesoon\Foundation\Exceptions\TokenException;

class Encoder{

    /**
     * @var Application
     */
    private $application;

    /**
     * @var Signature
     */
    private $signature; 

    public function __construct(Application $application, Signature $signature = null)
    {
        $this->application = $application;
        if($signature === null){
            $signature = new Token();
        }
        $this->signature = $signature;
    }

    /**
     * @param array $data
     * @param SignatureSetting|null $setting
     * @param Application|null $outer 外部应用，如果未指定该参数则将使用$this->application来进行加密
     * @return string
     * @throws TokenException
     */
    public function encrypt(array $data, SignatureSetting $setting = null, Application $outer = null): string{
        if($outer === null){
            $outer = $this->application;
        }
        if($setting === null){
            $setting = new SignatureSetting();
        }
        $setting->setSignature(Helper::computeMD5($data));
        return $this->signature->encrypt($outer, $setting)->signature;
    }

    public function getApplication():Application{
        return $this->application;
    }

}