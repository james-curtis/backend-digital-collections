<?php


namespace app\api\controller;


use logicmodel\AddressLogic;
use think\Request;

class Address extends BaseController
{
    private $addressLogic;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->addressLogic = new AddressLogic();
    }

    /**
     * 地址列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addressList(){
        return json($this->addressLogic->addressList($this->uid));
    }

    /**
     * 新增地址
     * @param $province
     * @param $city
     * @param $area
     * @param $address
     * @param $name
     * @param $phone
     * @param $is_default
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function addAddress($province,$city,$area,$address,$name,$phone,$is_default){
        return json($this->addressLogic->addAddress($this->uid,$province,$city,$area,$address,$name,$phone,$is_default));
    }

    /**
     * 编辑地址信息
     * @param $id
     * @param $province
     * @param $city
     * @param $area
     * @param $address
     * @param $name
     * @param $phone
     * @param $is_default
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function editAddress($id,$province,$city,$area,$address,$name,$phone,$is_default){
        return json($this->addressLogic->editAddress($this->uid,$id,$province,$city,$area,$address,$name,$phone,$is_default));
    }

    /**
     * 删除地址信息
     * @param $id
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteAddress($id){
        return json($this->addressLogic->deleteAddress($id));
    }

}