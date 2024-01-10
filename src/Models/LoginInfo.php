<?php

namespace Tesoon\Foundation\Models;

use Tesoon\Foundation\Context;
use Tesoon\Foundation\Helper;

class LoginInfo extends Data
{

    /**
     * 通行证admin_id
     * @var int
     */
    public $adminId = 0;

    /**
     * 员工唯一id
     * @var int
     */
    public $employeeId = 0;

    /**
     * 用户id
     * @var string
     */
    public $userId = '';

    /**
     * 真实姓名
     * @var string
     */
    public $realName = '';

    /**
     * 唯一标识
     * @var string
     */
    public $unionId = '';

    /**
     * 登录设备信息
     * @var string
     */
    public $device = '';

    /**
     * 是否管理员
     * @var bool
     */
    public $isAdmin = false;

    /**
     * @var string
     */
    public $level;

    /**
     * @param array $value 本地可能存在的管理原模型数组
     * @return Admin|null
     */
    public function pullAdmin(array $value=[]): ?Admin{
        $admin = null;
        try{
            if(!empty($value)){
                $admin = new Admin();
                Helper::setObjectValues($admin, $value);
                if($admin->id === $this->adminId){
                    return $admin;
                }
            }
            $admin = Context::instance()->getRequest()->getAdmin($this->adminId);
        }catch(\Exception $e){

        }
        return $admin;
    }

}