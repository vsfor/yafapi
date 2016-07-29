<?php
/**
 * 请求过滤配置
 */

return [
    'timeDiffLimit' => 1800, //请求参数时间与服务器允许的最大时间差

    'ipBlackList' => [ //ip 黑名单列表  - 支持使用网段,必须为标准的ipv4格式,eg: 192.168.0.1/24

    ],

    'userAgentBlackList' => [ //userAgent 黑名单列表   - 使用小写, 可不用全称

    ],

    'userIdBlackList' => [ //userId 黑名单列表

    ],

    //... other diy request filter config

];