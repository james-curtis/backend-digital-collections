<?php
namespace logicmodel\award;
use datamodel\AwardRecord;
use datamodel\GoodsUsers;
use datamodel\Users;
use logicmodel\AccountLogic;
use think\Db;
use comservice\Response;
use app\admin\model\GoodsMangheUsers;
require_once('../xasset/index.php');
class Award
{

    protected $awardData;
    protected $awardRecordData;
    protected $accountLogic;
    protected $usersData;
    public function __construct()
    {
        $this->awardData = new \datamodel\Award();
        $this->awardRecordData = new AwardRecord();
        $this->accountLogic = new AccountLogic();
        $this->usersData = new Users();
    }
    /**
     * 奖项是否开启
     * @param $award_id
     * @return array|bool|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function awardIsOpen($award_id){
        $where ['id'] = ['=',$award_id];
        $where ['status'] = ['=',1];
        $result = $this->awardData
            ->where($where)
            ->find();
        if ($result != null) return $result;
        return false;
    }

    /**
     * 生成记录
     * @param $uid
     * @param $currency_id
     * @param $money
     * @param $from_uid
     * @param $award_id
     * @param $award_name
     * @param $remark
     * @param $field
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function record($uid,$award_id,$goods_id){
        $goodsInfo = Db::name('goods')->where(['id'=>$goods_id])->find();
        $price = $goodsInfo['price'];
        $data['uid'] = $uid;
        $data['price'] = $price;
        $data['goods_id'] = $goods_id;
        $data['award_id'] = $award_id;
        $data['create_time'] = date('Y-m-d H:i:s');

              $this->awardRecordData->saveEntityAndGetId($data);
                if ($goodsInfo['is_manghe']!=1){
                    //非盲盒的奖励
                    $goods_user_number = Db::name('goods_users')->where(['goods_id'=>$goods_id,'status'=>1])->whereNotNull('number')->order('number', 'desc')->value('number');

                    if ($goods_user_number) {
                        $goods_user_number = str_pad($goods_user_number + 1,6,'0',STR_PAD_LEFT);
                    } else {
                        $goods_user_number = '000001';
                    }

                    $user = ['goods_id'=>$goods_id,'uid'=>$uid,'price'=>$price,'jlstatus'=>1,'create_time'=>date('Y-m-d H:i:s'),'number'=>$goods_user_number];
                    (new GoodsUsers())->insertGetId($user);
                }else{
                    //盲盒的奖励
                    //盲盒id
                    $mhid=Db::name('goods_manghe_users')->max('id');
                    $mhstr="mh".str_pad(($mhid+1),5,"0",STR_PAD_LEFT);
                    $currentTime = time();
                    $goods_manghe_number          = uniqueNum();
                    $goodsManghe['user_id']          = $uid;
                    $goodsManghe['goods_id']     = $goods_id;
                    $goodsManghe['goods_number'] = $goods_manghe_number;
                    $goodsManghe['status']       = 2;
                    $goodsManghe['createtime']  = $currentTime;
                    $goodsManghe['updatetime']  = $currentTime;
                    $goodsManghe['orderNo']  = $mhstr;
                    $goodsMangheUsersData = new GoodsMangheUsers();
                    // $class_id=session_create_id();

                    // //创建nft类别
                    // $mhclasses=CreateChainClasses($users['wallet_address'],$class_id,$uid);
                    // if($mhclasses){
                    //     if(array_key_exists('error', $mhclasses)){
                    //         return Response::fail('抢购人数过多,请重新抢购');
                    //     }
                    //     $goodsManghe['class_id']  = $class_id;
                    // }

                    $goods_manghe_users_id = $goodsMangheUsersData->insertGetId($goodsManghe);
                    if (!$goods_manghe_users_id) {
                        Db::rollback();
                        return json(['code'=>0,'msg'=>'赠送失败']);
                    }
                }



    }
}