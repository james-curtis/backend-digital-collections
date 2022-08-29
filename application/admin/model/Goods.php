<?php

namespace app\admin\model;

use think\Model;


class Goods extends Model
{

    

    

    // 表名
    protected $name = 'goods';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'is_show_text',
    ];
    

    
    public function getTypeList()
    {
        return ['1' => __('Type 1')];
    }

    public function getIsShowList()
    {
        return ['0' => __('Is_show 0'), '1' => __('Is_show 1')];
    }

    public function getIsMangheList()
    {
        return ['0' => __('藏品'), '1' => __('盲盒'), '2' => __('合成')];
    }

    public function getIsCanBuyList()
    {
        return ['0' => __('不参与'), '1' => __('参与')];
    }

    public function getIsChipList()
    {
        return ['0' => __('否'), '1' => __('是')];
    }
    
     public function getHcstatusList()
    {
        return ['0' => __('否'), '1' => __('是')];
    }
    
    public function getHcstatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['hcstatus']) ? $data['hcstatus'] : '');
        $list = $this->getHcstatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsShowTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_show']) ? $data['is_show'] : '');
        $list = $this->getIsShowList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getIsMnagheTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_manghe']) ? $data['is_manghe'] : '');
        $list = $this->getIsMangheList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getFnstatusList()
    {
        return ['0' => __('否'), '1' => __('是')];
    }



    public function getFnstatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['fnstatus']) ? $data['fnstatus'] : '');
        $list = $this->getFnstatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function coupon()
    {
        return $this->belongsTo('Coupon', 'coupon_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
    public function goodscategory()
    {
        return $this->belongsTo('GoodsCategory', 'goods_category_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

}
