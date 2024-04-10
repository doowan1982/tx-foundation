<?php
namespace Tesoon\Foundation;

use Tesoon\Foundation\Exceptions\DataException;
use Tesoon\Foundation\Exceptions\SignatureInvalidException;
use Tesoon\Foundation\Exceptions\TokenException;
use Tesoon\Foundation\Logstash\LogConfig;
use Tesoon\Foundation\Request\TransportBuilder;
use Tesoon\Foundation\Response\ResponseBody;

class Context{

    /**
     * @var bool
     */
    private $test = true;

    /**
     * @var bool
     */
    private $prod = false;

    /**
     * @var LogConfig
     */
    private $loggerConfig;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var Signature
     * 
     */
    private $signature;

    /**
     * @var Encoder
     */
    private $encoder;

    /**
     * @var Decoder
     */
    private $decoder;

    private static $instance;

    public static function instance(): Context{
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * 绑定Signature的外部实现
     * @param Signature $signature
     * @return Context
     */
    public function setSignature(Signature $signature): Context{
        $this->signature = $signature;
        return $this;
    }

    public function prodEnv(): Context{
        $this->test = !($this->prod = true);
        return $this;
    }

    public function testEnv(): Context{
        $this->prod = !($this->test = true);
        return $this;
    }
    public function isProd(): bool{
        return $this->prod;
    }

    public function isTest(): bool{
        return $this->test;
    }

    public function setApplication(Application $application): Context{
        $this->application = $application;
        return $this;
    }

    public function setEncoder(Encoder $encoder): Context{
        $this->encoder = $encoder;
        return $this;
    }

    public function setDecoder(Decoder $decoder): Context{
        $this->decoder = $decoder;
        return $this;
    }

    public function getApplication(): Application{
        if($this->application === null){
            return EmptyApplication::instance();
        }
        return $this->application;
    }

    public function setLogConfig(LogConfig $logConfig): Context{
        $this->loggerConfig = $logConfig;
        return $this;
    }

    public function getLogConfig(): LogConfig{
        if($this->loggerConfig === null){
            $this->loggerConfig = new LogConfig();
        }
        return $this->loggerConfig;
    }

    /**
     * @return TransportBuilder
     */
    public function getRequest(): TransportBuilder{
        if($this->signature === null){
            $this->signature = new Token();
        }
        if($this->encoder === null){
            $this->encoder = new Encoder($this->getApplication(), $this->signature);
        }
        return TransportBuilder::create()->setEncoder($this->encoder);
    }

    /**
     * @param string $ticket
     * @param array $parameters get + rawdata(Content-type:application/json)
     * @param Application $application 默认为从$this->applicaiton来解析数据，如果指定该值，则由此来解密
     * @return ResponseBody
     * @throws DataException
     * @throws SignatureInvalidException
     * @throws TokenException
     */
    public function getResponse(string $ticket, array $parameters, Application $application = null): ResponseBody{
        $authentication = new Authentication();
        $authentication->signature = $ticket;
        if($this->signature === null){
            $this->signature = new Token();
        }
        if($this->decoder === null){
            $this->decoder = new Decoder($this->getApplication(), $this->signature);
        }
        try{
            return $this->decoder->get($authentication, $application, $parameters);
        }catch(SignatureInvalidException|DataException|TokenException $e){
            throw $e;
        }
    }

}