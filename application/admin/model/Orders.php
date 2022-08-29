<?php

namespace app\admin\model;

use think\Model;


class Orders extends Model
{

    

    

    // 表名
    protected $name = 'orders';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'pay_type_text'
    ];
    

    
    public function getStatusList()
    {
        return ['1' => __('Status 1'), '2' => __('Status 2'), '3' => __('Status 3')];
    }

    public function getPayTypeList()
    {
        return ['0' => __('Pay_type 0'), '1' => __('Pay_type 1'), '2' => __('Pay_type 2'), '3' => __('Pay_type 3'),'4' => __('Pay_type 4'),'5' => __('Pay_type 5'),'6' => __('Pay_type 6'),'7' => __('Pay_type 7')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPayTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pay_type']) ? $data['pay_type'] : '');
        $list = $this->getPayTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function goods()
    {
        return $this->belongsTo('Goods', 'goods_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function goodsuser()
    {
        return $this->belongsTo('GoodsUsers', 'goods_users_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function users()
    {
        return $this->belongsTo('Users', 'sale_uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function busers()
    {
        return $this->belongsTo('Users', 'buy_uid', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
