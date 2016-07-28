# 使用yaf搭建的简易API服务框架

**测试环境 及 配置说明**
```
php v7.0.7
yaf v3.0.3
yar v2.0.1

mysql 5.6.21
nginx 1.10.1

=============
php ini example:
;php extension for yaf
extension = yaf.so
yaf.use_namespace=1
yaf.use_spl_autoload=1
;php extension for yar ,timeout ms
extension = msgpack.so
extension = yar.so
yar.timeout = 3000

============
nginx conf example:
server
{
        listen       80;
        server_name  yafapi.local.com;
        index index.php;
        root  /data/www/yaf/yafapi/apiopen/web;

        if (!-e $request_filename) {
                rewrite ^(.*)$ /index.php/$1 last;
        }

        location ~ [^/]\.php(/|$)
        {
                fastcgi_pass  127.0.0.1:9007;
                fastcgi_index index.php;
                include fastcgi.conf;
        }

        access_log  off;
}

```

### 项目模块
- apiopen  用于开放 api 调用
- apiyar  用于 rpc 调用 基于yar
- console  用于 cli 命令行处理
- common 包含一些 核心类 及 数据模型
- tests 包含项目phpunit测试文件
- var 用于项目log,session,cache的存储
- vendor 一些扩展及三方工具类

### J_ENV 环境分级
**需创建与之对应的配置文件 -  建议四级**
- local  - 本地
- alpha  - 测试线
- beta - 预发布
- stable - 正式线

**可根据项目实际部署环境调整**

### J_DEBUG 全局调试开关
**建议用于调试日志记录**


#### 其他备注
- 目录权限 chmod -R 0777 /path/to/yafapi/var
- 建议接口返回的布尔值 统一使用 0 1 代替
- 建议返回值键名规避各变成语言的关键字和保留字

**注意**

*使用 api yar 时,需要配置中的 vendor/jeen/JApi apiUrl与相关服务环境的配置*

#### Api Open 请求参数说明
**建议 使用 如下参数格式，用于安全校验**

|参数名|必要|类型|说明|范例|
|:---|:---|:---|:---|:---|
|systemType|否|string|系统类型|iphone 6s plus,HuaWei P9 MAX|
|systemVersion|否|string|系统版本|ios 9.1.2, android 5.0.1|
|systemMAC|否|string|系统网卡地址|0f:00:23:e2:ed:12|
|systemIMEI|否|string|手机IMEI|830123123123123|
|systemIDFA|否|string|iOS idfa|ifajs-asdlf-asfdjl-asdf|
|appType|是|int|应用类型|0未知,1android,2ios,3web,...|
|appVersion|是|string|应用版本|1.2.8|
|timeStamp|是|int|Unix 10位整型时间戳|1401230123|
|apiToken|是|string|接口调用令牌，指定或约定算法生成|dlfw932lfasff...|
|userId|是|int/string|用户ID(唯一标识)|692343993|
|userToken|是|string|用户会话令牌(用于登录校验)|ijfwafjsjd89s8df8a|
|apiName|是|string|调用接口名称|test_index_test|
|apiParams|是|json(array/dict/map)|接口请求参数|{a:"b",c:3}|
|sign|是|string|签名值,约定的签名算法|oiasldkf|


*更多说明待完善*
