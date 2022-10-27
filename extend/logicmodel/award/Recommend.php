<?php


namespace logicmodel\award;


use comservice\Response;
use datamodel\AwardRecommend;
use think\Db;
use comservice\GetRedis;
use datamodel\Goods;

class Recommend extends Award
{
    private $awardRecommendData;
    private $award_id;

    public function __construct()
    {
        parent::__construct();
        $this->awardRecommendData = new AwardRecommend();
    }

    //推荐奖励
    public function award($uid)
    {
        //看已经邀请了几个了
        $where = ['pid' => $uid, 'is_del' => 0, 'is_auth' => 1];
        $inviteTotal = $this->usersData->where($where)->count();
        //再看设置的邀请人数 哪个符合
        $award_now_arr = Db::name("award")
            ->where(['type' => 1, 'status' => 1, "total_number" => ['<=', $inviteTotal]])
            ->order("total_number desc")
            ->select();
        // 需求：拉新1人的奖励是可以重复获取，其他奖励只有一次机会
        foreach ($award_now_arr as $awardInfo) {
            //匹配奖励的id
            $award_id = $awardInfo['id'];

            // 排除拉新1人的情况。这里需要允许重复获取
            if (!$awardInfo['is_repeat']) {
                // 走了这里面说明是不允许重复领取
                //奖项未开起
                $info = $this->awardRecordData->where(['uid' => $uid, 'award_id' => $award_id])->find();
//            if ($info) return Response::fail('已领取奖励');
                // 已领取奖励
                if ($info) continue;
            }

            if (!$awardInfo['goods_id']) {
                return Response::fail('无产品');
            }
            $total_number = $awardInfo['total_number'];
            if ($inviteTotal >= $total_number) {
                $cz = Db::name('goods')->where(['id' => $awardInfo['goods_id']])->find();
                if ($cz) {
//                    $redis = GetRedis::getRedis();

//                    if ($cz['is_manghe'] == 1) {
//                        $goods_kc_count = $redis->rpop('goods_mh_' . $awardInfo['goods_id']);
//                    } else {
//                        $goods_kc_count = $redis->rpop('goods_kc_' . $awardInfo['goods_id']);
//                    }
//
//                    if (!$goods_kc_count) {
//                        Db::rollback();
//                        return Response::fail('没有库存了');
//                    }

                    $goodsData = new Goods();
                    //库存剩余递减 销量递增
                    $goodsData->where(['id' => $awardInfo['goods_id']])->setDec('surplus', 1);
                    $goodsData->where(['id' => $awardInfo['goods_id']])->setInc('sales', 1);

                    $this->record($uid, $award_id, $awardInfo['goods_id']);
                }
            }
        }
        return true;

    }

    //注册空投
    public function awardkt($uid)
    {
        $award_id = 1;
        $awardInfo = $this->awardIsOpen($award_id);

        if ($awardInfo === false) return false;//奖项未开起
        $info = $this->awardRecordData->where(['uid' => $uid, 'award_id' => 1])->find();

        if ($info) return Response::fail('已领取奖励');
        if (!$awardInfo['goods_id']) {
            return Response::fail('无产品');
        }
        $cz = Db::name('goods')->where(['id' => $awardInfo['goods_id']])->find();
        if ($cz) {

            $redis = GetRedis::getRedis();
            $goodsData = new Goods();

            if ($cz['is_manghe'] == 1) {
                $goods_kc_count = $redis->rpop('goods_mh_' . $awardInfo['goods_id']);
            } else {
                $goods_kc_count = $redis->rpop('goods_kc_' . $awardInfo['goods_id']);
            }
//
//            if (!$goods_kc_count) {
//                Db::rollback();
//                return Response::fail('没有库存了');
//            }

            //库存剩余递减 销量递增
            $goodsData->where(['id' => $awardInfo['goods_id']])->setDec('surplus', 1);
            $goodsData->where(['id' => $awardInfo['goods_id']])->setInc('sales', 1);

            $record = $this->record($uid, $award_id, $awardInfo['goods_id']);
            return $record;
        }

        return false;
    }
}