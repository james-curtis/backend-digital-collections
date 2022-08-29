<?php

namespace app\admin\model;

use think\Model;


class Users extends Model
{

    

    

    // 表名
    protected $name = 'users';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'is_del_text',
        'is_auth_text',
        'is_bank_text',
        'is_ali_text',
        'is_wx_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }
    
    public function getNftstatusList()
    {
        return ['0' => __('Nftstatus 0'), '1' => __('Nftstatus 1')];
    }

    public function getIsDelList()
    {
        return ['0' => __('Is_del 0'), '1' => __('Is_del 1')];
    }

    public function getIsAuthList()
    {
        return ['0' => __('Is_auth 0'), '1' => __('Is_auth 1')];
    }

    public function getIsBankList()
    {
        return ['0' => __('Is_bank 0'), '1' => __('Is_bank 1')];
    }

    public function getIsAliList()
    {
        return ['0' => __('Is_ali 0'), '1' => __('Is_ali 1')];
    }

    public function getIsWxList()
    {
        return ['0' => __('Is_wx 0'), '1' => __('Is_wx 1')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

 public function getNftstatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['nftstatus']) ? $data['nftstatus'] : '');
        $list = $this->getNftstatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getIsDelTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_del']) ? $data['is_del'] : '');
        $list = $this->getIsDelList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsAuthTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_auth']) ? $data['is_auth'] : '');
        $list = $this->getIsAuthList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsBankTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_bank']) ? $data['is_bank'] : '');
        $list = $this->getIsBankList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsAliTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_ali']) ? $data['is_ali'] : '');
        $list = $this->getIsAliList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsWxTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_wx']) ? $data['is_wx'] : '');
        $list = $this->getIsWxList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getYxgList()
    {
        return ['0' => __('Yxg 0'), '1' => __('Yxg 1')];
    }


    public function getYxgTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['yxg']) ? $data['yxg'] : '');
        $list = $this->getYxgList();
        return isset($list[$value]) ? $list[$value] : '';
    }



    public function role()
    {
        return $this->belongsTo('Role', 'role_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
