# 使用yaf搭建的简易API服务框架

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
*使用 api yar 时,需要设置vendor/jeen/JApi apiUrl *

*更多说明待完善*
