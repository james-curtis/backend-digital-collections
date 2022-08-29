<?php


namespace app\api\controller;


use logicmodel\GoodsLogic;
use think\Request;

class Order extends BaseController
{
    private $goodsLogic;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->goodsLogic = new GoodsLogic();
    }

    /**
     * 下单
     * @param $id
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function apply($id){
        return json($this->goodsLogic->apply($this->uid,$id));
    }

    /**
     * 支付
     * @param $order_id
     * @param int $pay_type
     * @return \think\response\Json
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function pay($order_id,$pay_type=1){

        $is_market = input('is_market',0);
        return json($this->goodsLogic->pay($this->userInfo,$order_id,$pay_type,$is_market));
    }
    
     /**
     * 支付
     * @param $order_id
     * @param int $pay_type
     * @return \think\response\Json
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function toUp($money,$pay_type=1){
        return json($this->goodsLogic->toUp($this->userInfo,$money,$pay_type));
    }
    public function CzList($page=1,$pagesize=10){
        return json($this->goodsLogic->CzList($this->uid,$page,$pagesize));
    }
    /**
     * 订单列表
     * @param int $status
     * @param int $page
     * @param int $pagesie
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function orderList($status=0,$page=1,$pagesize=10){
        return json($this->goodsLogic->orderList($this->uid,$status,$page,$pagesize));
    }

    /**
     * 订单详情
     * @param $id
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function orderDetail($id){
        return json($this->goodsLogic->orderDetail($this->uid,$id));
    }
     
     /**
     * 获取转增记录
     */
    public function getGoodsTransfer(){

        $transfer_type = input('transfer_type',0);

        return json($this->goodsLogic->getGoodsTransfer($transfer_type,$this->uid));
    }
    
    /**
     * 会员拍品列表
     * @param $search
     * @param $goods_category_id
     * @param $page
     * @param $pagesize
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function memberGoodsList($search='',$goods_category_id='',$page=1,$pagesize=10){
        return json($this->goodsLogic->memberGoodsList($this->uid,$search,$goods_category_id,$page,$pagesize));
    }

    /**
     * 会员藏品详情
     * @param $id
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function memberGoodsDetail($id){
        return json($this->goodsLogic->memberGoodsDetail($this->uid,$id));
    }

    /**
     * 会员拍品下单
     * @param $id
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function memberApply($id){
        return json($this->goodsLogic->memberApply($this->uid,$id));
    }
    public function ej(){
        $is_trade = config('site.is_trade');
        return($is_trade);
    }
    /**
     * 藏品列表
     * @param $status
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function collectionList($status){
        return json($this->goodsLogic->collectionList($this->uid,$status));
    }

    /**
     * 藏品详情
     * @param $id
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function collectionDetail($id){
        return json($this->goodsLogic->collectionDetail($this->uid, $id));
    }

    /**
     * 藏品出售
     * @param $id
     * @param $price
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sales($id,$price){
        return json($this->goodsLogic->sales($this->uid,$id,$price));
    }

    /**
     * 切换藏品状态
     * @param $id
     * @param $is_show
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkShow($id,$is_show){
        return json($this->goodsLogic->checkShow($this->uid,$id,$is_show));
    }

    /**
     * 作品转增
     * @param $id
     * @param $target_phone
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function transfer($id,$target_phone){
        return json($this->goodsLogic->transfer($this->uid,$id,$target_phone));
    }
    /**
     * 修改价格
     * @param $id
     * @param $price
     * @return \think\response\Json
     */
    public function checkPrice($id,$price){
        return json($this->goodsLogic->checkPrice($this->uid,$id,$price));
    }

    /**
     * 藏品信息
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function collectionCount(){
        return json($this->goodsLogic->collectionCount($this->uid));
    }

    /**
     * 取消订单
     * @param $id
     * @return \think\response\Json
     */
    public function cancelOrder($id){
        return json($this->goodsLogic->cancelOrder($this->uid,$id));
    }
}