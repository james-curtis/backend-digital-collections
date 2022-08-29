<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class GoodsConfig extends Backend
{
    
    /**
     * GoodsConfig模型对象
     * @var \app\admin\model\GoodsConfig
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\GoodsConfig;
        $this->view->assign("isShowList", $this->model->getIsShowList());
        $this->view->assign("isDelList", $this->model->getIsDelList());
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
        $goods_id = input('ids');
        $this->assignconfig('goods_id', $goods_id);
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {

            $offset = input('offset');
            $limit = input('limit');
            $where['m.goods_id'] = input('goods_id');
            $where['m.is_del'] = 0;
            $total  =  $this->model->alias('m')
                ->join('goods g','m.combination_goods_id = g.id')
                ->where($where)
                ->count();
            $field = ['m.*','g.name goods_name'];
            $data = $this->model->alias('m')
                ->field($field)
                ->join('goods g','m.combination_goods_id = g.id')
                ->where($where)->limit($offset,$limit)->select();
            return json(['rows'=>$data,'total'=>$total]);
        }
        return $this->view->fetch();
    }
    public function del($ids = "")
    {
        $result = $this->model->where(['id'=>['in',$ids]])->update(['is_del'=>1]);
        if($result) return json(['code'=>1,'msg'=>'删除成功']);
        return json(['code'=>0,'msg'=>'删除失败']);
    }
    public function add()
    {
        if(request()->isPost()){
            $goods_id = input('goods_id');
            $post = input('post.');
            $row = $post['row'];
            $row['goods_id'] = $goods_id;
            $result = $this->model->insertGetId($row);
            if($result > 0) return json(['code'=>1,'msg'=>'添加成功']);
            return json(['code'=>0,'msg'=>'添加失败']);
        }
        return $this->fetch();
    }

}
