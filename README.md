# 使用yaf搭建的简易API服务框架

```
测试环境
php v7.0.7
yaf v3.0.3
yar v2.0.1
*可使用php5.4+的配套扩展*
mysql 5.6.21
nginx 1.10.1

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

## 项目模块
### api  api 调用
### synapi  rpc 调用 基于yar
### console  cli 命令行处理

## J_ENV 环境分级 - 需创建与之对应的配置文件 -  建议四级 
### local  - 本地
### alpha  - 测试线
### beta - 预发布
### stable - 正式线
**可根据项目实际部署环境调整**

## J_DEBUG 全局调试开关
**建议用于调试日志记录**


**yaf php.ini**

```
;php extension for yaf
extension = yaf.so
yaf.use_namespace=1
yaf.use_spl_autoload=1

;php extension for yar ,timeout ms
extension = msgpack.so
extension = yar.so
yar.timeout = 3000

```

### 其他备注
#### 目录权限 chmod -R 0777 /path/to/yafapi/var
#### 建议接口返回的布尔值 统一使用 0 1 代替
#### 建议返回值键名规避各变成语言的关键字和保留字

**注意**

*使用 api yar 时,需要配置中的 vendor/jeen/JApi apiUrl与相关服务环境的配置*

*更多说明待完善*
