<?php


namespace logicmodel;


use comservice\Response;
use datamodel\ConfigDraw;
use datamodel\Currency;
use datamodel\DrawRecord;
use function fast\e;
use think\Db;

class DrawLogic
{
    private $configDrawData;
    private $drawRecordData;
    private $currencyData;
    public function __construct()
    {
        $this->configDrawData = new ConfigDraw();
        $this->drawRecordData = new DrawRecord();
        $this->currencyData = new Currency();
    }

    /**
     * 配置信息
     * @param $currency_id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function configDraw($currency_id){
        $data = $this->configDrawData->where(['is_del'=>0,'is_show'=>1,'currency_id'=>$currency_id])->find();
       if($data) return  Response::success('success',$data->toArray());
       return  Response::fail('暂无开启提现');
    }

    /**
     * 提现
     * @param $userInfo
     * @param $account
     * @param $type
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
       public function  draw($userInfo,$account,$type){
           if($type == 1){
               if($userInfo['is_bank'] == 0) return  Response::fail('请先完善银行卡信息');
           }elseif($type == 2){
               if($userInfo['is_ali'] == 0) return  Response::fail('请先完善支付宝信息');
           }elseif ($type == 3){
               if($userInfo['is_wx'] == 0) return  Response::fail('请先完善微信信息');
           }else{
             return  Response::fail('提现方式错误');
           }
         $field = ['cd.*','c.name'];
         $currency_id  = 1;
         $config = $this->configDrawData->alias('cd')
             ->join('currency c','c.id = cd.currency_id')
             ->where(['cd.currency_id'=>$currency_id,'cd.is_del'=>0,'cd.is_show'=>1])
             ->field($field)
             ->find();
         if(empty($config)) return Response::fail('暂不支持提现');
         if($account > $config['max']) return Response::fail('超出最大提现金额');
         if($account < $config['min']) return Response::fail('低于最小提现金额');

         $uid = $userInfo['id'];
         Db::startTrans();
         $result = (new AccountLogic())->subAccount($uid,$currency_id,$account,'提现','提现');
         if($result == false){
             Db::rollback();
             return Response::fail('账户余额不足');
         }
         $reality_account = bcdiv($account*(100-$config['rate']),100,8);
         $data['order_num'] = uniqueNum();
         $data['currency_id'] = $currency_id;
         $data['uid'] = $uid;
         $data['account'] = $account;
         $data['reality_account'] = $reality_account;
         $data['type'] = $type;
         $data['create_time'] = date('Y-m-d H:i:s');
         if($type == 1){
             $data['bank_name'] = $userInfo['bank_name'];
             $data['bank_number'] = $userInfo['bank_number'];
             $data['bank_owner'] = $userInfo['bank_owner'];
             $data['bank_branch'] = $userInfo['bank_branch'];
         }elseif ($type == 2){
             $data['ali_name'] = $userInfo['ali_name'];
             $data['ali_image'] = $userInfo['ali_image'];
         }elseif ($type == 3){
             $data['wx_name'] = $userInfo['wx_name'];
             $data['wx_image'] = $userInfo['wx_image'];
         }
         $result = $this->drawRecordData->saveEntityAndGetId($data);
         if($result){
             Db::commit();
             return Response::success('申请成功');
         }
         Db::rollback();
         return Response::fail('申请失败');
    }

    /**
     * 提现记录
     * @param $uid
     * @param $status
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function drawRecordList($uid,$status){
         $where ['dr.uid'] = $uid;
        if($status !== '') $where['dr.status'] = $status;
        $field = ['dr.*','c.name currency_name'];
        $data = $this->drawRecordData->alias('dr')
            ->join('currency c','c.id = dr.currency_id')
            ->where($where)
            ->field($field)
            ->order(['dr.id desc'])
            ->select();
        if($data) return Response::success('success',collection($data)->toArray());
        return Response::success('暂无数据',[]);
     }
}