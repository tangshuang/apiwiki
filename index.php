<?php

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',false);

// 绑定模块
define('BIND_MODULE','Apiwiki');

// 定义应用目录
define('APP_PATH','./Application/');

/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define('RUNTIME_PATH', './Runtime/');

/**
 * 一些常量  --------------------------------------------------------------------
 */
define('AUTH_SALT','dsafodf8908as90f8asd09f80asdf8ew3');

define('NOW_TIME',$_SERVER['REQUEST_TIME']);
define('NOW_DATE',date('Y-m-d',$_SERVER['REQUEST_TIME']));
define('NOW_DATETIME',date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']));

define('ABS_PATH',dirname(__FILE__)); // 公开根目录

/**
 * 创建一个url函数，可以在所有地方使用
 * @param $uri
 * @return string
 */
function site_url($uri) {
    $root = $_SERVER['DOCUMENT_ROOT'];
    $base = ABS_PATH;
    $subdir = str_replace($root,'',$base);

    $domain = $_SERVER['HTTP_HOST'];
    $base_url = (is_ssl() ? 'https://' : 'http://').$domain.$subdir;

    if(substr($uri,0,1) == '/')
        $uri = substr($uri,1);
    $url = $base_url.'/'.$uri;
    return $url;
}

// 引入ThinkPHP入口文件 ----------------------------------------------------------
require './ThinkPHP/ThinkPHP.php';
