<?php


namespace Tesoon\Foundation\Models;

use stdClass;
use Tesoon\Foundation\Constant;
use Tesoon\Foundation\Exceptions\DataException;
use Tesoon\Foundation\Models\Categories\Job;

class DataFactory
{
    /**
     * @param int $type
     * @param array|stdClass|string $data
     * @return Data
     * @throws DataException
     */
    public static function build(int $type, $data): Data{
        //如果指明type，同时不是一个数组形式时
        if($type != 0 && !is_array($data)){
            throw new DataException(DataFactory::class, null, "无效的数据");
        }
        switch($type){
            case Constant::ADMIN_PUSH_TYPE : {
                $list = new Lists();
                foreach($data as $value){
                    $list->add(Admin::create($value));
                }
                return $list;
            }
            break;
            case Constant::ALL_MENU_PUSH_TYPE : {
                return Menu::create($data);
            }
            break;
            case Constant::ALL_ADMIN_MENU_PUSH_TYPE : {
                return AdminMenu::create($data);
            }
            break;
            case Constant::ALL_ADMIN_PERMISSION_PUSH_TYPE : {
                return AdminPermission::create($data);
            }
            break;
            case Constant::APPLICATION_CONFIG_PUSH_TYPE : {
                return ApplicationConfig::create($data);
            }
            break;
            case Constant::JOBS_PUSH_TYPE : {
                $list = new Lists();
                foreach($data as $value){
                    $list->add(Job::create($value));
                }
                return $list;
            }
            break;
            case Constant::KEY_PUSH_TYPE : {
                if(is_string($data)){ //兼容前期仅推送中台密钥
                    $data = [
                        'type' => ApplicationKey::APPLICATION_TYPE,
                        'key' => $data
                    ];
                }
                return ApplicationKey::create($data);
            }
            break;
            case Constant::ORGANIZATION_PUSH_TYPE : {
                return EnterpriseOrganization::create($data);
            }
            break;
            case Constant::LOGIN_PUSH_TYPE: {
                return LoginInfo::create($data);
            }
        }
        if(!empty($data['object'])){
            //序列化型参数
            $obj = unserialize($data['object']);
            if(is_object($obj)){
                return $obj;
            }
        }
        return EmptyData::create();
    }

}