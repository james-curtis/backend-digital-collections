<?php


namespace app\api\controller;


use logicmodel\GoodsLogic;
use think\Controller;
use think\Request;
use datamodel\Users;

class Goods extends BaseController
{
    protected $noNeedRight = ['*'];

    private $goodsLogic;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->goodsLogic = new GoodsLogic();
    }

    /**
     * 商品列表
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
    public function goodsList($search = '', $goods_category_id = '', $page = 1, $pagesize = 10)
    {
        return json($this->goodsLogic->goodsList($search, $goods_category_id, $page, $pagesize));
    }

    /**
     * 商品详情
     * @param $id
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function goodsDetail($id)
    {
        //print_r($this->userInfo);
        return json($this->goodsLogic->goodsDetail($id, $this->uid));
    }

    /**
     * 商品分类列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function goodsCategoryList()
    {
        return json($this->goodsLogic->goodsCategoryList());
    }

    /**
     * 会员作品分类列表
     * @param $search
     * @param string $goods_category_id
     * @param int $page
     * @param int $pagesize
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function memberGoodsList($search = '', $goods_category_id = '', $page = 1, $pagesize = 10)
    {
        return json($this->goodsLogic->memberGoodsList('', $search, $goods_category_id, $page, $pagesize));
    }

    /**
     * 首页商品推荐
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function indexGoods()
    {
        return json($this->goodsLogic->indexGoods());
    }

    /**
     * 二手市场详情
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function indexMemberGoods()
    {
        return json($this->goodsLogic->indexMemberGoods());
    }

    /**
     * 出售记录
     * @param $id
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function goodsRecord($id)
    {
        return json($this->goodsLogic->goodsRecord($id));
    }

    /**
     * 二手市场交易纪录
     * @param $goods_number
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function memberGoodsRecord($goods_number)
    {
        return json($this->goodsLogic->memberGoodsRecord($goods_number));
    }

    /**
     * 预约
     */
    public function setAppointment($id)
    {
        return json($this->goodsLogic->Appointment($id, $this->uid));
    }

    public function buy_rl_handle($phone, $code)
    {
        return json(config('site.buy_ri_handle'));
    }


}