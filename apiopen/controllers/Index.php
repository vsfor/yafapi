<?php

class IndexController extends \basecontroller\ApiOpen
{
    public function indexAction()
    {
        $t = [
            'a' => 'b',
            'c' => [
                'd'=>2,
                'e'=>2.1
            ]
        ];
        $this->returnJson($t);
        return false;
    }

    public function testAction()
    {
        $apiUri = 'http://localjapi/index.php';
        $apiUri = 'http://yii.jeen.wang/japi.php';
        $t[] = microtime(true);
        $r = \vendor\jeen\JApi::getInstance($apiUri)->call('site/diy',['bc'=>'d1','e'=>3]);
        $t[] = microtime(true);
//        $t[] = microtime(true);
//        $r = \vendor\jeen\JApi::getInstance($apiUri)->call('index/index/yartest',['bc'=>'d1','e'=>3]);
//        $t[] = microtime(true);
//        /* call directly */
        echo '<hr>';
        var_dump($r);
        echo '<hr>';
//        $t[] = microtime(true);
//        \vendor\jeen\JApi::getInstance($apiUri)->addTask('index/index/yartest',['bc'=>'d2','e'=>3]);
//        $t[] = microtime(true);
//        \vendor\jeen\JApi::getInstance($apiUri)->addTask('index/index/yartest',['bc'=>'d3','e'=>3]);
//        \vendor\jeen\JApi::getInstance($apiUri)->addTask('index/index/yartest',['bc'=>'d4','e'=>3]);
//        \vendor\jeen\JApi::getInstance($apiUri)->addTask('index/index/yartest',['bc'=>'d5','e'=>3]);
//        $t[] = microtime(true);
//        \vendor\jeen\JApi::getInstance($apiUri)->addTask('index/index/yartest',['bc'=>'d6','e'=>3]);
//        $t[] = microtime(true);
        Jeen::show($t);
        return false;
//        $t[] = microtime(true);
//        $apiUrl = "http://yaf.jeen.wang/japi.php";
//        $synClient = new Yar_Client($apiUrl);
//        $t[] = microtime(true);
//        /* call directly */
//        var_dump($synClient->add(1, 2));
//        $t[] = microtime(true);
//        Yar_Concurrent_Client::call($apiUrl,'add',[2,1],null);
//        Yar_Concurrent_Client::call($apiUrl,'add',[2,2],null);
//        Yar_Concurrent_Client::call($apiUrl,'add',[2,3],null);
//        Yar_Concurrent_Client::call($apiUrl,'add',[2,4],null);
//        $t[] = microtime(true);
//        Yar_Concurrent_Client::loop();
//        $t[] = microtime(true);
//        Jeen::show($t);
//        exit('<hr>');
    }

    public function yarAction($params=[])
    {
        \Yaf\Dispatcher::getInstance()->autoRender(FALSE); // 关闭自动加载模板
        $ps = func_get_args();
        $yafparams = \Yaf\Dispatcher::getInstance()->getRequest()->getParams();
        \vendor\jeen\JLog::debug('frontend->index->index->yar:'.json_encode($params).json_encode($ps).json_encode($yafparams),[],'japi');
        echo 'a';
    }

    public function ajaxAction()
    {
        header('Access-Control-Allow-Origin: *');
        $return = [
            'post'=>$_POST,
            'get'=>$_GET,
            'body'=>file_get_contents('php://input')
        ];
        echo json_encode($return);
        exit();
    }

    public function jsonpAction()
    {
        $functionName = $_GET['jsoncallback'] ? : 'Error';
        $return = [
            'post'=>$_POST,
            'get'=>$_GET,
            'body'=>file_get_contents('php://input')
        ];
        echo "$functionName(".json_encode($return).")";
        exit();
    }

}