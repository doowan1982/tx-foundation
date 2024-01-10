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

    private static $instance;

    public static function instance(): Context{
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
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

    public function getApplication(): Application{
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
        return TransportBuilder::create()
                    ->setEncoder(new Encoder($this->getApplication(), new Token()));
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
        $decoder = new Decoder($this->getApplication(), new Token());
        try{
            return $decoder->get($authentication, $application, $parameters);
        }catch(SignatureInvalidException|DataException|TokenException $e){
            throw $e;
        }
    }

}