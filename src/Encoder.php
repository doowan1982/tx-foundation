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
     * @param Application|null $outer
     * @return string
     * @throws TokenException
     */
    public function encrypt(array $data, Application $outer = null): string{
        if($outer === null){
            $outer = $this->application;
        }
        $setting = new SignatureSetting();
        $setting->setSignature(Helper::computeMD5($data));
        $setting->setClaim('application_id', $outer->id);
        return $this->signature->encrypt($outer, $setting)->signature;
    }

}