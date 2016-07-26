<?php
namespace vendor\jeen\wechat;

class MpTemplate extends Mp
{
    private static $instance;
    /**
     * @param string $appId
     * @param string $appSecret
     * @param string $token
     * @return MpTemplate
     */
    public static function getInstance($appId='', $appSecret='', $token='')
    {
        $classHash = 'mp_'.md5("$appId:$appSecret:$token");
        if(empty(self::$instance[$classHash])){
            self::$instance[$classHash] = new self($appId,$appSecret,$token);
        }
        return self::$instance[$classHash];
    }

    private $apiBaseUrl = 'https://api.weixin.qq.com/cgi-bin/template/';

    /**
     * 设置所属行业
     * @param array|string $industries
     * @return mixed
     */
    public function apiSetIndustry($industries)
    {
        $body = is_string($industries) ? $industries : json_encode($industries);
        $url = $this->makeUrl($this->apiBaseUrl, 'api_set_industry', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 获取设置的行业信息
     * @return mixed
     */
    public function getIndustry()
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'get_industry', $this->getParams);
        $res = json_decode($this->httpGet($url), true);
        return $res;
    }

    /**
     * 获得模板ID  @todo 接口描述似乎有点问题  待核实
     * @param string $shortId 模板库中模板的编号，有“TM**”和“OPENTM*”等形式
     * @return mixed
     */
    public function apiAddTemplate($shortId)
    {
        $body = json_encode(['template_id_short'=>strval($shortId)]);
        $url = $this->makeUrl($this->apiBaseUrl, 'api_add_template', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }

    /**
     * 获取模板列表
     * @return mixed
     */
    public function getAllPrivateTemplate()
    {
        $url = $this->makeUrl($this->apiBaseUrl, 'get_all_private_template', $this->getParams);
        $res = json_decode($this->httpGet($url), true);
        return $res;
    }

    /**
     * 删除模板
     * @param string $templateId
     * @return mixed
     */
    public function delPrivateTemplate($templateId)
    {
        $body = json_encode(['template_id'=>strval($templateId)]);
        $url = $this->makeUrl($this->apiBaseUrl, 'del_private_template', $this->getParams);
        $res = json_decode($this->httpPost($url, $body), true);
        return $res;
    }
 



}