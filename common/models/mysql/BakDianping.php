<?php
namespace models\mysql;

use core\mysqldb\BaseMod;

class BakDianping extends BaseMod
{
    public $dbName = 'shop';
    public $tbName = 'dianping';

    public function getData()
    {
        $q = $this
            ->where('`province`=:province',[':province'=>'北京'])
            ->andWhere('`area`=:name',[':name'=>'%朝阳区%'])
        ;
        return $q->count();
    }

    public function initBj()
    {
        $q = $this
            ->where('`province`=:province',[':province'=>'北京'])
            ->andWhere('`area`=:name',[':name'=>'%朝阳区%'])
            ->limit(3)
            ->all()
        ;
        return $q;
    }

}