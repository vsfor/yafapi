<?php
namespace models\mongo;

use core\mongodb\BaseMod;

class TBTestA extends BaseMod
{
    public $dbName = 'louli';  // default to DB: common
    public $tbName = 'test'; //collection name 不设置 则自动识别为test_a 即 TB  TestA  => table  test_a

    /**
     * @param string $diy
     * @return TBTestA
     */
    public static function getInstance($diy = 'tb.test_a')
    {
        return parent::getInstance($diy);
    }
     
}