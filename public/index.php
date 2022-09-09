<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
$allow_origin = [
    'http://nft.jzjxxy.top',
];

ini_set('session.cookie_domain', ".jzjxxy.top");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: *');
header("Access-Control-Allow-Headers: DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding,token,language_id,Cookies,Cookie");
if (in_array($origin, $allow_origin) || strstr($origin, '127.0.0.1') || strstr($origin, 'localhost'))
    header('Access-Control-Allow-Origin: ' . $origin);


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}
// 判断是否安装
if (!is_file(APP_PATH . 'admin/command/Install/install.lock')) {
    header("location:./install.php");
    exit;
}
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
