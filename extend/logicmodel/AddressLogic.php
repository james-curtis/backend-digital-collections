<?php


namespace logicmodel;


use comservice\Response;
use datamodel\Address;

class AddressLogic
{
    private $addressData;
    public function __construct()
    {
        $this->addressData = new Address();
    }

    /**
     * 地址列表
     * @param $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addressList($uid){
        $where['uid'] = $uid;
        $where['is_del'] = 0;
        $data = $this->addressData->where($where)->order(['id default'])->select();
        if($data) return Response::success('success',collection($data)->toArray());
        return  Response::success('暂无数据');
    }

    /**
     * 新增地址
     * @param $uid
     * @param $province
     * @param $city
     * @param $area
     * @param $address
     * @param $name
     * @param $phone
     * @param $is_default
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function addAddress($uid,$province,$city,$area,$address,$name,$phone,$is_default){
        if($is_default == 1) $this->addressData->updateByWhere(['uid'=>$uid],['is_default'=>0]);
        $data['uid'] = $uid;
        $data['province'] = $province;
        $data['city'] = $city;
        $data['area'] = $area;
        $data['address'] = $address;
        $data['name'] = $name;
        $data['phone'] = $phone;
        $data['is_default'] = $is_default;
        $data['create_time'] = date('Y-m-d H:i:s');
        $result = $this->addressData->saveEntityAndGetId($data);
        if($result) return Response::success();
        return Response::fail('添加失败');
    }

    /**
     * 编辑地址
     * @param $uid
     * @param $id
     * @param $province
     * @param $city
     * @param $area
     * @param $address
     * @param $name
     * @param $phone
     * @param $is_default
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function editAddress($uid,$id,$province,$city,$area,$address,$name,$phone,$is_default){
        if($is_default == 1) $this->addressData->updateByWhere(['uid'=>$uid],['is_default'=>0]);
        $data['uid'] = $uid;
        $data['province'] = $province;
        $data['city'] = $city;
        $data['area'] = $area;
        $data['address'] = $address;
        $data['name'] = $name;
        $data['phone'] = $phone;
        $data['is_default'] = $is_default;
        $result = $this->addressData->updateByWhere(['id'=>$id],$data);
        if($result) return Response::success('编辑成功');
        return Response::fail('编辑失败');
    }

    /**
     * 删除地址
     * @param $id
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteAddress($id){
        $result = $this->addressData->updateByWhere(['id'=>$id],['is_del'=>1]);
        if($result) return Response::success('删除成功');
        return Response::fail('删除失败');
    }
}