<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 用户盲盒管理
 *
 * @icon fa fa-circle-o
 */
class GoodsMangheUsers extends Backend
{
    
    /**
     * GoodsMangheUsers模型对象
     * @var \app\admin\model\GoodsMangheUsers
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\GoodsMangheUsers;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("stateList", $this->model->getStateList());
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
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    ->with(['goods','users'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

           

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
    
    //上链
    public function slupdate($ids = "")
    {   
        foreach ($ids as $key => $value) {
           $goodsmangheusers=$this->model->where('id',$value)->find();
           $goods=Db::name('goods')->where('id',$goodsmangheusers['goods_id'])->find();
           $users=Db::name('users')->where('id',$goodsmangheusers['users_id'])->find();
           $url='http://'.$_SERVER['HTTP_HOST'].$goods['image'];
           if($goodsusers['state']==0){
                $nfsfx=CreateChainNfts($goodsusers['users_id'],$goodsmangheusers['class_id'],$url);
           if(array_key_exists('error', $nfsfx)){
                    return json(['code'=>0,'msg'=>$users['member'].'会员账号上链失败：'.$nfsfx['error']['code']]);
                }
              $result=$this->model->where('id',$value)->update(['state'=>1,'operation_id'=>$nfsfx['data']['operation_id']]);
           }else{
              return json(['code'=>0,'msg'=>'请选择未上链的数据！']); 
           }
          
        }
        if($result) return json(['code'=>1,'msg'=>'上链成功']);
        return json(['code'=>0,'msg'=>'上链失败']);
    }

}
