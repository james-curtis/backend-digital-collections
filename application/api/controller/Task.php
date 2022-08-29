<?php


namespace app\api\controller;



use logicmodel\TaskLogic;

class Task
{

    private $taskLogic;
    public function __construct()
    {
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