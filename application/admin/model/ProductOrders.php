<?php

namespace app\admin\model;

use think\Model;


class ProductOrders extends Model
{

    

    

    // 表名
    protected $name = 'product_orders';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['1' => __('Status 1'), '2' => __('Status 2'),'3' => __('Status 3'),'4' => __('Status 4'), '5' => __('Status 5')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function goods()
    {
        return $this->belongsTo('Goods', 'goods_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function product()
    {
        return $this->belongsTo('Product', 'product_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function goodscategory()
    {
        return $this->belongsTo('GoodsCategory', 'goods_category_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function users()
    {
        return $this->belongsTo('Users', 'buy_uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function susers()
    {
        return $this->belongsTo('Users', 'sale_uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
