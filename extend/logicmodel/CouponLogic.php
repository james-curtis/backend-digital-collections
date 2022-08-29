<?php


namespace logicmodel;


use comservice\Response;
use datamodel\Coupon;
use datamodel\UsersCoupon;

class CouponLogic
{
    private $couponData;
    private $usersCouponData;
    public function __construct()
    {
        $this->couponData = new Coupon();
        $this->usersCouponData = new UsersCoupon();
    }

    /**
     * 优惠券列表
     * @param $uid
     * @param $status
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function usersCoupon($uid,$status){
        $field = ['uc.*','c.name','c.money','c.desc'];
        $where['uc.uid'] = $uid;
       if($status !== '') $where['uc.status'] = $status;
        $data = $this->usersCouponData->alias('uc')
            ->join('coupon c','c.id = uc.coupon_id')
            ->where($where)
            ->field($field)
            ->order(['uc.id desc'])
            ->select();
        if($data){
            $data = collection($data)->toArray();
            return Response::success('success',$data);
        }
        return Response::success('暂无优惠券',[]);
    }

    /**
     * 券码核销
     * @param $uid
     * @param $order_num
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function coupon($uid,$order_num){
      $info = $this->usersCouponData->where(['order_num'=>$order_num,'status'=>1])->find();
      if(empty($info)) return Response::fail('券码已核销');
      $result = $this->usersCouponData->updateByWhere(['id'=>$info['id']],['write_uid'=>$uid,'status'=>2,'use_time'=>date('Y-m-d H:i:s')]);
      if($result) return Response::success('核销成功');
      return Response::fail('核销失败');
    }

}