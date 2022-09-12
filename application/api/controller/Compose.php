<?php
/**
 * 合成接口
 */

namespace app\api\controller;


use app\admin\model\ChipComposeRecord;
use app\admin\model\ChipUsers;
use comservice\Response;
use datamodel\Goods;
use datamodel\GoodsUsers;
use think\Db;
use think\Request;
// require_once('../xasset/index.php');
class Compose extends BaseController
{
    protected $noNeedRight = ['*'];
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    /**
     * 获取合成配置规则
     */
    public function getComposeConfig(){
        $data['compose_num'] = config('site.compose_num');
        $data['rule'] = config('site.rule');
        return json(Response::success('success',$data));
    }

    /**
     * 获取用户碎片列表
     */
    public function getUserChipsList()
    {
        $goods= new Goods();
        $goodss=$goods->where('hcgoods_id','not null')->field('id,hcgoods_id,image')->select();
        foreach ($goodss as $value) {
           
        }
        print_r($goodss);
        $chipUsersModel =  new ChipUsers();
        $where['c.user_id'] = $this->uid;
        $where['g.is_can_buy'] = 1;
        $where['g.is_manghe'] = 0;
        
        $data = $chipUsersModel->alias('c')->join('goods g', 'g.id=c.goods_id')
            ->field('c.*, g.name, g.image, g.price')
            ->where($where)
            ->select();
        if($data)
        {
            $data = collection($data)->toArray();
            foreach ($data as &$v)
            {
                $v = addWebSiteUrl($v, ['image']);
            }
        }
        $responsData = Response::success('success', $data);

        return json($responsData);
    }

    /**
     * 碎片合成
     */
    public function chipCompose($chip_id = '')
    {
        $chipUsersModel =  new ChipUsers();
        if(!$chip_id)
        {
            $responsData =  Response::fail('碎片信息错误!');
            return json($responsData);
        }
        //查询改碎片信息 判断是否可以合成
        $chipData = $chipUsersModel->where('id', $chip_id)->find();
        if($chipData)
        {
            $compose_num = config('site.compose_num');
            if($chipData['total'] < $compose_num)
            {
                $responsData =  Response::fail('碎片数量不足，无法合成!');
                return json($responsData);
            }
            //可以合成
            Db::startTrans();
            //合成 碎片减少
            $res = $chipUsersModel->where('id', $chip_id)->setDec('total', $compose_num);
            if(!$res)
            {
                Db::rollback();
                $responsData =   Response::fail('碎片合成失败!');
                return json($responsData);
            }
            //检查当前合成的碎片商品是否已经 修改为非碎片
            $GoodsModel = new Goods();
            $goodInfo = $GoodsModel->where('id', $chipData['goods_id'])->find();
            if(!$goodInfo )
            {
                Db::rollback();
                $responsData =   Response::fail('碎片合成失败!');
                return json($responsData);
            }
            if($goodInfo['is_chip'])
            {
                //更新为非碎片
                $result = $GoodsModel->where('id', $goodInfo['id'])->update(['is_chip'=>0]);
                if(!$result )
                {
                    Db::rollback();
                    $responsData =   Response::fail('碎片合成失败!');
                    return json($responsData);
                }
            }
            //添加合成记录
            $chipComposeRecordModel = new ChipComposeRecord();
            $chipComposeRecordData['user_id'] = $this->uid;
            $chipComposeRecordData['goods_id'] = $goodInfo['id'];
            $chipComposeRecordData['compose_num'] = $compose_num;

            //添加到我的藏品中
            $goodsUsersData = new GoodsUsers();
            $goods_user_number = $goodsUsersData->where(['goods_id'=>$goodInfo['id']])->whereNotNull('number')->order('id', 'desc')->value('number');
            if ($goods_user_number) {
                $goods_user_number = str_pad($goods_user_number + 1,6,'0',STR_PAD_LEFT);
            } else {
                $goods_user_number = '000001';
            }
            $time                  = date('Y-m-d H:i:s');
            $goods_number          = uniqueNum();
            $usersGoods['uid']          = $this->uid;
            $usersGoods['goods_id']     = $goodInfo['id'];
            $usersGoods['goods_number'] = $goods_number;
            $usersGoods['price']        = $goodInfo['price'];
            $usersGoods['create_time']  = $time;
            $usersGoods['status']       = 1; //待出售
            $usersGoods['number']       = $goods_user_number;
            $results = $goodsUsersData->insertGetId($usersGoods);
            if (!$results) {
                Db::rollback();
                $responsData =  Response::fail('碎片合成失败!');
                return json($responsData);
            }
            //减少 藏品份额
            $GoodsModel->where(['id' => $goodInfo['id']])->setDec('surplus', 1);
            $GoodsModel->where(['id' => $goodInfo['id']])->setInc('sales', 1);

            $result = $chipComposeRecordModel->save($chipComposeRecordData);
            if(!$result )
            {
                Db::rollback();
                $responsData =   Response::fail('碎片合成失败!');
                return json($responsData);
            }
            if($results){
                $goods_users=Db::name('goods_users')->where('id',$results)->find();
                $goods=Db::name('goods')->where('id',$goods_users['goods_id'])->find();
                $users=Db::name('users')->where('id',$this->uid)->find();

                 
                  Db::name('goods_users')->where('id',$results)->update(['jlstatus'=>2]);

             
            Db::commit();
            $responsData =  Response::success('success', []);
            return json($responsData);
        } else{
            $responsData =  Response::fail('碎片信息错误!');
            return json($responsData);
        }
    }
    }
}