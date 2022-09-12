<?php


namespace app\api\controller;



use logicmodel\TaskLogic;
use think\Request;

class Task extends BaseController
{
    protected $noNeedLogin = '*';
    private $taskLogic;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->taskLogic = new TaskLogic();
    }

    /**
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function cancelUnPay(){
        return json($this->taskLogic->cancelUnPay());
    }

}