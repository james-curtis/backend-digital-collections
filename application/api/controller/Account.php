<?php


namespace app\api\controller;


use logicmodel\AccountLogic;
use think\Request;

class Account extends BaseController
{
    private $accountLogic;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->accountLogic = new AccountLogic();
    }

    /**
     * 会员账户余额
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function account(){
        return json($this->accountLogic->userAccount($this->uid));
    }

}