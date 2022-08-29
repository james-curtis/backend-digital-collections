<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 用户碎片管理
 *
 * @icon fa fa-circle-o
 */
class ChipUsers extends Backend
{
    
    /**
     * ChipUsers模型对象
     * @var \app\admin\model\ChipUsers
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\ChipUsers;

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

            foreach ($list as $row) {
                
                $row->getRelation('goods')->visible(['id','name','image']);
				$row->getRelation('users')->visible(['id','name','nick_name']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 如果已存在碎片则不用新增 直接修改数量即可
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     */
    public function add()
    {
        if(request()->isPost()){
            $post = input('post.');
            $row = $post['row'];
            //判断入已经添加了则不允许再添加
            $goods_id  = $row['goods_id'];
            $user_id  = $row['user_id'];
            $res = $this->model->where(['goods_id'=>$goods_id, 'user_id'=>$user_id])->count();
            if($res)
            {
                //更新随便即可
                $total = $row['total'];
                $result = $this->model->where(['goods_id'=>$goods_id, 'user_id'=>$user_id])->setInc('total', $total);
            }
            else{
                $result = $this->model->isUpdate(false)->save($row);
            }
            if($result)
            {
                return json(['code'=>1,'msg'=>'添加成功']);
            }
            return json(['code'=>0,'msg'=>'添加失败']);
        }
        return $this->fetch();
    }


}
