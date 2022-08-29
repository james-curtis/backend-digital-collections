<?php

namespace app\admin\model;

use think\Model;


class LuckRecord extends Model
{

    

    

    // 表名
    protected $name = 'luck_record';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'award_type_text'
    ];
    

    
    public function getAwardTypeList()
    {
        return ['1' => __('Award_type 1'), '2' => __('Award_type 2')];
    }


    public function getAwardTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['award_type']) ? $data['award_type'] : '');
        $list = $this->getAwardTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function luck()
    {
        return $this->belongsTo('Luck', 'luck_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function users()
    {
        return $this->belongsTo('Users', 'uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
