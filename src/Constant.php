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


    const GET_REQUEST = 'GET';
    const POST_REQUEST = 'POST';
    const PUT_REQUEST = 'PUT';
    const DELETE_REQUEST = 'DELETE';

    const RESPONSE_CODE_OK = 20000;

}