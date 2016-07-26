<?php
namespace models\vsfor;

use core\mysqldb\BaseMod;
use vendor\jeen\JLog;

class AdminUser extends BaseMod
{
    //-覆盖-单例模式Start
    private static $instance;

    /**
     * @return AdminUser
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

    protected $dbName = 'common';
    protected $tbName = 'admin_user';

    public function getByUserName($userName)
    {
        try {
            return $this->where('`username`=:username', [
                ':username' => $userName,
            ])->one();
        } catch (\Exception $e) {
            JLog::debug($e->getMessage());
        }
        return false;
    }
}