<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/3 0003
 * Time: 22:11
 */

namespace logicmodel;


use comservice\Response;
use datamodel\ConfigPay;
use datamodel\RemittanceRecord;
use think\Db;

class RemittanceLogic
{
    private $remittanceRecordData;
    private $configPayData;
    public  function __construct()
    {
        $this->remittanceRecordData = new RemittanceRecord();
        $this->configPayData = new ConfigPay();
    }

    /**
     * 汇款记录
     * @param $uid
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function remittanceRecordList($uid){
        $where['rr.uid'] = $uid;
        $where['rr.status'] = 1;
       $field = ['rr.account','rr.status','rr.create_time','cp.name config_pay_name'];
       $data =  $this->remittanceRecordData->alias('rr')
            ->join('currency c','c.id = rr.currency_id')
            ->join('config_pay cp','cp.id = rr.config_pay_id')
            ->where($where)
            ->order(['rr.id desc'])
            ->field($field)
            ->select();
      if($data) return Response::success('success',collection($data)->toArray());
      return   Response::success('暂无数据',[]);
    }

    /**
     * 支付列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function configPayList(){
       $data =  $this->configPayData->where(['is_show'=>1])->select();
       if($data) {
            $data = collection($data)->toArray();
            $data = addWebSiteUrl($data,['image']);
            return Response::success('success',$data);
       }
       return Response::success('success');
    }

    /**
     * 充值
     * @param $uid
     * @param $config_pay_id
     * @param $account
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function  remittance($uid,$config_pay_id,$account){
       $config =  $this->configPayData->where(['is_show'=>1,'id'=>$config_pay_id])->find();
       if(empty($config)) return  Response::fail('未开启充值通道');
        if($account < $config['min']) return Response::fail('最小充值数量'.$config['min']);
        if($account > $config['max']) return Response::fail('最大充值数量'.$config['max']);
        //生成充值记录
        $order_num = uniqueNum();
        $data['uid'] = $uid;
        $data['order_num'] = $order_num;
        $data['currency_id'] = 1;
        $data['account'] = $account;
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['config_pay_id'] = $config_pay_id;
        Db::startTrans();
        $order_id = $this->remittanceRecordData->saveEntityAndGetId($data);
        if(!$order_id){
            Db::rollback();
            return Response::fail('订单生成失败');
        }
        if($config_pay_id == 1){
            $result = (new WxLogic())->pay($order_num,'余额充值',$account,$config['notify_url']);
        }elseif($config_pay_id == 2){
            $result = (new AliLogic())->pay($order_num,'余额充值',$account,$config['notify_url']);
        }else{
            Db::rollback();
            return Response::fail('订单生成失败');
        }

        if(!$result){
            Db::rollback();
            return Response::fail('订单生成失败');
        }
        Db::commit();
        return Response::success('下单成功',['pay'=>$result]);
    }
}