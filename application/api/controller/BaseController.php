<?php


namespace app\api\controller;


use app\lib\exception\LoginException;
use app\lib\exception\PhoneException;
use comservice\GetRedis;
use datamodel\Users;
use think\Controller;
use think\Request;

class BaseController extends Controller
{
    protected $uid;
    protected $userInfo;
    public function __construct(Request $request = null)
    {

        $this->checkLogin();
        parent::__construct($request);
    }

    public function checkLogin(){
        $token =  Request::instance()->header('token');
        if (empty($token) || $token==''){
            throw  new LoginException('用户身份失效,请先登录');
             // $uid = 1;
        }else {
            $redis = GetRedis::getRedis();
            $uid = $redis->getItem($token);
            if (empty($uid) || $uid == false || !$uid){
                throw  new LoginException('用户身份失效,请先登录');
            }
        }
        $field = ['u.*','r.name rank_name'];
        $userInfo = (new Users())->alias('u')
            ->join('rank r','r.id = u.rank_id')
            ->where(['u.is_del'=>0,'u.id'=>$uid])
            ->field($field)
            ->find();
        if(empty($userInfo))   throw  new LoginException('用户身份信息错误');
        if(empty($userInfo['phone']))   throw  new PhoneException('请先绑定手机号');
        if($userInfo['status'] == 0)   throw  new LoginException('账户已冻结');
        // if($userInfo['app_token'] != $token)  throw  new LoginException('当前账号已在其他设备登录,您已强制下线!');
        $this->uid = $uid;
        $this->userInfo = $userInfo->toArray();
    }
}