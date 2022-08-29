<?php


namespace logicmodel;



use comservice\Response;
use datamodel\Users;
use EasyWeChat\Factory;

class WxLogic
{
    private $config;
    public  $message='支付失败';
    public function __construct()
    {
        $this->config = [
            'app_id' => config('site.wx_app_id'), // APP APPID
            'mch_id' => config('site.wx_mch_id'),
            'key' => config('site.wx_mch_key'),
            'cert_path' => config('site.wx_client_cert'), // optional，退款等情况时用到
            'key_path' => config('site.wx_client_pem'),// optional，退款等情况时用到,
            'notify_url'=>config('site.wx_notify_url')
        ];
    }

    /**
     * APP支付
     * @param $order_num
     * @param $body
     * @param $amount
     * @return array|bool
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function  appPay($order_num,$body,$amount){
        $payConfig = $this->config;
        $payConfig['notify_url'] = config('site.wx_notify_url');
        $app = Factory::payment($payConfig);
        $order = [
            'body' => $body,
            'out_trade_no' => $order_num,
            'total_fee' => bcmul($amount,100,0),
            'trade_type' => 'APP', // 请对应换成你的支付方式对应的值类型
        ];
        $result = $app->order->unify($order);
        if($result['return_code'] == 'SUCCESS' && $result['result_code'] != 'FAIL'){
            $prepay_id = $result['prepay_id'];
            return $app->jssdk->appConfig($prepay_id);
        }
        return false;
    }

    /**
     * 小程序支付
     * @param $order_num
     * @param $body
     * @param $amount
     * @param $openid
     * @return array|bool|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function smallPay($order_num,$body,$amount,$openid){
        $notify_url = config('site.wx_small_notify_url');
        $config = [
            'app_id' => config('site.wx_small_app_id'), // 小程序 APPID
            'mch_id' => config('site.wx_mch_id'),
            'key' => config('site.wx_mch_key'),
            'cert_path' => config('site.wx_client_cert'), // optional，退款等情况时用到
            'key_path' => './cert/apiclient_key.pem',// optional，退款等情况时用到
        ];
        $config['notify_url'] = $notify_url;
        $app = Factory::payment($config);
        $order = [
            'body' => $body,
            'out_trade_no' => $order_num,
            'total_fee' => bcmul($amount,100,0),
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid'=>$openid,
        ];
        $result = $app->order->unify($order);
        if($result['return_code'] == 'SUCCESS' && $result['result_code'] != 'FAIL'){
            $prepay_id = $result['prepay_id'];
            return $app->jssdk->bridgeConfig($prepay_id,false);
        }
        return false;
    }


    /**
     * 企业付款到个人
     * @param $order_num
     * @param $open_id
     * @param $amount
     * @param $name
     * @return bool|mixed
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function draw($order_num,$open_id,$amount){
           try {
        $payConfig = $this->config;
        $payConfig['mch_id'] = config('site.wx_draw_mch_id');
        $payConfig['key'] = config('site.wx_draw_mch_key');
        $app = Factory::payment($payConfig);
        $result = $app->transfer->toBalance(
            [
                'partner_trade_no' => $order_num, // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
                'openid' => $open_id,
                'check_name' => 'NO_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
                'amount' => bcmul($amount, 100, 0),
                'desc' => '佣金提现', // 企业付款操作说明信息。必填
            ]
        );
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') return $result['payment_no'];
        $this->message = $result['err_code_des'];
        return false;
         }catch (\Exception $e){
             return  false;
         }
    }


    /**
     * 微信小程序授权
     * @param $userInfo
     * @param $code
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function  auth($userInfo,$code){
        if($userInfo['wx_small_auth'] == 1){
            return Response::fail('授权失败');
        }
        $config = [
            'app_id' => config('site.wx_small_app_id'), // APP APPID
            'secret' => config('site.wx_small_app_secret'),
        ];
        $app =  Factory::miniProgram($config);
        $result = $app->auth->session($code);
        $openid = $result['openid'];
        $unionid = $result['unionid'];
        $data['wx_small_auth'] = 1;
        $data['wx_small_openid'] = $openid;
        $data['wx_union_id'] = $unionid;
        $result = (new Users())->updateByWhere(['id'=>$userInfo['id']],$data);
        if($result) return Response::success('授权成功');
        return Response::fail('授权失败');
    }

    /**
     * WEB支付
     * @param $order_num
     * @param $body
     * @param $amount
     * @return array|bool
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function webPay($order_num,$body,$amount){
        $payConfig = $this->config;
        $payConfig['notify_url'] = config('site.wx_notify_url');
        $app = Factory::payment($payConfig);
        $order = [
            'body' => $body,
            'out_trade_no' => $order_num,
            'total_fee' => bcmul($amount,100,0),
            'trade_type' => 'NATIVE', // 请对应换成你的支付方式对应的值类型
        ];
        $result = $app->order->unify($order);
        if($result['return_code'] == 'SUCCESS' && $result['result_code'] != 'FAIL'){
            // $prepay_id = $result['prepay_id'];
            // return $app->jssdk->sdkConfig($prepay_id);
            return $result;
        }
        return false;
    }
}