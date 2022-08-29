<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/27 0027
 * Time: 22:29
 */

namespace logicmodel;





use comservice\Response;
use datamodel\Goods;
use datamodel\GoodsConfig;
use datamodel\Orders;

class TaskLogic
{

    public function cancelUnPay(){
        $ordersData = new Orders();
       $orderData =  $ordersData->where(['status'=>1,'pay_end_time'=>['<=',date('Y-m-d H:i:s')]])->select();
       if(empty($orderData)) return Response::fail('暂未有订单需要取消');
        $goodsConfigData = new GoodsConfig();
        $goodsData = new Goods();
        $cancel = [];
       foreach ($orderData as $v){
            $goods_config_id = $v['goods_config_id'];
            if($goods_config_id > 0){
                $goodsConfigData->where(['id'=>$goods_config_id])->setInc('surplus',1);
                $goodsConfigData->where(['id'=>$goods_config_id])->setDec('sales',1);
            }
            $goodsData->where(['id'=>$v['buy_goods_id']])->setInc('surplus',1);
            $goodsData->where(['id'=>$v['buy_goods_id']])->setDec('sales',1);
            $cancel[] = $v['id'];
        }
        $ordersData->where(['id'=>['in',$cancel]])->update(['status'=>3]);
       return Response::success('未付款订单取消成功');
    }

}