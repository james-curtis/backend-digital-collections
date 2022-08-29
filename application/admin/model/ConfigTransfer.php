<?php

namespace app\admin\model;

use think\Model;


class ConfigTransfer extends Model
{

    

    

    // 表名
    protected $name = 'config_transfer';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'is_show_text',
        'is_del_text'
    ];
    

    
    public function getIsShowList()
    {
        return ['0' => __('Is_show 0'), '1' => __('Is_show 1')];
    }

    public function getIsDelList()
    {
        return ['0' => __('Is_del 0'), '1' => __('Is_del 1')];
    }


    public function getIsShowTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_show']) ? $data['is_show'] : '');
        $list = $this->getIsShowList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsDelTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_del']) ? $data['is_del'] : '');
        $list = $this->getIsDelList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function currency()
    {
        return $this->belongsTo('Currency', 'currency_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
