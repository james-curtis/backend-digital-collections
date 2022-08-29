<?php

namespace app\admin\model;

use think\Model;


class AwardRecommend extends Model
{

    

    

    // 表名
    protected $name = 'award_recommend';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







    public function rank()
    {
        return $this->belongsTo('Rank', 'rank_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
