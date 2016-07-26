<?php
namespace models\mysql;

use core\mysqldb\BaseMod;

class TBTestA extends BaseMod
{
    public $dbName = 'common';
    public $tbName = 'base_village';

    public function getData()
    {
        $q = $this
            ->offset(3)
            ->where('`citycode`=:citycode',[':citycode'=>'1101'])
            ->andWhere('`name` like :name',[':name'=>'%居委%'])
            ->orderBy('`id` desc')
            ->limit(3)
            ->all();
        ;
        return $q;
//        return $q->getRawSql();
        return $q->count();
    }
}