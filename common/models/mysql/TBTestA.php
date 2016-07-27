<?php
namespace models\mysql;

use core\mysqldb\BaseMod;

class TBTestA extends BaseMod
{
//    public $dbName = 'common';  // default to DB: common
//    public $tbName = 'base_village'; //不设置 则自动识别为test_a 即 TB  TestA  => table  test_a

    /**
     * @param string $diy
     * @return TBTestA
     */
    public static function getInstance($diy = 'tb.test_a')
    {
        return parent::getInstance($diy);
    }
    
    public function getData()
    {
        $q = $this
//            ->offset(3)
//            ->orderBy('`id` desc')
//            ->limit(1)
//            ->all();
        ;
//        return $q;
//        return $q->getSql();
//        return $q->getRawSql();
        return $q->updateCounters(['pid'=>-8],'`id`=:id',[':id'=>1]);
        return $q->update(['cname'=>"abc'; delete * from test_a;"],'`id`=:id',[':id'=>1]);
        return $q->count();
    }
}