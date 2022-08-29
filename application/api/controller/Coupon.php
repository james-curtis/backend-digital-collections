<?php


namespace app\api\controller;


use logicmodel\CouponLogic;
use think\Request;

class Coupon extends BaseController
{
    private $couponLogic;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->couponLogic = new CouponLogic();
    }

    /**
     * 我的优惠券
     * @param $status
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function usersCoupon($status=''){
        return json($this->couponLogic->usersCoupon($this->uid,$status));
    }

    /**
     * 券码核销
     * @param $order_num
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function coupon($order_num){
        return json($this->couponLogic->coupon($this->uid,$order_num));
    }

}