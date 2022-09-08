<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use comservice\Response;
use logicmodel\AccountLogic;
use logicmodel\award\Recommend;
use logicmodel\MemberLogic;
use logicmodel\WalletLogic;
use datamodel\GoodsUsers;
use think\Db;
// require_once('../xasset/index.php');
/**
 *
 *
 * @icon fa fa-users
 */
class Statistics extends Backend
{

    /**
     * Users模型对象
     * @var \app\admin\model\Users
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Users;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("isDelList", $this->model->getIsDelList());
        $this->view->assign("isAuthList", $this->model->getIsAuthList());
        $this->view->assign("isBankList", $this->model->getIsBankList());
        $this->view->assign("isAliList", $this->model->getIsAliList());
        $this->view->assign("isWxList", $this->model->getIsWxList());
        $this->view->assign("yxgList", $this->model->getYxgList());
        $this->view->assign("typeList",['0' => __('Type 0'), '1' => __('Type 1')]);


    }

    public function import()
    {
        parent::import();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {
        $pid = input('pid',1);

        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        $offset=input('offset');
        $limit=input('limit');
          // 获取搜索框的值
         $filter=input('filter');
         $snapshot=$this->request->post('params');
        
         //exit();
        if ($this->request->isAjax()) {

            $type = input('type',0);

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }

           list($where, $sort, $order, $offset, $limit) = $this->buildparams();

             //判断是否有值
            if(strlen($filter)>2){
                $filter=json_decode($filter,true);
                 if(array_key_exists('goods_id',$filter)){
                
                    $wheres['phone']=$filter['goods_id'];

                    $goodsuserst=Db::name('goods_users')->where('goods_id',$filter['goods_id'])->where('uid','not in',1)->count();
                    $goodsusers=Db::name('goods_users')->where('goods_id',$filter['goods_id'])->where('uid','not in',1)->select();


                    if(empty($goodsuserst)){
                        //有藏品为空查询
                        $list = $this->model
                            ->with(['role'])
                            ->where(['is_del'=>0])
                            ->where($where)
                            ->order($sort, $order)
                            ->paginate($limit); ;
                    }else{

                        //有藏品查询
                        foreach ($goodsusers as $value) {
                            $arr[]=$value['uid'];
                        }
                        unset($filter['goods_id']);
                        $this->request->get(['filter'=>json_encode($filter)]);
                        list($where, $sort, $order, $offset, $limit) = $this->buildparams();
                        $list = $this->model
                            ->with(['role'])
                            ->where(['is_del'=>0])
                            ->where($where)
                            ->where('users.id','in',$arr)
                            ->order($sort, $order)
                            ->paginate($limit);
                    }

                }else{
                     if(array_key_exists('kz',$filter)){
                         //快照查询
                    $config=Db::name('config')->where('group','timeconfiguration')->select();

                    $goods_users=Db::name('goods_users')->where('create_time',$config[0]['value'])->where('status',1)->select();
                     print_r($goods_users);
                     $list = $this->model
                    ->with(['role'])
                    ->where(['is_del'=>0])
                    ->where('users.id',1)
                    ->order($sort, $order)
                    ->paginate($limit);
                     }else{
                        //没有藏品查询
                     $list = $this->model
                    ->with(['role'])
                    ->where(['is_del'=>0])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);
                     }
                }

            }else{



                if($type){
                    $users_ = $this->model->where(['is_del'=>0,'pid' => $pid])->select();

                    $ids_arr = array();
                    foreach ($users_ as $key => $val){
                        $ids_arr[] = $val['id'];
                    }

                    $where_son['pid'] = ['in',$ids_arr];
//                    dump($ids_arr);die;
                }else{
                    $where_son['is_del'] = 0;
                    $where_son['pid'] = $pid;
                }

                //初始化查询
                $list = $this->model
                ->with(['role'])
                ->where($where_son)
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit); 
            }

           
            

            foreach ($list as $row) {
                $goodsusers=Db::name('goods_users')->where('uid',$row['id'])->count();
                $this->model->where('id',$row['id'])->update(['cpzs'=>$goodsusers]);
                $row->visible(['id','member','nick_name','head_image','phone','status','uuid','total_direct','group_person_count','achievement_money','group_achievement_money','parent_member','create_time','name','card','is_bank','is_wx','is_ali','wholesale_account','award_recommend','wallet_address','cpzs']);
                $row->visible(['role']);
                $row->getRelation('role')->visible(['name']);
                 $row['cpzs']=$goodsusers;
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }

        $this->view->assign("pid", $pid);
        return $this->view->fetch();
    }
    
    
    public function goods(){
    $json = cache('goods_id');
    if ($json===false){
        $list = Db::name('goods')->select();
        $json = json($list);
        cache('goods_id',$json);
    }
    return $json;
}

    public function plstatus($ids=""){
         foreach ($ids as $k => $v) {
                $this->model->where('id',$v)->update(['yxg'=>1]);
        }
        $this->success();
    }

    public function add()
    {

        if(request()->isPost()){
            $data = input('post.');
            $data = $data['row'];
            $parent_member = $data['parent_member'];
            $parentInfo = $this->model->where(['is_del'=>0,'phone'=>$parent_member])->find();
            if(empty($parentInfo))  $this->error('推荐人手机号错误');
            $phone = $data['phone'];
            if(empty($phone))$this->error('手机号不能为空');
            $info = $this->model->where(['phone'=>$phone,'is_del'=>0])->find();
            if($info) $this->error('手机号已注册');
             //调用地址方法
            $account=CreateChainAccount([],$data['phone']);
                if(array_key_exists('error', $account)){
                   return Response::fail('钱包地址生成失败');
                }else{
                   $userData['wallet_address']=$account['data']['account']; 
                }
            $member = $phone;
            $password = $data['password'];
            if(empty($password) ) $this->error('请输入登录密码');;
            $salt = rand(1111,9999);
            $pid = $parentInfo['id'];
            $userData['nick_name'] = 'zz_'.rand(111111,999999);
            $userData['member'] = $member;
            $userData['salt'] = $salt;
            $userData['password'] = md5(md5($password).$salt);
            $userData['head_image'] = defaultImage();
            $userData['uuid'] = uuid();
            $userData['pid'] = $pid;
            $userData['upid'] = $pid;
            $userData['phone'] = $phone;
            $userData['parent_member'] = $parentInfo['phone'];
            $userData['create_time'] = date('Y-m-d H:i:s');
            $userData['role_id'] = $data['role_id'];
            Db::startTrans();
            $user_id = $this->model->insertGetId($userData);
            if($user_id > 0) {
                $result =  $this->updateGroup($pid);
                if(!$result){
                    Db::rollback();
                    $this->error('注册失败');
                }
                Db::commit();
                $this->success('注册成功');

            }
            Db::rollback();
            $this->error('注册失败');
        }
        return $this->fetch();
    }
    
    //余额充值
    public function plrecharge($ids = "",$account=""){
  

            //$account = $data['account'];
            if($account <= 0) return json(['code'=>0,'msg'=>'操作金额需大于0']);
            $type = 1;
            $currency_id = 1;
            Db::startTrans();
            if($type == 1){
                //$currency_id = 1;
                //充值
                $bil_type = '后台充值';
                $remark = '后台充值';
                     foreach ($ids as $value) {
                         $result =  (new AccountLogic())->addAccount($value,$currency_id,$account,$bil_type,$remark);
                            if($result == false){
                    Db::rollback();
                    return json(['code'=>0,'msg'=>'充值失败']);
                }
                     }
                 }
                
            
            foreach ($ids as $values) {
               $fundsData = ['uid'=>$values,'currency_id'=>$currency_id,'account'=>$account,'type'=>$type,'create_time'=>date('Y-m-d H:i:s')];
            $result = Db::name('funds')->insertGetId($fundsData);
            }
              if($result > 0) {
                Db::commit();
                return json(['code'=>1,'msg'=>'操作成功']);
            }
            return json(['code'=>0,'msg'=>'操作失败']);
        return $this->fetch();
    }
    
    public function plgive($ids=""){
     //$data = input('post.');
     $goodsUsersData = new GoodsUsers();
        if(request()->isPost()){
            $data = input('post.');
            $idlist = explode(",", $data['id']);
            $goods_id = $data['goods_id'];
           $goods =  (new \app\admin\model\Goods())->where(['id'=>$goods_id,'is_del'=>0])->find();
           if(empty($goods)) return json(['code'=>0,'msg'=>'藏品信息错误']);
           if($goods['surplus']<0) return json(['code'=>0,'msg'=>'藏品已用完']);
            $price = $data['price'];
           if(empty($price)) $price = $goods['price'];
           //生成

           if($goods['is_manghe']==0){
               foreach ($idlist as $value) {
               
                 $users=Db::name('users')->where('id',$value)->find();

        
                 $goods_user_number = $goodsUsersData->where(['goods_id'=>$goods_id])->whereNotNull('number')->order('id', 'desc')->value('number');

                if ($goods_user_number) {
                    $goods_user_number = str_pad($goods_user_number + 1,6,'0',STR_PAD_LEFT);
                } else {
                    $goods_user_number = '000001';
                }
               $user = ['uid'=>$value,'goods_id'=>$goods_id,'price'=>$price,'number'=>$goods_user_number,'create_time'=>date('Y-m-d H:i:s'),'is_send'=>1];
            Db::startTrans();
            $results = (new \app\admin\model\GoodsUsers())->insertGetId($user);
            if(!$results){
                Db::rollback();
                return json(['code'=>0,'msg'=>'赠送失败']);
            }
             $send = ['uid'=>$value,'goods_id'=>$goods_id,'price'=>$price,'create_time'=>date('Y-m-d H:i:s')];
           
            Db::name('goods_users')->where('id',$results)->update(['jlstatus'=>1]);
                $result = (new \app\admin\model\GoodsSend())->insertGetId($send);
                if(!$result){
               Db::rollback();
            return json(['code'=>0,'msg'=>'赠送失败']);
           }
                 Db::commit();
           
           }
            return json(['code'=>1,'msg'=>'赠送成功']);
           }
           
           
            //生成
           if($goods['is_manghe']==1){
               foreach ($idlist as $value) {
                $mhid=Db::name('goods_manghe_users')->max('id');
                $mhstr="mh".str_pad(($mhid+1),5,"0",STR_PAD_LEFT);
                $users=Db::name('users')->where('id',$value)->find();
                  //调用地址方法
                 //创建nft类别

               $user = ['user_id'=>$value,'goods_id'=>$goods_id,'createtime'=>time(),'updatetime'=>time(),'status'=>2,'state'=>1,'class_id'=>$class_id];
            Db::startTrans();
            $results = (new \app\admin\model\GoodsMangheUsers())->insertGetId($user);
            if(!$results){
                Db::rollback();
                return json(['code'=>0,'msg'=>'赠送失败']);
            }
             $send = ['uid'=>$value,'goods_id'=>$goods_id,'price'=>$price,'status'=>1,'create_time'=>date('Y-m-d H:i:s')];
              $result = (new \app\admin\model\GoodsSend())->insertGetId($send);
                if(!$result){
               Db::rollback();
            return json(['code'=>0,'msg'=>'赠送失败']);
           }
                 Db::commit();
           
           }
            return json(['code'=>1,'msg'=>'赠送成功']);
           }
    
        }
        return $this->fetch();
    }
    
    /**
     * 更新团队信息
     * @param $pid
     * @return bool
     * @throws \think\Exception
     */
    private function updateGroup($pid)
    {

        $field = ['id', 'pid', 'upid'];
        $userData = (new MemberLogic())->listParent($pid, $field, 1, 0);
        $groupArr = array_column($userData, 'id');
        $where['id']  = ['in', $groupArr];
        $result = $this->model->where($where)->setInc('group_person_count',1); //修改团队成员
        $res = $this->model->where(['id' => $pid])->setInc('total_direct', 1);//修改直推人数
        if ($result > 0 && $res > 0) return true;
        return false;
    }

    public function del($ids = "")
    {
        $result = $this->model->where(['id'=>['in',$ids]])->update(['is_del'=>1]);
        if($result) return json(['code'=>1,'msg'=>'删除成功']);
        return json(['code'=>0,'msg'=>'删除失败']);
    }
    public function edit($ids = null)
    {
        if(request()->isPost()){
            $data = input('post.');
            $data = $data['row'];
            if(!empty($data['password'])){
                $salt = rand(1111,9999);
                $password = md5(md5($data['password']).$salt);
                $data['password'] = $password;
                $data['salt'] = $salt;
            }else{
                unset($data['password']);
            }
            if(!empty($data['pay_password'])){
                $salt = rand(1111,9999);
                $password = md5(md5($data['pay_password']).$salt);
                $data['pay_password'] = $password;
                $data['pay_salt'] = $salt;
            }else{
                unset($data['pay_password']);
            }
            $result = $this->model->where(['id'=>$ids])->update($data);
            if($result)  $this->success('修改成功');
            $this->success('修改失败');
        }
        $row = $this->model->find($ids)->toArray();
        $this->assign('row',$row);
        return  $this->fetch();
    }
    /**
     * 资金操作
     * @param $ids
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function funds($ids){
        if(request()->post()){
            $data = input('post.');
            if(empty($data['currency_id'])) return json(['code'=>0,'msg'=>'请选择要操作币种']);
            if(empty($data['type'])) return json(['code'=>0,'msg'=>'请选择操作类型']);
            if(empty($data['account'])) return json(['code'=>0,'msg'=>'请输入金额']);
            $account = $data['account'];
            if($account <= 0) return json(['code'=>0,'msg'=>'操作金额需大于0']);
            $type = $data['type'];
            $currency_id = $data['currency_id'];
            Db::startTrans();
            if($type == 1){
                $currency_id = $data['currency_id'];
                //充值
                $bil_type = '后台充值';
                $remark = '后台充值';

                $result =  (new AccountLogic())->addAccount($ids,$currency_id,$account,$bil_type,$remark);
                if($result == false){
                    Db::rollback();
                    return json(['code'=>0,'msg'=>'充值失败']);
                }
            }elseif($type == 2){
                //扣费
                //充值
                $bil_type = '后台扣费';
                $remark = '后台扣费';
                $result =  (new AccountLogic())->subAccount($ids,$currency_id,$account,$bil_type,$remark);
                if($result == false){
                    Db::rollback();
                    return json(['code'=>0,'msg'=>'账户余额不足']);
                }

            }else{
                return json(['code'=>0,'msg'=>'操作类型错误']);
            }
            $fundsData = ['uid'=>$ids,'currency_id'=>$currency_id,'account'=>$account,'type'=>$type,'create_time'=>date('Y-m-d H:i:s')];
            $result = Db::name('funds')->insertGetId($fundsData);
            if($result > 0) {
                Db::commit();
                return json(['code'=>1,'msg'=>'操作成功']);
            }
            return json(['code'=>0,'msg'=>'操作失败']);
        }
        $field = ['ua.*','c.name currency_name'];
        $where['uid'] = $ids;
        $userAccount = Db::name('users_account')->alias('ua')
            ->join('currency c','c.id = ua.currency_id')
            ->field($field)
            ->where($where)
            ->select();
        $currencyData = Db::name('currency')->where(['is_del'=>0])->select();
        $userInfo = $this->model->find($ids);
        $this->assign('userInfo',$userInfo);
        $this->assign('currencyData',$currencyData);
        $this->assign('userAccount',$userAccount);
        $this->assign('ids',$ids);
        return $this->fetch();
    }

    public function team($phone=''){
        if(request()->isAjax()){
            if(empty($phone)){
                $data = $this->model->alias('u')
                    ->join('rank r','r.id = u.rank_id')
                    ->join('role ro','ro.id = u.role_id')
                    ->where(['u.pid'=>0])
                    ->field(['u.*','r.name rank_name'])
                    ->select();
                return json($data);
            }
            $field = ['pu.*','r.name rank_name','r.name role_name'];
            $data =  $this->model->alias('u')
                ->join('users pu','u.id = pu.pid')
                ->join('role ro','ro.id = u.role_id')
                ->where(['u.phone'=>$phone,'pu.is_del'=>0])
                ->join('rank r','r.id = pu.rank_id')
                ->field($field)
                ->select();
            return $data;
        }
        return $this->fetch();
    }
    /**
     * 搜索
     * @param string $phone
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function search($phone=''){

        if(empty($phone)){

            $where ['u.pid'] = 0;
        }else{
            $where ['u.phone'] = $phone;
            $where['is_del'] = 0;
        }

        $data = $this->model
            ->alias('u')
            ->join('rank r','r.id = u.rank_id')
            ->join('role ro','ro.id = u.role_id')
            ->where($where)
            ->field(['u.*','r.name rank_name','ro.name role_name'])
            ->select();
        return json($data);
    }
    public function send($ids =''){
        if(request()->isPost()){
            $data = input('post.');
            $goodsUsersData=new GoodsUsers();
            $goods_id = $data['goods_id'];
           $goods =  (new \app\admin\model\Goods())->where(['id'=>$goods_id,'is_del'=>0])->find();
           if(empty($goods)) return json(['code'=>0,'msg'=>'藏品信息错误']);
            $price = $data['price'];
           if(empty($price)) $price = $goods['price'];
           //生成
              $goods_user_number = $goodsUsersData->where(['goods_id'=>$goods_id])->whereNotNull('number')->order('id', 'desc')->value('number');

                if ($goods_user_number) {
                    $goods_user_number = str_pad($goods_user_number + 1,6,'0',STR_PAD_LEFT);
                } else {
                    $goods_user_number = '000001';
                }
            $user = ['uid'=>$ids,'goods_id'=>$goods_id,'price'=>$price,'number'=>$goods_user_number,'create_time'=>date('Y-m-d H:i:s'),'is_send'=>1];
            Db::startTrans();
            $results = (new \app\admin\model\GoodsUsers())->insertGetId($user);
            if(!$results){
                Db::rollback();
                return json(['code'=>0,'msg'=>'赠送失败']);
            }
            $send = ['uid'=>$ids,'goods_id'=>$goods_id,'price'=>$price,'create_time'=>date('Y-m-d H:i:s')];
             $users=Db::name('users')->where('id',$ids)->find();

            Db::name('goods_users')->where('id',$results)->update(['jlstatus'=>1]);
                $result = (new \app\admin\model\GoodsSend())->insertGetId($send);
            if($result){
                Db::commit();
                return json(['code'=>1,'msg'=>'赠送成功']);
            }
            Db::rollback();
            return json(['code'=>0,'msg'=>'赠送失败']);
             

              

             
               
               
        }
        $this->assign('ids',$ids);
        return $this->fetch();
    }
    
    function GetRandStr($len) {
    $chars = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k","l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v","w", "x", "y", "z","0", "1", "2","3", "4", "5", "6", "7", "8", "9");
    $charsLen = count($chars) - 1;
    shuffle($chars);
    $output = "";
    for ($i=0; $i<$len; $i++){
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}



}
