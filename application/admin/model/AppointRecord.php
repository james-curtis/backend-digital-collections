<?php

namespace app\admin\model;

use think\Model;


class AppointRecord extends Model
{

    

    

    // 表名
    protected $name = 'appoint_record';
    
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


    public function appoint()
    {
        return $this->belongsTo('Appoint', 'appoint_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function goodscategory()
    {
        return $this->belongsTo('GoodsCategory', 'goods_category_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
