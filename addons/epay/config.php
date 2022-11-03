<?php

return [
    [
        'name' => 'wechat',
        'title' => '微信',
        'type' => 'array',
        'content' => [],
        'value' => [
            'appid' => '',
            'app_id' => '',
            'app_secret' => '',
            'miniapp_id' => '',
            'mch_id' => '',
            'key' => '',
            'notify_url' => '/addons/epay/api/notifyx/type/wechat',
            'cert_client' => '/epay/certs/apiclient_cert.pem',
            'cert_key' => '/epay/certs/apiclient_key.pem',
            'log' => 1,
        ],
        'rule' => '',
        'msg' => '',
        'tip' => '微信参数配置',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'alipay',
        'title' => '支付宝',
        'type' => 'array',
        'content' => [],
        'value' => [
            'app_id' => '',
            'notify_url' => '/addons/epay/api/notifyx/type/alipay',
            'return_url' => '/addons/epay/api/returnx/type/alipay',
            'ali_public_key' => '',
            'private_key' => '',
            'log' => 1,
        ],
        'rule' => 'required',
        'msg' => '',
        'tip' => '支付宝参数配置',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'epay',
        'title' => '易支付',
        'type' => 'array',
        'content' => [],
        'value' => [
            'api' => 'http://jzjxxy.xyz/',
            'pid' => '1000',
            'key' => 'd9x5kVr5RKD7622DS57awWd62Wko6DAx',
            'notify_url' => '/addons/epay/api/notifyx/type/epay',
            'return_url' => '/addons/epay/api/returnx/type/epay',
            'log' => '0',
        ],
        'rule' => 'required',
        'msg' => '',
        'tip' => '易支付参数配置',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'alipay_way',
        'title' => '支付宝通道选择',
        'type' => 'select',
        'content' => [
            'epay' => '易支付',
            'alipay' => '官方',
        ],
        'value' => 'epay',
        'rule' => 'required',
        'msg' => '',
        'tip' => '支付宝支付通道选择',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => 'wechat_way',
        'title' => '微信通道选择',
        'type' => 'select',
        'content' => [
            'epay' => '易支付',
            'wechat' => '官方',
        ],
        'value' => 'epay',
        'rule' => 'required',
        'msg' => '',
        'tip' => '微信支付通道选择',
        'ok' => '',
        'extend' => '',
    ],
    [
        'name' => '__tips__',
        'title' => '温馨提示',
        'type' => 'array',
        'content' => [],
        'value' => '请注意微信支付证书路径位于/addons/epay/certs目录下，请替换成你自己的证书<br>'."\n"
            .'appid：APP的appid<br>'."\n"
            .'app_id：公众号的appid<br>'."\n"
            .'app_secret：公众号的secret<br>'."\n"
            .'miniapp_id：小程序ID<br>'."\n"
            .'mch_id：微信商户ID<br>'."\n"
            .'key：微信商户支付的密钥',
        'rule' => '',
        'msg' => '',
        'tip' => '微信参数配置',
        'ok' => '',
        'extend' => '',
    ],
];