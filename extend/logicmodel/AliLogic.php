<?php


namespace logicmodel;


use Yansongda\Pay\Pay;

class AliLogic
{
    private $config;

    public function __construct()
    {
        $this->config = [
//            'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
            'app_id' => config('site.ali_app_id'),
            // 支付宝公钥字符串
            'ali_public_key' => config('site.ali_public_key'),
            // 自己生成的密钥字符串
            'private_key' => config('site.ali_private_key'),
            'notify_url' => config('site.ali_notify_url'),
            'log' => [ // optional
                'file' => './logs/alipay.log',
                'level' => 'debug', // 建议生产环境等级调整为 info，开发环境为 debug
                'type' => 'single', // optional, 可选 daily.
                'max_file' => 1, // optional, 当 type 为 daily 时有效，默认 30 天
            ],
        ];
    }

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 支付宝WEB支付
     * @param $order_num
     * @param $body
     * @param $amount
     * @return bool|false|string
     */
    public function appPay($order_num, $body, $amount)
    {
        try {
            $order = [
                'out_trade_no' => $order_num,
                'total_amount' => $amount,
                'subject' => $body,
            ];
            $config = $this->config;
            $alipay = Pay::alipay($config)->app($order);
            return $alipay->getContent();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 网页支付
     * @param $order_num
     * @param $body
     * @param $amount
     * @return bool|false|string
     */
    public function wapPay($order_num, $body, $amount)
    {
        try {
            $order = [
                'out_trade_no' => $order_num,
                'total_amount' => $amount,
                'subject' => $body,
            ];
            $config = $this->config;
            $alipay = Pay::alipay($config)->wap($order);
            return $alipay->getContent();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * WEB端支付
     * @param $order_num
     * @param $body
     * @param $amount
     * @return bool|false|string
     */
    public function webPay($order_num, $body, $amount)
    {
        try {
            $order = [
                'out_trade_no' => $order_num,
                'total_amount' => $amount,
                'subject' => $body,
            ];
            $config = $this->config;
            $alipay = Pay::alipay($config)->web($order);
            return $alipay->getContent();
        } catch (\Exception $e) {
            return false;
        }
    }
}