<?php
namespace basecontroller;

use \Yaf\Controller_Abstract;
use \Yaf\Dispatcher;

class Console extends Controller_Abstract
{
    public function init()
    {
        if($this->getRequest()->isCli() !== true) {
            exit('permission not allowed');
        }
        Dispatcher::getInstance()->disableView();//关闭视图渲染
//        Dispatcher::getInstance()->autoRender(false);//关闭模板自动渲染
    }

    //获取excel表单内容
    protected function getSheet($inFile,$index = false)
    {
        return \vendor\phpexcel\JExcel::getInstance()->readSheet($inFile,$index);
    }

    //保存数组至表单  单表
    protected function saveSheet($outFile,array $data)
    {
        return \vendor\phpexcel\JExcel::getInstance()->saveSheet($outFile,$data);
    }

    //处理表格数组 保存N列
    protected function handleSheetArray(array $data,$cols = 10,$offset = 1,$must = false)
    {
        $final = [];
        if($must && $must >= $cols) return $final;
        foreach($data as $k=>$row)
        {
            if($k < $offset) continue;
            $t = [];
            for($i=0;$i<$cols;$i++) {
                if(isset($row[$i])) $t[$i] = trim($row[$i]);
                else $t[$i] = '';
            }
            if(is_array($row) && implode('',$t) && ($must===false || $t[$must])) {
                $final[] = $t; continue;
            }
        }
        return $final;
    }

}