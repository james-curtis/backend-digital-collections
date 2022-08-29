<?php


namespace app\api\controller;


use logicmodel\RemittanceLogic;
use think\Request;

class Remittance extends BaseController
{
    private $remittanceLogic;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->remittanceLogic = new RemittanceLogic();
    }

    /**
     * 充值配置列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function configPayList(){
        return json($this->remittanceLogic->configPayList());
    }

    /**
     * 充值
     * @param $config_pay_id
     * @param $account
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function  remittance($config_pay_id,$account){
        return json($this->remittanceLogic->remittance($this->uid,$config_pay_id,$account));
    }

    /**
     * 充值记录
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function remittanceRecordList(){
        return json($this->remittanceLogic->remittanceRecordList($this->uid));
    }

}