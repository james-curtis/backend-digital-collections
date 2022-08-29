<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 商品盲盒配置
 *
 * @icon fa fa-circle-o
 */
class GoodsMangheConfig extends Backend
{
    
    /**
     * GoodsMangheConfig模型对象
     * @var \app\admin\model\GoodsMangheConfig
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\GoodsMangheConfig;

        $this->view->assign("isWinList", $this->model->getIsWinList());

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
        $goods_id = input('goods_id');
        $this->assignconfig('goods_id', $goods_id);
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
                ->with(['goods'])
                ->where('goods_manghe_config.goods_id', $goods_id)
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    public function add()
    {
        if(request()->isPost()){
            $goods_id = $this->request->get('goods_id');
            $post = input('post.');
            $row = $post['row'];
            //判断入已经添加了则不允许再添加
            $combination_goods_id  = $row['combination_goods_id'];
            $res = $this->model->where(['goods_id'=>$goods_id, 'combination_goods_id'=>$combination_goods_id])->count();
            if($res)
            {
                return json(['code'=>0,'msg'=>'此藏品已经添加!']);
            }
            //检查是否已经设置了未中奖项
            $res1 = $this->model->where(['goods_id'=>$goods_id,'is_win'=>0])->count();

            if($res1 && $row['is_win']==0)
            {
                return json(['code'=>0,'msg'=>'已经设置了未中奖项，请勿再设置!']);
            }

            $row['goods_id'] = $goods_id;
            $result = $this->model->insertGetId($row);
            if($result > 0)
            {
                return json(['code'=>1,'msg'=>'添加成功']);
            }
            return json(['code'=>0,'msg'=>'添加失败']);
        }
        return $this->fetch();
    }

}
