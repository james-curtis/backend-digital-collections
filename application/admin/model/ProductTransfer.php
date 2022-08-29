<?php

namespace app\admin\model;

use think\Model;


class ProductTransfer extends Model
{

    

    

    // 表名
    protected $name = 'product_transfer';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







    public function goods()
    {
        return $this->belongsTo('Goods', 'goods_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function users()
    {
        return $this->belongsTo('Users', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function tusers()
    {
        return $this->belongsTo('Users', 'target_uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function productorders()
    {
        return $this->belongsTo('ProductOrders', 'order_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
