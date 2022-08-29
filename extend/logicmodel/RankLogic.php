<?php


namespace logicmodel;


use datamodel\Rank;
use datamodel\Users;
use think\Db;

class RankLogic
{
    private $rankData;
    private $usersData;
    public function __construct()
    {
        $this->rankData = new Rank();
        $this->usersData = new Users();
    }

    /**
     * 会员升级
     * @param $uid
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function upRank($uid){
       $userInfo =  $this->usersData->where(['id'=>$uid])->find();
       if($userInfo['rank_id'] == 1)  {
           $this->usersData->where(['id'=>$uid])->update(['rank_id'=>2]);
         $team =   (new MemberLogic())->listParentArr($uid);
        $this->usersData->where(['id'=>['in',$team]])->setInc('group_valid_person_count',1);
       }
       $teamArr =  (new MemberLogic())->listParent($uid,['id','pid','upid','rank_id','buy_group_achievement_money','group_valid_person_count'],2,0);
        $rank =  $this->rankData->column('id,remittance_money,buy_number,total_direct','id');
        $up = [];
       foreach ($teamArr as $v){
           $rank_id = $v['rank_id'];
           if($rank_id <= 1) continue;
           if($rank_id >= 7) continue;
           $up_rank = $rank[$rank_id+1];//升级条件
           if($up_rank['remittance_money'] > 0){
               if($v['buy_group_achievement_money'] < $up_rank['remittance_money']) continue;
           }
           if($up_rank['buy_number'] > 0){
                if($v['group_valid_person_count'] < $up_rank['buy_number']) continue;
           }
           if($up_rank['total_direct'] > 0){
             $total_direct =   $this->usersData->where(['pid'=>$v['id'],'rank_id'=>['>=',$rank_id],'is_del'=>0])->count();
            if($total_direct < $up_rank['total_direct']) continue;
           }
           $up[] = ['id'=>$v['id'],'rank_id'=>$rank_id+1];
       }
       $this->usersData->saveAll($up);
    }
}