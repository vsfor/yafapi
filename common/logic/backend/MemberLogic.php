<?php
namespace logic\backend;

use core\Code;
use core\JObject;
use jhelper\JHash;
use models\vsfor\AdminUser;

class MemberLogic extends JObject
{
    //-覆盖-单例模式Start
    private static $instance;

    /**
     * @return MemberLogic
     */
    public static function getInstance(){
        if(empty(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
    private function __construct() { }
    public function __clone() { throw new \Exception('Clone is not allowed !'); }
    //-覆盖-单例模式End

    public function login($params)
    {
        if(!isset($params['username'], $params['password'])) {
            return ['code'=>Code::ErrorParam, 'msg'=>'缺少必要参数'];
        }
        $adminLib = AdminUser::getInstance();
        $admin = $adminLib->getByUserName(trim($params['username']));
        if(!$admin) {
            return ['code'=>Code::FailedCode, 'msg'=>'用户名不存在'];
        }
        if (!JHash::checkHash($admin['password'], $params['password'])) {
            return ['code'=>Code::FailedCode, 'msg'=>'密码错误'];
        }
        if (!$admin['status']) {
            return ['code'=>Code::FailedCode, 'msg'=>'账号被锁定'];
        }
        $_SESSION['backend.user_id'] = $admin['id'];
        return ['msg'=>'登录成功'];
    }


}