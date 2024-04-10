<?php
namespace Tesoon\Tests;

use Lcobucci\JWT\Signer\Hmac\Sha512;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Tesoon\Foundation\Application;
use Tesoon\Foundation\Authentication;
use Tesoon\Foundation\Constant;
use Tesoon\Foundation\Context;
use Tesoon\Foundation\Exceptions\DataException;
use Tesoon\Foundation\Exceptions\SignatureInvalidException;
use Tesoon\Foundation\Exceptions\TokenException;
use Tesoon\Foundation\Helper;
use Tesoon\Foundation\Logstash\LogConfig;
use Tesoon\Foundation\Models\Admin;
use Tesoon\Foundation\Models\ApplicationKey;
use Tesoon\Foundation\Models\DataFactory;
use Tesoon\Foundation\Models\EnterpriseOrganization;
use Tesoon\Foundation\Models\Lists;
use Tesoon\Foundation\Decoder;
use Tesoon\Foundation\Models\LoginInfo;
use Tesoon\Foundation\SignatureSetting;
use Tesoon\Foundation\Token;

/**
 * @group reach
 */
class ReachTest extends TestCase{

    public static function setUpBeforeClass(): void
    {
        $config = new LogConfig();
        $setting = new SignatureSetting();
        $setting->expiredTime = '+2 hours';
        $config->handlers = [new StreamHandler('./tests/data/log/logger.log', Logger::DEBUG)];
        Context::instance()
            ->setApplication(static::getApplication())
            ->setLogConfig($config)
            ->getRequest()
            ->setSetting($setting)
            ->setRequestContext('foundation.lts.tesoon.com');
//            ->setRequestContext('f-dev.tesoon.com', 'http');
    }
    public function testToken(){
        $token = new Token();
        $setting = new SignatureSetting();
        $setting->setSignature(Helper::computeMD5(['a' => ['c' => 'aaaaa']]));
        $setting->expiredTime = '+2 day';
        $authentication = null;
        try{
            $authentication = $token->encrypt(static::getApplication(), $setting);
            // echo $authentication->signature;exit;
            $this->assertTrue($token->decrypt($authentication, static::getApplication()), '验证失败');
        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * @group reachEnter
     */
    public function testGenerateTicket(){
        $token = new Token();
        $setting = new SignatureSetting();
        $setting->signer = new Sha512();
        $setting->expiredTime = '+2 day';
        $setting->setSignature(Helper::computeMD5([ 
            'content' => $this->getTestJSON('admin.json'),
            'type' => Constant::ADMIN_PUSH_TYPE
        ]));
        try{
            $authentication = $token->encrypt(static::getApplication(), $setting);
            file_put_contents('./tests/data/ticket.txt', $authentication->signature);
            $this->assertTrue(true, 'Required');
            return $authentication;
        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * @group reachEnter
     * @depends testGenerateTicket
     */
    public function testReach(Authentication $authentication){
        $reach = new Decoder(new Token());
        $parameters = [
            'type' => Constant::ADMIN_PUSH_TYPE,
            'content' => $this->getTestJSON('admin.json'),
        ];
        try{
            $data = $reach->get($authentication, static::getApplication(), $parameters)->getData();
            $this->assertNotNull($data, '获取无效的Data');
            $this->assertTrue($data instanceof Lists, '获取管理员数据失败');
            $this->assertAdminTest($data, $parameters['content']);
        }catch(SignatureInvalidException|DataException|TokenException $e){
            throw $e;
        }
    }

    /**
     * @depends testGenerateTicket
     */
    public function testContextResponse(Authentication $authentication){
        $body = Context::instance()->getResponse($authentication, [
            'content' => $this->getTestJSON('admin.json'),
            'type' => Constant::ADMIN_PUSH_TYPE
        ]);
        $this->assertIsObject($body->getData(), '请求失败');
    }


    /**
     * @group fetchResponseData
     */
    public function testFetchResponseData(){
        $token = new Token();
        $setting = new SignatureSetting();
        $data = $this->getTestJSON('admin.json');
        $setting->setSignature(Helper::computeMD5($data));
        try{
            $authentication = $token->encrypt(static::getApplication(), $setting);
            // echo $authentication->signature;exit;
            $this->assertTrue($token->decrypt($authentication,static::getApplication()), '验证失败');
            return $data;
        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * @depends testFetchResponseData
     */
    public function testAdmin($array){
        try{
             /**
              * @var Lists
              */
            $admins = DataFactory::build(Constant::ADMIN_PUSH_TYPE, $array);
            $this->assertTrue($admins instanceof Lists, '管理员列表类型不正确');
            $this->assertAdminTest($admins, $array);
        }catch(DataException $e){
            throw $e;
        }
     }
 
    public function testJob(){
        $array = $this->getTestJSON('job.json');
        $jobs = DataFactory::build(Constant::JOBS_PUSH_TYPE, $array);
        foreach($jobs as $key=>$job){
            $this->assertTrue($job->id == $array[$key]['id'],"学科编号不正确[{$key}]");
        }
    }
 
     public function testPermission(){
        $array = $this->getTestJSON('permission.json');
        $permissions = DataFactory::build(Constant::ALL_ADMIN_PERMISSION_PUSH_TYPE, $array);
        foreach($permissions as $permission){
            $this->assertTrue(isset($array[$permission->adminId]),"权限管理员编号不正确[{$permission->adminId}]");
            $this->assertTrue(count(array_diff($permission->routes, $array[$permission->adminId])) == 0,"权限地址不匹配[{$permission->adminId}]");
        }
     }
 
     public function testMenu(){
        $array = $this->getTestJSON('menu.json');
        $menus = DataFactory::build(Constant::ALL_MENU_PUSH_TYPE, $array);
        foreach($menus as $key=>$menu){
            $data = $array[$key];
            $this->assertTrue($menu->id == $data['id'], '菜单编号不正确');
            $this->assertTrue($menu->name == $data['name'], '菜单名称不正确');
            $this->assertTrue($menu->order == $data['show_order'], '菜单名称不正确');
            $this->assertTrue($menu->menus->size() == count($data['childs']), '子菜单不一致');
        }
     }
 
     public function testAdminMenus(){
        $array = $this->getTestJSON('admin-menus.json');
        $menus = DataFactory::build(Constant::ALL_ADMIN_MENU_PUSH_TYPE, $array);
        foreach($menus as $menu){
            $this->assertTrue(isset($array[$menu->adminId]),"管理员菜单编号不正确[{$menu->adminId}]");
            $this->assertTrue(count(array_diff($menu->menus, $array[$menu->adminId])) == 0,"菜单地址不匹配[{$menu->adminId}]");
        }
     }
 
     public function testOrganization(){
        $array = $this->getTestJSON('organization.json');
         /**
          * @var EnterpriseOrganization
          */
         $root = DataFactory::build(Constant::ORGANIZATION_PUSH_TYPE, $array);
         $firstLevelOrganization = 0;
         foreach($array as $value){
             if($value['parent_organization_id'] == 1){
                 $firstLevelOrganization++;
             }
         }
         $this->assertTrue($root->parentId == 0, '不是一个有效的根节点：'.$root->id);
         $this->assertNotNull($root, '组织架构根节点不能为空');
         $this->assertTrue($root->organizations->size() == $firstLevelOrganization, '组织架构直接子节点数量不正确');
         $this->assertTrue($root->getAllEnterpriseOrganization()->size() == count($array) - 1, '组织架构未包含所有子节点');
     }

    /**
     * @group loginInfo
     */
     public function testLoginInfo(){
         $array = $this->getTestJSON('login-info.json');
         /**
         * @var LoginInfo
         */
         $loginInfo = DataFactory::build(Constant::LOGIN_PUSH_TYPE, $array);
         $this->assertTrue($loginInfo->adminId === $array['admin_id'], '管理员ID不正确');
         $this->assertTrue($loginInfo->employeeId === $array['employee_id'], '员工ID不正确');
         $admin = $loginInfo->pullAdmin([]);
         $this->assertEquals($admin instanceof Admin, '登录后拉取管理数据失败');

    }


     public function testKey(){
        $array = [
            'type' => ApplicationKey::APPLICATION_TYPE,
            'key' => 'asfasfsdsdf'
        ];
        $key = DataFactory::build(Constant::KEY_PUSH_TYPE, $array);
        $this->assertTrue($key->type === ApplicationKey::APPLICATION_TYPE, 'Key类型不正确');
     }


     private function assertAdminTest(Lists $admins, array $array){
        foreach($admins as $k=>$admin){
            $data = $array[$k];
            $this->assertNotNull($admin, "无效的admin类型");
            $this->assertTrue($admin->id == $data['admin_id'], "id不正确");
            $this->assertTrue($admin->realName == $data['real_name'], "realName不正确");
            $this->assertTrue($admin->phone == $data['phone'], "phone不正确");
            $this->assertTrue($admin->sex == $data['sex'], "sex不正确");
            $this->assertTrue($admin->nickname == $data['nickname'], "nickname不正确");
            $this->assertTrue($admin->status == $data['status'], "status不正确");
            $this->assertTrue($admin->email == $data['email'], "email不正确");
            $this->assertTrue($admin->employee->id == $data['employee_id'], '员工编号不正确');
            foreach($admin->subjects as $key=>$subject){
                $this->assertTrue($subject->id == $data['subjects'][$key]['subject_id'],"学科编号不正确[{$key}]");
            }
            foreach($admin->employee->organization as $key=>$organization){
                $this->assertTrue($organization->id == $data['employee']['organization'][$key]['id'],"员工编号不正确[{$key}]");
            }
         }
     }

     private function getTestJSON(string $file){
         $content = file_get_contents('./tests/data/'.$file);
         $array = json_decode($content, true); 
         return $array['content'] ?? $array;
     }

    private static $application;
    private static function getApplication(): Application{
        if(static::$application === null){
            static::$application = new Application('9276597406', 'f2d09915fc4dee4b3ee6d97e5a6ea5b8');
        }
        return static::$application;
    }

}