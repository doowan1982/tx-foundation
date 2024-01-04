<?php
namespace Tesoon\Tests;

use Lcobucci\JWT\Signer\Hmac\Sha512;
use PHPUnit\Framework\TestCase;
use Tesoon\Foundation\Application;
use Tesoon\Foundation\Authentication;
use Tesoon\Foundation\Constant;
use Tesoon\Foundation\Exceptions\DataException;
use Tesoon\Foundation\Exceptions\SignatureInvalidException;
use Tesoon\Foundation\Models\DataFactory;
use Tesoon\Foundation\Models\EnterpriseOrganization;
use Tesoon\Foundation\Models\Lists;
use Tesoon\Foundation\Response\Reach;
use Tesoon\Foundation\Response\Signature;
use Tesoon\Foundation\SignatureSetting;

/**
 * @group reach
 */
class ReachTest extends TestCase{

    public function testToken(){
        $application = $this->getApplication();
        $token = new Signature($application);
        $setting = new SignatureSetting();
        $setting->claims = ['a' => ['c' => 'aaaaa']];
        $setting->expiredTime = '+2 seconds';
        $authentication = null;
        try{
            $authentication = $token->encrypt($setting);
            // echo $authentication->signature;exit;
            $this->assertTrue($token->check($authentication), '验证失败');
            $body = $authentication->getBody();
            $this->assertTrue(isset($body['a']), '解析ticket数据失败');
        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * @group reachEnter
     */
    public function testGenereateTicket(){
        $token = new Signature($this->getApplication());
        $setting = new SignatureSetting();
        $setting->signer = new Sha512();
        $setting->claims = [ 
            'content' => $this->getTestJSON('admin.json'),
            'type' => Constant::ADMIN_PUSH_TYPE
        ];
        try{
            $authentication = $token->encrypt($setting);
            file_put_contents('./tests/data/ticket.txt', $authentication->signature);
            $this->assertTrue(true, 'Required');
            return $authentication->signature;
        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * @group reachEnter
     * @depends testGenereateTicket
     */
    public function testReach($ticket){
        $authentication = new Authentication();
        $authentication->signature = $ticket;
        $application = $this->getApplication();
        $reach = new Reach($application, new Signature($application));
        try{
            $data = $reach->get($authentication);
            $this->assertNotNull($data, '获取无效的Data');
            $this->assertTrue($data instanceof Lists, '获取管理员数据失败');
            $body = $authentication->getBody();
            $this->assertAdminTest($data, $body['content']);
        }catch(SignatureInvalidException $e){
            throw $e;
        }
    }

    /**
     * @group fetchResponseData
     */
    public function testFetchResponseData(){
        $token = new Signature($this->getApplication());
        $setting = new SignatureSetting();
        $setting->claims = $this->getTestJSON('admin.json');
        try{
            $authentication = $token->encrypt($setting);
            // echo $authentication->signature;exit;
            $this->assertTrue($token->check($authentication), '验证失败');
            $this->assertTrue(is_array($authentication->getBody()), '解析ticket数据失败');
            return $authentication->getBody();
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
         return $array['content'] ?? [];
     }

    private $application;
    private function getApplication(): Application{
        if($this->application === null){
            $this->application = new Application('test', 'this is key!');
        }
        return $this->application;
    }

}