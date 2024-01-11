<?php
namespace Tesoon\Tests;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Tesoon\Foundation\Application;
use Tesoon\Foundation\Context;
use Tesoon\Foundation\Logstash\LogConfig;
use Tesoon\Foundation\SignatureSetting;

/**
 * @group transport
 */
class TransportTest extends TestCase{

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
                ->setRequestContext('foundation.lts.tesoon.com');
    }


    public function testGetAdminById(){
        $admin = Context::instance()
                ->getRequest()
                ->getAdmin(244282);
        $this->assertIsObject($admin, '数据类型有误');
        $this->assertEquals($admin->id == 244282, '编号不正确');
    }

}