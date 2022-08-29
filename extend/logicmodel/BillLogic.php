<?php


namespace logicmodel;


use comservice\Response;
use datamodel\Bill;

class BillLogic
{

    private $billData;
    public function __construct()
    {
        $this->billData = new Bill();
    }

    /**
     * 账单流水
     * @param $uid
     * @param $start_time
     * @param $end_time
     * @param $page
     * @param $pagesize
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function billList($uid,$start_time,$end_time,$page,$pagesize){
        $where ['b.uid'] = $uid;
        if(!empty($start_time)) $where['b.create_time'] = ['>=',$start_time];
        if(!empty($end_time)) $where['b.create_time'] = ['<=',$end_time];
        if(!empty($start_time) && !empty($end_time)){
            $where['b.create_time'] = ['between',[$start_time,$end_time]];
        }
        $count = $this->billData->alias('b')
            ->join('currency c','c.id = b.currency_id')
            ->where($where)
            ->count();
        if($count <= 0) return Response::success('暂无数据',['data'=>[],'page'=>$page,'pagesize'=>$pagesize,'count'=>$count]);
        $field = ['b.*','c.name currency_name'];
        $order = ['b.id desc'];
        $data = $this->billData->alias('b')
            ->join('currency c','c.id = b.currency_id')
            ->where($where)
            ->order($order)
            ->page($page,$pagesize)
            ->field($field)
            ->select();
        return Response::success('success',['data'=>$data,'page'=>$page,'pagesize'=>$pagesize,'count'=>$count]);
    }


}