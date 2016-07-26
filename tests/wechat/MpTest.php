<?php
use vendor\yiihelpers\Json;
class MpTest extends \tests\TestCase {

    public function testDefault()
    {
        $this->assertTrue(true);
    }

    public $success = [
        'errcode' => 0,
        'errmsg' => 'ok'
    ];

    public function jtestMenu()
    {
        $wj = 'opHwYtxPRS1bdrlW0X3tvrVQt43U';
        $vs = 'opHwYtyabd2Uy3uAlUUf6VeEPQoo';
        $menu = \vendor\jeen\wechat\MpMenu::getInstance();
        $md = '{"button":[{"type":"click","name":"数据报表","key":"V1001_DATA_REPORT","sub_button":[]},{"name":"其他","sub_button":[{"type":"view","name":"搜索","url":"http:\/\/www.baidu.com\/","sub_button":[]},{"type":"view","name":"楼里","url":"http:\/\/louli.com.cn\/","sub_button":[]},{"type":"click","name":"赞一下我们","key":"V1001_GOOD","sub_button":[]}]}]}';
        $this->assertEquals($this->success, $menu->create($md));
        unset($md);
        $ret = $menu->get();
        $this->assertArrayHasKey('menu', $ret);
        $mr = json_encode([
            'group_id' => "2",
//  "sex":"1",
//  "country":"中国",
//  "province":"广东",
//  "city":"广州",
//  "client_platform_type":"2"
//  "language":"zh_CN"
        ]);
        $conditionalMD = '{"button":[{"type":"click","name":"超级报表","key":"V1001_DATA_REPORT","sub_button":[]},{"name":"其他","sub_button":[{"type":"view","name":"搜索","url":"http:\/\/www.baidu.com\/","sub_button":[]},{"type":"view","name":"楼里","url":"http:\/\/louli.com.cn\/","sub_button":[]},{"type":"click","name":"顶一下我们","key":"V1001_GOOD","sub_button":[]}]}],"matchrule":'.$mr.'}';
        unset($mr);
        $ret = $menu->addConditional($conditionalMD);
        Jeen::echoln(Json::encode($ret));
        unset($conditionalMD);
        $this->assertArrayHasKey('menuid', $ret, Json::encode($ret));
        if (isset($ret['menuid'])) {
            $this->assertEquals($this->success, $menu->delConditional($ret['menuid']));
        }
        $this->assertEquals($this->success, $menu->delete());
    }
    
    public function jtestMessage()
    {
        $wj = 'opHwYtxPRS1bdrlW0X3tvrVQt43U';
        $vs = 'opHwYtyabd2Uy3uAlUUf6VeEPQoo';
        $mp = \vendor\jeen\wechat\MpMessage::getInstance();
        $text = [
            'touser' => $wj,
            'msgtype' => 'text',
            'text' => ['content' => 'hello world']
        ];

        $msg = $text;
        $this->assertEquals($this->success, $mp->customSend($msg));
 
        $msg =  [
            'filter' => ['is_to_all'=>false,'group_id'=>2],
            'msgtype' => 'text',
            'text' => ['content' => 'hi all :)']
        ];
        $this->assertArrayHasKey('msg_id', $mp->massSendAll($msg));
        
        $msg = [
            'touser' => [$wj, $vs],
            'msgtype' => 'text',
            'text' => ['content' => 'hi some :)']
        ];
        $this->assertArrayHasKey('msg_id', $mp->massSend($msg));

    }

    public function jtestUser()
    {
        $wj = 'opHwYtxPRS1bdrlW0X3tvrVQt43U';
        $vs = 'opHwYtyabd2Uy3uAlUUf6VeEPQoo';
        $mp = \vendor\jeen\wechat\MpUser::getInstance();

        $ret = $mp->get();
        $this->assertArrayHasKey('data',$ret);
        if (isset($ret['data']['openid'])) {
            $uList = $ret['data']['openid'];
            $ret = $mp->infoBatchGet($uList);
            $this->assertArrayHasKey('user_info_list', $ret);
        }
    }

}
