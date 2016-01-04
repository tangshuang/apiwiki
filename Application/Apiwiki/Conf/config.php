<?php
return array(
    /* 数据库配置 */
    'DB_TYPE'   => 'mysql',         // 数据库类型
    'DB_HOST'   => '10.169.91.32',     // 服务器地址
    'DB_NAME'   => 'apiwiki',       // 数据库名
    'DB_USER'   => 'admin',              // 用户名
    'DB_PWD'    => 'ajrice123ajrice123#',              // 密码
    'DB_PORT'   => '3306',          // 端口
    'DB_PREFIX' => 'aw_',           // 数据库表前缀

    /* URL模式 */
    'URL_CASE_INSENSITIVE'  =>  false,     // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             =>  2,         // URL访问模式,可选参数0、1、2、3,代表以下四种模式： // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__'    => site_url('/Public/Static'),
        '__PUBLIC__'    => site_url('/Public/'.MODULE_NAME),
        '__IMG__'       => site_url('/Public/'.MODULE_NAME . '/img'),
        '__CSS__'       => site_url('/Public/'.MODULE_NAME . '/css'),
        '__JS__'        => site_url('/Public/'.MODULE_NAME . '/js'),
        '__ASSETS__'    => site_url('/Public/Assets'),
        '__AM__'        => site_url('/Public/Assets/AmazeUI'),
    ),

    // Trace开关
    //'SHOW_PAGE_TRACE'   => true,
);