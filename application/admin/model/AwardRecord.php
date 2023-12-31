<?php

namespace app\admin\model;

use think\Model;


class AwardRecord extends Model
{

    

    

    // 表名
    protected $name = 'award_record';
    
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


    public function goods()
    {
        return $this->belongsTo('Goods', 'goods_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function award()
    {
        return $this->belongsTo('Award', 'award_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
