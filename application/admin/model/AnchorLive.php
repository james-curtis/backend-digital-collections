<?php

namespace app\admin\model;

use think\Model;


class AnchorLive extends Model
{

    

    

    // 表名
    protected $name = 'anchor_live';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'census_time_text',
        'is_del_text'
    ];
    

    
    public function getIsDelList()
    {
        return ['0' => __('Is_del 0'), '1' => __('Is_del 1')];
    }


    public function getCensusTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['census_time']) ? $data['census_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getIsDelTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_del']) ? $data['is_del'] : '');
        $list = $this->getIsDelList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setCensusTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function anchor()
    {
        return $this->belongsTo('Anchor', 'anchor_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
