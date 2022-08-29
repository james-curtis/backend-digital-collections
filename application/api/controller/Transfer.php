<?php


namespace app\api\controller;


use logicmodel\TransferLogic;
use think\Request;

class Transfer extends BaseController
{
    private $transferLogic;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->transferLogic = new TransferLogic();
    }

    /**
     * 转增配置
     * @param $currency_id
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function configTransfer($currency_id){
        return json($this->transferLogic->configTransfer($currency_id));
    }

    /**
     * 转增
     * @param $target_phone
     * @param $currency_id
     * @param $account
     * @param $pay_password
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function transfer($target_phone,$currency_id,$account,$pay_password){
        return json($this->transferLogic->transfer($this->userInfo,$target_phone,$currency_id,$account,$pay_password));
    }

    /**
     * 转增记录
     * @param $page
     * @param $pagesize
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function transferRecord($page=1,$pagesize=10){
        return json($this->transferLogic->transferRecord($this->uid,$page,$pagesize));
    }
}