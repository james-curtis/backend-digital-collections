<?php


namespace logicmodel;


use comservice\Response;
use datamodel\ConfigTransfer;
use datamodel\TransferRecord;
use datamodel\Users;
use think\Db;

class TransferLogic
{
    private $transferRecordData;
    private $configTransferData;
    public function __construct()
    {
        $this->transferRecordData = new TransferRecord();
        $this->configTransferData = new ConfigTransfer();
    }

    /**
     * 转增配置
     * @param $currency_id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function configTransfer($currency_id){
      $config =   $this->configTransferData->where(['currency_id'=>$currency_id,'is_show'=>1,'is_del'=>0])->find();
      if($config) return Response::success('success',$config->toArray());
      return Response::fail('暂未开启转增');
    }

    /**
     * 转增
     * @param $userInfo
     * @param $target_phone
     * @param $currency_id
     * @param $account
     * @param $pay_password
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function transfer($userInfo,$target_phone,$currency_id,$account,$pay_password){
        if(empty($userInfo['pay_password'])) return Response::invalidPassword('请先设置支付密码');
        if(md5(md5($pay_password).$userInfo['pay_salt']) != $userInfo['pay_password']) return Response::fail('支付密码错误');
        $config =   $this->configTransferData->where(['currency_id'=>$currency_id,'is_show'=>1,'is_del'=>0])->find();
        if(empty($config)) return Response::fail('未开启转增功能');
        if($account < $config['min']) return Response::fail('最小转增数量'.$config['min']);
        if($account > $config['max']) return Response::fail('最大转增数量'.$config['max']);
        if($account%$config['cardinal_number']) return Response::fail('转增基数为'.$config['cardinal_number']);
       $targetInfo = (new Users())->where(['phone'=>$target_phone,'is_del'=>0])->find();
       if(empty($targetInfo)) return Response::fail('接收人手机号错误');
       $uid = $userInfo['id'];
       $accountLogic =  new AccountLogic();
        Db::startTrans();
        $result = $accountLogic->subAccount($uid,$currency_id,$account,'转增','向会员'.$targetInfo['phone'].'转出');
        if(!$result){
            Db::rollback();
            return Response::fail('余额不足');
        }
        $reality_account = bcdiv($account*(100-$config['rate']),100,2);
        $target_uid = $targetInfo['id'];
        $result = $accountLogic->addAccount($target_uid,$currency_id,$reality_account,'转增','会员'.$userInfo['phone'].'转入');
        if(!$result){
            Db::rollback();
            return Response::fail('转增失败');
        }
        //生成转增记录
        $data['uid'] = $uid;
        $data['target_uid'] = $target_uid;
        $data['currency_id'] = $currency_id;
        $data['account'] = $account;
        $data['reality_account'] = $reality_account;
        $data['create_time'] = date('Y-m-d H:i:s');
        $result = $this->transferRecordData->saveEntityAndGetId($data);
        if($result){
            Db::commit();
            return Response::success('转增成功');
        }
        Db::rollback();
        return Response::fail();
    }

    /**
     * 转增记录
     * @param $uid
     * @param $page
     * @param $pagesize
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function transferRecord($uid,$page,$pagesize){
        $where['uid|target_uid'] = $uid;
        $count = $this->transferRecordData->alias('tf')
            ->join('currency c','c.id = tf.currency_id')
            ->join('users u','u.id = tf.uid')
            ->join('users tu','tu.id = tf.target_uid')
            ->where($where)
            ->count();
        if($count <= 0) return Response::success('暂无数据',['page'=>$page,'pagesize'=>$pagesize,'count'=>$count,'data'=>[]]);
       $data = $this->transferRecordData->alias('tf')
            ->join('currency c','c.id = tf.currency_id')
           ->join('users u','u.id = tf.uid')
           ->join('users tu','tu.id = tf.target_uid')
            ->where($where)
            ->order(['tf.id desc'])
            ->page($page,$pagesize)
            ->field(['tf.*','c.name currency_name','u.phone','tu.phone target_phone'])
            ->select();
       if($data){
           $data = collection($data)->toArray();
           foreach ($data as &$v){
               $v['type'] = $v['uid'] == $uid?1:2;
           }
       }
        return Response::success('success',['page'=>$page,'pagesize'=>$pagesize,'count'=>$count,'data'=>$data]);
    }
}