<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class TransferRecord extends Backend
{
    
    /**
     * TransferRecord模型对象
     * @var \app\admin\model\TransferRecord
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\TransferRecord;

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
            $filter = $this->request->request('filter');
            $filterArr = (array)json_decode($filter, true);
            $where = [];
            if(!empty($filterArr['phone'])) $where['u.phone'] = ['like','%'.$filterArr['phone'].'%'];
            if(!empty($filterArr['target_phone'])) $where['tu.phone'] = ['like','%'.$filterArr['target_phone'].'%'];
            if(!empty($filterArr['currency_id'])) $where['tr.currency_id'] = $filterArr['currency_id'];
            if(!empty($filterArr['create_time'])){
                $time = splitTime($filterArr['create_time']);
                $where['tr.create_time'] = ['between',[$time[0],$time[1]]];
            }
            $offset = input('offset');
            $limit = input('limit');
            $total =  $this->model->alias('tr')
                ->join('users u','u.id = tr.uid')
                ->join('currency c','c.id = tr.currency_id')
                ->join('users tu','tu.id = tr.target_uid')
                ->where($where)
                ->count();

            $field = ['tr.*','u.phone','tu.phone target_phone','c.name currency_name'];
            $data = $this->model->alias('tr')
                ->join('users u','u.id = tr.uid')
                ->join('currency c','c.id = tr.currency_id')
                ->join('users tu','tu.id = tr.target_uid')
                ->where($where)
                ->field($field)
                ->order(['tr.id desc'])
                ->limit($offset,$limit)
                ->select();
            return json(['rows'=>$data,'total'=>$total]);
        }
        return $this->view->fetch();
    }

}
