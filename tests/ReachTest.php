<?php
namespace Tesoon\Tests;

use PHPUnit\Framework\TestCase;
use Tesoon\Foundation\Application;
use Tesoon\Foundation\Authentication;
use Tesoon\Foundation\Response\Reach;
use Tesoon\Tests\Signature;

class ReachTest extends TestCase{

    public function testData(){
        $application = $this->getApplication();
        $authentication = $this->getAuthentication();
        $data = (new Reach($application, new Signature()))->get($authentication);
        $this->assertTrue($data != null, '测试失败');
    }

    
    private $authentication;
    private function getAuthentication(): Authentication{
        if($this->authentication === null){
            $this->authentication = new Authentication();
        }
        return $this->authentication;
    }

    private $application;
    private function getApplication(): Application{
        if($this->application === null){
            $this->application = new Application('test', 'test');
        }
        return $this->application;
    }

}