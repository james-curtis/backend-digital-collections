<?php


namespace logicmodel;


use app\admin\model\GoodsMangheUsers;
use datamodel\GoodsUsers;
use datamodel\Orders;
use EasyWeChat\Factory;
use think\Db;
use Yansongda\Pay\Pay;

class NotifyLogic
{
    private $ordersData;
    private $goodsUsersData;

    public function __construct()
    {
        $this->ordersData = new Orders();
        $this->goodsUsersData = new GoodsUsers();
    }

    /**
     * 支付宝回调
     * @return string
     */
    // public function aliNotify(){
    //     $config = [
    //         'use_sandbox' => true, // 是否使用沙盒模式
    //         'app_id'    => config('site.ali_app_id'),
    //         'sign_type' => 'RSA2', // RSA  RSA2
    //         // 支付宝公钥字符串
    //         'ali_public_key' => config('site.ali_public_key'),
    //         // 自己生成的密钥字符串
    //         'private_key' => config('site.ali_private_key'),
    //     ];
    //     $alipay = Pay::alipay($config);
    //     try{
    //         $data = $alipay->verify(); 
    //         $order_num = $data['out_trade_no'];
    //       $info =  $this->ordersData->where(['order_num'=>$order_num,'status'=>1])->find();
    //       if(empty($info)) return 'FAIL';
    //       $result =  $this->goods($info['id'],$info['sale_uid'],$info['price'],$info['goods_users_id'],$info['goods_id'],$info['goods_num']);
    //         if(!$result)   return 'FAIL';
    //         (new GoodsLogic())->sub_commission($info['buy_id'],$info['price']);
    //     } catch (\Exception $e) {
    //          $e->getMessage();
    //          return 'FAIL';
    //     }
    //     return $alipay->success()->send();
    // }
    public function aliNotifyOld()
    {
        $config = (new AliLogic())->getConfig();
        $alipay = Pay::alipay($config);
        try {
            $data = $alipay->verify();
            $order_num = $data['out_trade_no'];
            $info = Db::name('chongzhi')->where(['order_num' => $order_num, 'status' => 0])->find();
            if (empty($info)) return 'FAIL';
            $result = $this->cz($info['order_num']);
            if (!$result) return 'FAIL';

        } catch (\Exception $e) {
            $e->getMessage();
            return 'FAIL';
        }
        return $alipay->success()->send();
    }

    public function aliNotify()
    {
        $paytype = 'alipay';
        $pay = \addons\epay\library\Service::checkNotify($paytype);
        if (!$pay) {
            echo '签名错误';
            return;
        }
        $data = $pay->verify();
        try {
            $payamount = $paytype == 'alipay' ? $data['total_amount'] : $data['total_fee'] / 100;
            $out_trade_no = $data['out_trade_no'];
            $info = Db::name('chongzhi')->where(['order_num' => $out_trade_no, 'status' => 0])->find();
            if (empty($info)) return 'FAIL';
            $result = $this->cz($info['order_num']);
            //你可以在此编写订单逻辑
        } catch (\Exception $e) {
            return 'FAIL';
        }
        return $pay->success();
    }

    public function cz($order_num)
    {
        $info = Db::name('chongzhi')->where(array('order_num' => $order_num))->find();
        Db::name('chongzhi')->where(array('order_num' => $order_num))->update(array('status' => 1));
        $result = (new AccountLogic())->addAccount($info['uid'], 1, $info['money'], '前端用户充值', '前端用户充值');
        return true;
    }

    /**
     * 微信回调
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function wxNotify()
    {
        $payConfig = [
            'app_id' => config('site.wx_app_id'),
            'mch_id' => config('site.wx_mch_id'),
            'key' => config('site.wx_mch_key'),
        ];
        $app = Factory::payment($payConfig);
        $response = $app->handlePaidNotify(function ($message, $fail) {
            $out_trade_no = $message['out_trade_no'];


            $info = $this->ordersData->where(['order_num' => $out_trade_no, 'status' => 1])->find();
            if (empty($info)) return 'FAIL';
            if ($info['status'] != 1) return 'FAIL';
            if ($message['return_code'] === 'SUCCESS') {
                $result = $this->goods($info['id'], $info['sale_uid'], $info['price'], $info['goods_users_id'], $info['goods_id'], $info['goods_num']);
                if (!$result) return $fail('通信失败，请稍后再通知我');
                (new GoodsLogic())->sub_commission($info['buy_id'], $info['price']);
            } else {
                return $fail('通信失败，请稍后再通知我');
            }
        });
        return $response->send();
    }
    /**
     * 微信公众号回调
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    // public function wxGzhNotify(){
    //     $payConfig = [
    //         'app_id' => config('site.wx_appid'),
    //         'mch_id' => config('site.wx_mch_id'),
    //         'key' => config('site.wx_mch_key'),
    //     ];
    //     $app = Factory::payment($payConfig);
    //     $response = $app->handlePaidNotify(function ($message, $fail) {
    //         $out_trade_no =  $message['out_trade_no'];
    //         $info =$this->ordersData->where(['order_num'=>$out_trade_no,'status'=>1])->find();
    //         if(empty($info)) return 'FAIL';
    //         if($info['status'] != 1)    return 'FAIL';
    //         if ($message['return_code'] === 'SUCCESS') {
    //             $result =  $this->goods($info['id'],$info['sale_uid'],$info['price'],$info['goods_users_id'],$info['goods_id'],$info['goods_num']);
    //             if(!$result) return $fail('通信失败，请稍后再通知我');
    //             (new GoodsLogic())->sub_commission($info['buy_id'],$info['price']);
    //         } else {
    //             return $fail('通信失败，请稍后再通知我');
    //         }
    //     });
    //   return   $response->send();
    // }
    public function wxGzhNotifyOld()
    {
        $payConfig = [
            'app_id' => config('site.wx_appid'),
            'mch_id' => config('site.wx_mch_id'),
            'key' => config('site.wx_mch_key'),
        ];
        $app = Factory::payment($payConfig);
        $response = $app->handlePaidNotify(function ($message, $fail) {
            $out_trade_no = $message['out_trade_no'];
            $info = Db::name('chongzhi')->where(['order_num' => $out_trade_no, 'status' => 0])->find();
            if (empty($info)) return 'FAIL';
            $result = $this->cz($info['order_num']);
            if (!$result) return 'FAIL';
        });
        return $response->send();
    }
    public function wxGzhNotify()
    {
        $paytype = 'wechat';
        $pay = \addons\epay\library\Service::checkNotify($paytype);
        if (!$pay) {
            echo '签名错误';
            return;
        }
        $data = $pay->verify();
        try {
            $payamount = $paytype == 'alipay' ? $data['total_amount'] : $data['total_fee'] / 100;
            $out_trade_no = $data['out_trade_no'];

            //你可以在此编写订单逻辑
            $info = Db::name('chongzhi')->where(['order_num' => $out_trade_no, 'status' => 0])->find();
            if (empty($info)) return 'FAIL';
            $result = $this->cz($info['order_num']);
            if (!$result) return 'FAIL';
        } catch (\Exception $e) {
            echo 'FAIL';
        }
        echo $pay->success();
    }

    /**
     * 微信小程序回调地址
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function wxSmallNotify()
    {
        $payConfig = [
            'app_id' => config('site.wx_small_app_id'),
            'mch_id' => config('site.wx_mch_id'),
            'key' => config('site.wx_mch_key'),
        ];
        $app = Factory::payment($payConfig);
        $response = $app->handlePaidNotify(function ($message, $fail) {
            $out_trade_no = $message['out_trade_no'];
            $info = $this->ordersData->where(['order_num' => $out_trade_no, 'status' => 1])->find();
            if (empty($info)) return 'FAIL';
            if ($info['status'] != 1) return 'FAIL';
            if ($message['return_code'] === 'SUCCESS') {
                $result = $this->goods($info['id'], $info['sale_uid'], $info['price'], $info['goods_users_id'], $info['goods_id'], $info['goods_num']);
                if (!$result) return $fail('通信失败，请稍后再通知我');
            } else {
                return $fail('通信失败，请稍后再通知我');
            }
        });
        return $response->send();
    }

    /**
     * 转让拍品
     * @param $order_id
     * @param $uid
     * @param $price
     * @param $goods_users_id
     * @param $goods_id
     * @param $goods_num
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function goods($order_id, $uid, $price, $goods_users_id, $goods_id, $goods_num)
    {
        $accountLogic = new AccountLogic();
        $time = date('Y-m-d H:i:s');
        Db::startTrans();
        // $result = $this->ordersData->updateByWhere(['id'=>$order_id],['status'=>2,'pay_time'=>$time]);//订单已支付
        $result = Db::name('orders')->where(array('id' => $order_id))->update(['status' => 2]);
        if (!$result) {
            Db::rollback();
            return false;
        }

        //查询订单信息 判断是否是盲盒购买
        $info = $this->ordersData->where(['id' => $order_id])->find();
        if ($info['order_type'] == 3) {
            //盲盒购买
            $goodsMangheUsersData = new GoodsMangheUsers();
            $result = $goodsMangheUsersData->where(['id' => $info['goods_manghe_users_id']])->update(['status' => 2]);
            if (!$result) {
                Db::rollback();
                return false;
            } else {
                Db::commit();
                return true;
            }
        } else {
            $result = $accountLogic->addAccount($uid, 1, $price, '出售藏品', '出售藏品');
            if (!$result) {
                Db::rollback();
                return false;
            }

            $result = $this->goodsUsersData->where(['id' => $goods_users_id])->update(['status' => 4]);
            if (!$result) {
                Db::rollback();
                return false;
            }
            $goods_user_number = $this->goodsUsersData->where(['goods_id' => $goods_id])->whereNotNull('number')->order('id', 'desc')->value('number');

            if ($goods_user_number) {
                $goods_user_number = str_pad($goods_user_number + 1, 6, '0', STR_PAD_LEFT);
            } else {
                $goods_user_number = '000001';
            }
            $buy_uid = $this->ordersData->where('id', $order_id)->value('buy_uid');
            $usersGoods['uid'] = $buy_uid;
            $usersGoods['goods_id'] = $goods_id;
            $usersGoods['goods_number'] = $goods_num;
            $usersGoods['price'] = $price;
            $usersGoods['create_time'] = $time;
            $usersGoods['number'] = $goods_user_number;
            $result = $this->goodsUsersData->insertGetId($usersGoods);
            if ($result) {
                Db::commit();
                return true;
            }
        }
        Db::rollback();
        return false;
    }

}