<?php
namespace Tesoon\Foundation;

class Constant{

     /**
     * 所有管理员推送
     */
    const ADMIN_PUSH_TYPE = 1;

    /**
     * 应用配置推送
     */
    const APPLICATION_CONFIG_PUSH_TYPE = 2;
    /**
     * KEY推送
     */
    const KEY_PUSH_TYPE = 3;

    /**
     * 所有菜单推送
     */
    const ALL_MENU_PUSH_TYPE = 4;

    /**
     * 所有管理员菜单推送
     */
    const ALL_ADMIN_MENU_PUSH_TYPE = 5;

    /**
     * 所有管理员权限推送
     */
    const ALL_ADMIN_PERMISSION_PUSH_TYPE = 6;

    /**
     * 职位推送
     */
    const JOBS_PUSH_TYPE = 7;
    
    /**
     * 职位推送
     */
    const ORGANIZATION_PUSH_TYPE = 8;

    /**
     * 登录信息
     */
    const LOGIN_PUSH_TYPE = 9;


    const GET_REQUEST = 'GET';
    const POST_REQUEST = 'POST';
    const PUT_REQUEST = 'PUT';
    const DELETE_REQUEST = 'DELETE';

    const APPLICATION_NAME = 'application_id';

    const RESPONSE_CODE_OK = 20000;

    const DATA_VALIDATION_FAILED_RESPONSE_CODE = 40000; //数据验证失败

    const INVALID_TOKEN_RESPONSE_CODE = 40003; //无效的TOKEN

    const INVALID_ID_RESPONSE_CODE = 40004; //无效的ID
    
    const TOKEN_VALIDATION_FAILED_RESPONSE_CODE = 400010; //令牌验证失败

    const TOKEN_HAS_EXPRIED_RESPONSE_CODE = 40011; //令牌已过期

    const TOEKN_NOT_USE_RESPONSE_CODE = 40012; //令牌无法使用，未到使用时间

}