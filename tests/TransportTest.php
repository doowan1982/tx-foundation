<?php
namespace Tesoon\Tests;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Tesoon\Foundation\Application;
use Tesoon\Foundation\Constant;
use Tesoon\Foundation\Context;
use Tesoon\Foundation\Logstash\LogConfig;
use Tesoon\Foundation\Models\Data;
use Tesoon\Foundation\Response\ResponseBody;
use Tesoon\Foundation\SignatureSetting;

/**
 * @group transport
 */
class TransportTest extends TestCase{

    private $host = 'f-dev.tesoon.com';

    public static function setUpBeforeClass(): void
    {
        $config = new LogConfig();
        $config->handlers = [new StreamHandler('./tests/data/log/logger.log', Logger::DEBUG)];

        $setting = new SignatureSetting();
        $setting->expiredTime = '+2 day';
        Context::instance()->setLogConfig($config)
                ->setApplication(new Application('9276597406', 'f2d09915fc4dee4b3ee6d97e5a6ea5b8'))
                ->getRequest()
                ->setSetting($setting);
    }


    /**
     * @before
     */
    public function setEncoder(){
        Context::instance()
                ->getRequest()
                ->setRequestContext($this->host, 'http');
    }

    public function testGetAdminById(){
        $admin = Context::instance()
                ->getRequest()
                ->getAdmin(244282);
        $this->assertIsObject($admin, '数据类型有误');
        $this->assertEquals($admin->id == 244282, '编号不正确');
    }

    public function testInvoke(){
        $class = new class() extends ResponseBody{
            private $data = null;
            public function __construct()
            {
                $this->data = new class() extends Data{
                    public $responseData = [];
                };
            }
            public function setResponseParameters(array $parameters){
                $this->data->responseData = $parameters;
            }
            public function getData(): ?Data{
                return $this->data;
            }
        };
        $responseBody = Context::instance()
                        ->getRequest()
                        ->sendWarpper("http://{$this->host}/v2/abilities/foundation-test", [
                            'query' => [
                                'a' => 1,
                                'b' => 2,
                            ],
                            'method' => Constant::GET_REQUEST,
                            'body' => [
                                'c' => [
                                    'd' => 3,
                                    'e' => [
                                        'f' => 4,
                                    ]
                                ],
                                'g' => 4,
                            ]
                        ], $class);

        $this->assertEquals(is_array($responseBody->getData()->responseData), '调用失败');
        return $responseBody->getData()->responseData;
    }

    /**
     * @depends testInvoke
     */
    public function testVerifySign($data){
        $responseBody = Context::instance()
                        ->getRequest()
                        ->verifySign($data['auth'], $data['body']);
                        
        $this->assertTrue($responseBody === true, '验证失败');
    }

}