<?php

namespace app\index\controller;

use think\Db;
use app\index\controller\JsApiPay;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;
use think\Controller;

//引入微信公众号jssdk
require_once(EXTEND_PATH . 'wechat/jssdk.php');

class Vip extends Controller
{

    public function _initialize()
    {
        parent::_initialize();
        $this->wechat = [
            'appid' => '', // APP APPID
            'app_id' => config('site.wx_appid'), // 公众号 APPID
            'app_scerect' => config('site.wx_appsecret'),
            'miniapp_id' => '', // 小程序 APPID
            'mch_id' => config('site.wx_mch_id'),
            'key' => config('site.wx_mch_key'),
            'notify_url' => config('site.wx_notifyurl'),
            'cert_client' => './cert/apiclient_cert.pem', // optional，退款等情况时用到
            'cert_key' => './cert/apiclient_key.pem',// optional，退款等情况时用到
            'log' => [ // optional
                'file' => './logs/wechat.log',
                'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
                'type' => 'single', // optional, 可选 daily.
                'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
            ],
            'http' => [ // optional
                'timeout' => 5.0,
                'connect_timeout' => 5.0,
                // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
            ],
            'mode' => 'normal', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
        ];
    }

    public function index()
    {
        $order_num = input('order_num');
        $body = input('body');
        $price = input('price');
        $jssdk = new \JSSDK($this->wechat['app_id'], $this->wechat['app_scerect']);
        $signPackage = $jssdk->GetSignPackage();
        $this->view->assign('signPackage', $signPackage);
//        获取openid
        $tools = new JsApiPay();
        $openId = $tools->GetOpenid();

        //发起支付
        $order = [
            'out_trade_no' => $order_num,
            'total_fee' => $price * 100, // **单位：分**
            'body' => $body,
            'openid' => $openId,
        ];

        $pay = Pay::wechat($this->wechat)->mp($order);
        $this->view->assign('data', $pay);
        return $this->view->fetch();
    }

}
