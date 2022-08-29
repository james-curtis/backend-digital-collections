<?php


namespace app\api\controller;


use logicmodel\BillLogic;
use think\Request;

class Bill extends BaseController
{
    private $billLogic;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->billLogic = new BillLogic();
    }

    /**
     * 账单流水
     * @param $start_time
     * @param $end_time
     * @param int $page
     * @param int $pagesize
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function  billList($start_time,$end_time,$page=1,$pagesize=10){
        return json($this->billLogic->billList($this->uid,$start_time,$end_time,$page,$pagesize));
    }

}