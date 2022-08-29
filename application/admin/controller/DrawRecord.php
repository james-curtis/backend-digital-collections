<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use logicmodel\AccountLogic;
use logicmodel\MemberLogic;
use think\Db;
use think\Session;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class DrawRecord extends Backend
{
    
    /**
     * DrawRecord模型对象
     * @var \app\admin\model\DrawRecord
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\DrawRecord;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("statusList", $this->model->getStatusList());
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
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        $teamId = Session::get('team_id');
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }

            //如果发
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    ->with(['currency','users'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                $row->visible(['id','order_num','account','reality_account','type','status','refuse','create_time','bank_name','bank_number','bank_owner','bank_branch','ali_name','ali_number','ali_image','wx_name','wx_number','wx_image']);
                $row->visible(['currency']);
				$row->getRelation('currency')->visible(['name']);
				$row->visible(['users']);
				$row->getRelation('users')->visible(['phone']);
            }
            $account = $this->model
                ->with(['currency','users'])
                ->where($where)
                ->sum('draw_record.account');
            $reality_account = $this->model
                ->with(['currency','users'])
                ->where($where)
                ->sum('draw_record.reality_account');
            $fee_account = bcsub($account,$reality_account,2);
            $result = array("total" => $list->total(), "rows" => $list->items(),'account'=>$account,'reality_account'=>$reality_account,'fee_account'=>$fee_account);

            return json($result);
        }
        return $this->view->fetch();
    }
    /**
     * @param $ids
     * @return \app\admin\model\DrawRecord|array|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function pass($ids){
        $info = $this->model->find($ids);
        if(empty($info)) return json(['code'=>0,'msg'=>'提现信息错误']);
        if($info['status'] != 0 )return json(['code'=>0,'msg'=>'当前提现已审核']);
        $result = $this->model->where(['id'=>$ids])->update(['status'=>1]);
        if($result) return json(['code'=>1,'msg'=>'审核成功']);
        return json(['code'=>0,'msg'=>'审核失败']);
    }
    /**
     * 审核拒绝
     * @param $ids
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function refuse($ids){
        if(request()->post()){
            $info = $this->model->where(['id'=>$ids])->find();
            if(empty($info)) return json(['code'=>0,'msg'=>'申请信息错误']);
            if(!in_array($info['status'],[0,3]))      $this->error('记录状态错误');
            $refuse = input('post.refuse');
            Db::startTrans();
            $result = $this->model->where(['id'=>$ids])->update(['refuse'=>$refuse,'status'=>2]);
            if($result <= 0){
                Db::rollback();
                $this->error('操作失败');
            }
            $result = (new AccountLogic())->addAccount($info['uid'],$info['currency_id'],$info['account'],'提现拒绝','提现拒绝');
            if($result){
                Db::commit();
                $this->success('操作成功');
            }
            Db::rollback();
            $this->error('操作失败');
        }
        $this->assign('ids',$ids);
        return $this->fetch();
    }
}
