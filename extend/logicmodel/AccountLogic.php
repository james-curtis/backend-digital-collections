<?php


namespace logicmodel;


use comservice\Response;
use datamodel\Bill;
use datamodel\Currency;
use datamodel\UsersAccount;
use think\Db;

class AccountLogic
{
    private $userAccountData;
    private $billData;
    private $currencyData;
    public function __construct()
    {
        $this->userAccountData = new UsersAccount();
        $this->billData = new Bill();
        $this->currencyData = new Currency();
    }

    /**
     * 增加账户余额
     * @param $uid
     * @param $currency_id
     * @param $account
     * @param $bill_type
     * @param $remark
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function  addAccount($uid,$currency_id,$account,$bill_type,$remark){
        $where = ['uid'=>$uid,'currency_id'=>$currency_id];
        $accountInfo = $this->userAccountData->where($where)->lock(true)->find();
        Db::startTrans();
        if($accountInfo){
            $before_account = $accountInfo['account']; //变动前总账户
            $after_account = bcadd($account,$before_account,10); //变动后
            $result =  $this->userAccountData->where(['id'=>$accountInfo['id']])->update(['account'=>$after_account]);
            if($result <= 0){
                Db::rollback();
                return false;
            }
        }else{
            $accountData['uid'] = $uid;
            $accountData['currency_id'] = $currency_id;
            $accountData['account'] = $account;
            $accountData['create_time'] = date('Y-m-d H:i:s');
            $result = $this->userAccountData->saveEntityAndGetId($accountData);
            if($result <= 0){
                Db::rollback();
                return false;
            }
            $before_account = 0;
            $after_account = $account;
        }
        $result =  $this->bill($uid,$currency_id,$account,$before_account,$after_account,$bill_type,$remark,1);
        if($result > 0){
            Db::commit();
            return true;
        }
        Db::rollback();
        return false;
    }


    /**
     * 扣除系统账户
     * @param $uid
     * @param $currency_id
     * @param $account
     * @param $bill_type
     * @param $remark
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function  subAccount($uid,$currency_id,$account,$bill_type,$remark){
        $where = ['uid'=>$uid,'currency_id'=>$currency_id];
        $accountInfo = $this->userAccountData->where($where)->lock(true)->find();
        if(empty($accountInfo)) return false;
        if($accountInfo['account'] < $account) return false;
        $before_account = $accountInfo['account']; //变动前总账户
        $after_account = bcsub($before_account,$account,10);
        Db::startTrans();
        $result = $this->userAccountData->updateByWhere(['id'=>$accountInfo['id']],['account'=>$after_account]);
        if ($result > 0) {
            $result = $this->bill($uid,$currency_id,$account,$before_account,$after_account,$bill_type,$remark,2);
            if($result > 0){
                Db::commit();
                return true;
            }
        }
        Db::rollback();;
        return false;
    }


    /**
     * 生成系统流水
     * @param $uid
     * @param $currency_id
     * @param $account
     * @param $before_account
     * @param $after_account
     * @param $bill_type
     * @param $remark
     * @param $type
     * @return bool
     */
    private function bill($uid,$currency_id,$account,$before_account,$after_account,$bill_type,$remark,$type){
        $data['uid'] = $uid;
        $data['currency_id'] = $currency_id;
        $data['account'] = $account;
        $data['before_account'] = $before_account;
        $data['after_account'] = $after_account;
        $data['bill_type'] = $bill_type;
        $data['remark'] = $remark;
        $data['type'] = $type;
        $data['create_time'] = date('Y-m-d H:i:s');
        $id = $this->billData->saveEntityAndGetId($data);
        if($id > 0) return true;
        return false;
    }

    /**
     * 账户余额
     * @param $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userAccount($uid){
        $currencyData =  (new Currency())->field(['id','name','image','digit'])->select();
        $currencyData =  collection($currencyData)->toArray();
        $userAccount = $this->userAccountData->where(['uid'=>$uid])->column('id,account','currency_id');
        foreach ($currencyData as &$v){
            $currency_id = $v['id'];
            if(!empty($userAccount[$currency_id])){
                $v['account'] = bcadd($userAccount[$currency_id]['account'],0,$v['digit']);
            }else{
                $v['account'] = bcadd(0,0,$v['digit']);
            }
        }
        $data = array_column($currencyData,null,'id');
        return Response::success('success',$data);
    }

}