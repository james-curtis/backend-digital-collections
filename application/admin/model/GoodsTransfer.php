<?php

namespace app\admin\model;

use think\Model;


class GoodsTransfer extends Model
{

    

    

    // 表名
    protected $name = 'goods_transfer';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







    public function users()
    {
        return $this->belongsTo('Users', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function tusers()
    {
        return $this->belongsTo('Users', 'target_uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function goods()
    {
        return $this->belongsTo('Goods', 'goods_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
