<?php

namespace app\admin\model;

use think\Model;


class GoodsMangheConfig extends Model
{

    

    

    // 表名
    protected $name = 'goods_manghe_config';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    

    public function getIsWinList()
    {
        return ['0' =>'不是中奖项', '1' => '是中奖项'];
    }





    public function goods()
    {
        return $this->belongsTo('Goods', 'combination_goods_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
