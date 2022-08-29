<?php

namespace app\admin\model;

use think\Model;


class CommissionRecord extends Model
{

    

    

    // 表名
    protected $name = 'commission_record';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'is_freeze_text'
    ];
    

    
    public function getIsFreezeList()
    {
        return ['0' => __('Is_freeze 0'), '1' => __('Is_freeze 1')];
    }


    public function getIsFreezeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_freeze']) ? $data['is_freeze'] : '');
        $list = $this->getIsFreezeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function users()
    {
        return $this->belongsTo('Users', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function anchor()
    {
        return $this->belongsTo('Anchor', 'anchor_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
