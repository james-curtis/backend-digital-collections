<?php


namespace logicmodel;


use comservice\GetRedis;
use comservice\Response;
use datamodel\Feedback;
use datamodel\Users;
use dh2y\qrcode\QRcode;
use logicmodel\award\Award;
use logicmodel\award\Recommend;
use think\Cookie;
use think\Db;
use think\Exception;
use think\Request;

// require_once('../xasset/index.php');

class UserLogic
{

    private $usersData;

    public function __construct()
    {
        $this->usersData = new Users();
    }

    /**
     * 注册
     * @param $uuid
     * @param $phone
     * @param $password
     * @param $code
     * @param $name
     * @param $card
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function register($uuid, $phone, $password, $code)
    {
        $result = validateCode($phone, $code, 1);
        if (!$result) return Response::fail('验证码输入错误');
        // $phone_code = $this->redis->getItem($phone . '-' . 'fs');
        // if ($phone_code) {
        //     return Response::fail('频繁');
        // }
        // $fs=uniqueNum();
        // $this->redis->setItem($phone . '-' . 'fs', $fs);
        // $this->redis->settime($phone . '-' . 'fs', 10); 
        $info = $this->usersData->where(['phone' => $phone, 'is_del' => 0])->find();
        if ($info) return Response::fail('手机号已注册');

        if (!empty($uuid)) {
            $parentInfo = $this->usersData->where(['uuid' => $uuid, 'is_del' => 0])->find();
            if (empty($parentInfo)) return Response::fail('邀请码错误');
        } else {
            $parentInfo = $this->usersData->orderRaw('id asc')->find();
        }
        $head_image = defaultImage();
        $salt = rand(1111, 9999);
        $uuid = uuid();
        $pid = $parentInfo['id'];
        $data['phone'] = $phone;
        $data['salt'] = $salt;
        $data['password'] = md5(md5($password) . $salt);
        $data['head_image'] = $head_image;
        $data['nick_name'] = 'sp_' . rand(111111, 999999);
        $data['pid'] = $pid;
        $data['upid'] = $pid;
        $data['uuid'] = $uuid;
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['parent_member'] = $parentInfo['phone'];
        $data['wallet_private_key'] = uuid() . uuid();

        try {
            $account = CreateChainAccount([], $data['wallet_private_key']);
            if (array_key_exists('error', $account)) {
                if ($account['error'])
                    return Response::fail($account['error']);
            } else {
                $data['wallet_address'] = $account['data']['account'];
                $data['Nftstatus'] = 1;
            }
        } catch (\Exception $exception) {
            return Response::fail('钱包地址生成失败');
        }

        Db::startTrans();
        $user_id = $this->usersData->saveEntityAndGetId($data);

        if ($user_id > 0) {
            $result = $this->updateGroup($pid);
            if ($result) {
                Db::commit();
                try {
                    (new Recommend())->awardkt($user_id, 0);
//                    (new Recommend())->award($pid, 0);
                } catch (\Exception $exception) {

                }
                return Response::success('注册成功');
            }
            Db::rollback();
            return Response::fail('注册失败');

        }
        Db::rollback();
        return Response::fail('注册失败');
    }

    /**
     * 更新团队信息
     * @param $pid
     * @return bool
     * @throws \think\Exception
     */
    private function updateGroup($pid)
    {

        $field = ['id', 'pid', 'upid'];
        $userData = (new MemberLogic())->listParent($pid, $field, 1, 0);
        $groupArr = array_column($userData, 'id');
        $where['id'] = ['in', $groupArr];
        $result = $this->usersData->updateForInc($where, 'group_person_count', 1); //修改团队成员
        $res = $this->usersData->updateForInc(['id' => $pid], 'total_direct', 1);//修改直推人数
        if ($result > 0 && $res > 0) return true;
        return false;
    }

    private function updateGroup_auth($pid)
    {

        $field = ['id', 'pid', 'upid'];
        $userData = (new MemberLogic())->listParent($pid, $field, 1, 0);
        $groupArr = array_column($userData, 'id');
        $where['id'] = ['in', $groupArr];
        $result = $this->usersData->updateForInc($where, 'group_valid_person_count', 1); //修改团队成员
        $res = $this->usersData->updateForInc(['id' => $pid], 'total_direct_auth', 1);//修改直推人数
        if ($result > 0 && $res > 0) return true;
        return false;
    }

    /**
     * 忘记密码
     * @param $phone
     * @param $password
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function forgetPassword($phone, $password)
    {
        $userInfo = $this->usersData->where(['phone' => $phone, 'is_del' => 0])->find();
        if (empty($userInfo)) return Response::fail('手机号未注册');
        $salt = rand(1111, 9999);
        $data['password'] = md5(md5($password) . $salt);
        $data['salt'] = $salt;
        $result = $this->usersData->updateByWhere(['id' => $userInfo['id']], $data);
        if ($result) return Response::success('修改成功');
        return Response::fail('修改失败');
    }

    public function validateCode($phone, $code)
    {
        $result = validateCode($phone, $code, 2);
        if ($result) return Response::success('验证成功');
        return Response::fail('验证失败');
    }

    /**
     * 会员登录
     * @param $member
     * @param $password
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function login($member, $password)
    {
        $userInfo = $this->usersData->where(['phone' => $member, 'is_del' => 0])->find();
        if (empty($userInfo)) return Response::fail('手机号未注册');
        if ($userInfo['status'] == 0) return Response::fail('账号已冻结');
        if ($userInfo['id'] == 1) return Response::fail('系统账户禁止登陆');
        if (md5(md5($password) . $userInfo['salt']) != $userInfo['password']) return Response::fail('密码错误');
        $app_token = uniqueNum();
        $redis = GetRedis::getRedis();
        $redis->setItem($app_token, $userInfo['id']);
        $result = $this->usersData->updateByWhere(['id' => $userInfo['id']], ['app_token' => $app_token, 'login_time' => date('Y-m-d H:i:s')]);
        if ($result) return Response::success('登录成功', ['app_token' => $app_token]);
        return Response::fail('登录失败');
    }

    /**
     * 验证码登录
     * @param $phone
     * @param $code
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function codeLogin($phone, $code)
    {
        //判断手机号是否注册登录
        $result = validateCode($phone, $code, 3);
        if (!$result) return Response::fail('验证码输入错误');
        $info = $this->usersData->where(['phone' => $phone, 'is_del' => 0])->find();
        if (empty($info)) {
            $parentInfo = $this->usersData->where(['id' => 30682])->find();
            $account = CreateChainAccount([], $phone);

            $uuid = uuid();
            $pid = $parentInfo['id'];
            $data['phone'] = $phone;
            $data['head_image'] = defaultImage();
            $data['nick_name'] = 'sp_' . rand(111111, 999999);
            $data['pid'] = $pid;
            $data['upid'] = $pid;
            $data['uuid'] = $uuid;
            $data['create_time'] = date('Y-m-d H:i:s');
            $data['parent_member'] = $parentInfo['phone'];
            Db::startTrans();
            $user_id = $this->usersData->saveEntityAndGetId($data);
            if ($user_id <= 0) {
                Db::rollback();
                return Response::fail('注册失败');
            }
            $result = $this->updateGroup($pid);
            if (!$result) {
                Db::rollback();
                return Response::fail('注册失败');
            }
            Db::commit();
        } else {
            $user_id = $info['id'];
        }
        $redis = GetRedis::getRedis();
        $app_token = uniqueNum();
        $redis->setItem($app_token, $user_id);
        $result = $this->usersData->updateByWhere(['id' => $user_id], ['app_token' => $app_token, 'login_time' => date('Y-m-d H:i:s')]);

        if ($result) {
            //   (new Recommend())->awardkt($user_id);
            //(new Recommend())->award($user_id);
            Cookie::set('app_token', $app_token);
            return Response::success('登录成功', ['app_token' => $app_token]);
        }
        return Response::fail('登录失败');
    }

    /**
     * 修改登录密码
     * @param $userInfo
     * @param $password
     * @param $password_re
     * @param $code
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function updatePassword($userInfo, $password, $password_re, $code)
    {
        if ($password != $password_re) return Response::fail('两次密码输入不一致');
        $result = validateCode($userInfo['phone'], $code, 2);
        if (!$result) return Response::fail('验证码错误');
        $salt = rand(1111, 9999);
        $password = md5(md5($password) . $salt);
        $data['salt'] = $salt;
        $data['password'] = $password;
        $result = $this->usersData->updateByWhere(['id' => $userInfo['id']], $data);
        if ($result > 0) return Response::success('修改成功');
        return Response::fail('修改失败');
    }

    /**
     * 修改支付密码
     * @param $userInfo
     * @param $pay_password
     * @param $pay_password_re
     * @param $code
     * @param $type
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function updatePayPassword($userInfo, $pay_password, $pay_password_re, $code, $type)
    {
        if ($pay_password != $pay_password_re) return Response::fail('支付面输入不一致');
        if ($type != 1) {
            if ($pay_password != $pay_password_re) return Response::fail('两次密码输入不一致');
            $result = validateCode($userInfo['phone'], $code, 2);
            if (!$result) return Response::fail('验证码错误');
        }

        $salt = rand(1111, 9999);
        $password = md5(md5($pay_password) . $salt);
        $data['pay_salt'] = $salt;
        $data['pay_password'] = $password;
        $result = $this->usersData->updateByWhere(['id' => $userInfo['id']], $data);
        if ($result > 0) return Response::success('修改成功');
        return Response::fail('修改失败');
    }

    /**
     * 会员信息
     * @param $userInfo
     * @return array
     */
    public function userInfo($userInfo)
    {
        $data['id'] = $userInfo['id'];
        $data['nick_name'] = $userInfo['nick_name'];
        $data['role_id'] = $userInfo['role_id'];
        $data['phone'] = $userInfo['phone'];
        $data['uuid'] = $userInfo['uuid'];
        $data['total_direct'] = $userInfo['total_direct'];
        $data['wallet_address'] = $userInfo['wallet_address'];
        $data['wallet_private_key'] = $userInfo['wallet_private_key'];
        $data['wallet_public_key'] = $userInfo['wallet_public_key'];
        $data['name'] = $userInfo['name'];
        $data['card'] = $userInfo['card'];
        $data['card_front_image'] = $userInfo['card_front_image'];
        $data['card_back_image'] = $userInfo['card_back_image'];
        $data['is_auth'] = $userInfo['is_auth'];
        $data['wx_small_auth'] = $userInfo['wx_small_auth'];
        $data['head_image'] = $userInfo['head_image'];
        if (!$userInfo['head_image'])
            $data['head_image'] = config('site.default_image');
        $data = addWebSiteUrl($data);
//        $data = addWebSiteUrl($data,['head_image','card_front_image','card_back_image']);
        return Response::success('success', $data);
    }

    /**
     * 编辑会员信息
     * @param $uid
     * @param $nick_name
     * @param $head_image
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function editUserInfo($uid, $nick_name, $head_image)
    {
        $data['head_image'] = $head_image;
        $data['nick_name'] = $nick_name;
        $data = trimWebUrl($data, ['head_image']);
        $result = $this->usersData->updateByWhere(['id' => $uid], $data);
        if ($result) return Response::success('修改成功');
        return Response::fail('编辑失败');
    }

    /**
     * 海报分享
     * @param $userInfo
     * @return array
     * @throws \think\Exception
     */
    public function share($userInfo)
    {
        //海报背景图
        $invite_image = config('site.share_image');
        $invite_image = trim($invite_image, '/');
        //设置背景图
        config('qrcode.background', $invite_image);
        $url = config('site.register_url');
        $code = new QRcode();
        $uuid = $userInfo['id'];
        $register_url = $url . '#/pages/login/reg?invite=' . $userInfo['uuid'];
        $qr_code_img = $code
            ->png($register_url, 'uploads/qrcode/' . $uuid . '.png', 6)
            ->background(240, 600)
            ->text($userInfo['uuid'], 45, ['center', 1000], '#FFF296')
            ->getPath();
        $data['qr_code_img'] = str_replace('\\', '/', $qr_code_img);
        $data['register_url'] = $register_url;
        $data['invite_image'] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $invite_image;
        $data['uuid'] = $uuid;
        $data = addWebSiteUrl($data, ['qr_code_img']);
        return Response::success('success', $data);
    }

    /**
     * 切换手机号
     * @param $userInfo
     * @param $phone
     * @param $code
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function checkPhone($userInfo, $phone, $code)
    {
        $result = validateCode($phone, $code, 1);
        if (!$result) return Response::fail('验证码错误');
        if ($phone == $userInfo['phone']) return Response::fail('新旧手机号不能相同');
        $info = $this->usersData->where(['phone' => $phone, 'is_del' => 0])->find();
        if ($info) return Response::fail('新手机号已绑定');
        $result = $this->usersData->updateByWhere(['id' => $userInfo['id']], ['phone' => $phone]);
        if ($result) return Response::success('更换成功');
        return Response::fail('更换失败');
    }

    /**
     * 团队信息
     * @param $userInfo
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function team($userInfo)
    {
        $uid = $userInfo['id'];
        $where['pid'] = $uid;
        $where['is_del'] = 0;
        $field = ['phone', 'head_image', 'create_time'];
        $data = $this->usersData
            ->where($where)
            ->field($field)
            ->order(['id desc'])
            ->select();
        if ($data) {
            $data = collection($data)->toArray();
            $data = addWebSiteUrl($data, ['head_image']);
        }
        return Response::success('暂无数据', $data);
    }

    /**
     * 收款信息
     * @param $userInfo
     * @return array
     */
    public function collection($userInfo)
    {
        $data['is_bank'] = $userInfo['is_bank'];
        $data['is_ali'] = $userInfo['is_ali'];
        $data['is_wx'] = $userInfo['is_wx'];
        $data['bank_name'] = $userInfo['bank_name'];
        $data['bank_number'] = $userInfo['bank_number'];
        $data['bank_owner'] = $userInfo['bank_owner'];
        $data['bank_branch'] = $userInfo['bank_branch'];
        $data['bank_comment'] = $userInfo['bank_comment'];
        $data['ali_name'] = $userInfo['ali_name'];
        $data['ali_image'] = $userInfo['ali_image'];
        $data['wx_name'] = $userInfo['wx_name'];
        $data['wx_image'] = $userInfo['wx_image'];
        // $data = addWebSiteUrl($data,['wx_image','ali_image']);
        return Response::success('success', $data);
    }

    /**
     * 收款信息
     * @param $userInfo
     * @param $bank_name
     * @param $bank_number
     * @param $bank_owner
     * @param $bank_branch
     * @param $ali_name
     * @param $ali_image
     * @param $wx_name
     * @param $wx_image
     * @param $code
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function collectMoney($userInfo, $bank_name, $bank_number, $bank_owner, $bank_branch, $ali_name, $ali_image, $wx_name, $wx_image, $code, $bank_comment)
    {
        if ($code !== '') {
            $result = validateCode($userInfo['phone'], $code, 2);
            if (!$result) return Response::fail('验证码错误');
        }
        $data['bank_name'] = $bank_name;
        $data['bank_number'] = $bank_number;
        $data['bank_owner'] = $userInfo['name'];
        $data['bank_branch'] = $bank_branch;
        $data['bank_comment'] = $bank_comment;
        if (!empty($bank_name) && !empty($bank_number) && !empty($bank_owner)) {
            $data['is_bank'] = 1;
        }
        $data['ali_name'] = $ali_name;
        $data['ali_image'] = $ali_image;
        if (!empty($ali_name) && !empty($ali_image)) {
            $data['is_ali'] = 1;
        }
        $data['wx_name'] = $wx_name;
        $data['wx_image'] = $wx_image;
        if (!empty($wx_name) && !empty($wx_image)) {
            $data['is_wx'] = 1;
        }
        $data = trimWebUrl($data, ['ali_image', 'wx_image']);
        $result = $this->usersData->updateByWhere(['id' => $userInfo['id']], $data);
        if ($result) return Response::success('编辑成功');
        return Response::fail('编辑失败');
    }


    /**
     * 问题反馈
     * @param $uid
     * @param $images
     * @param $remark
     * @return array
     */
    public function feedback($uid, $images, $remark)
    {
        $data['uid'] = $uid;
        $data['images'] = $images;
        $data['remark'] = $remark;
        $data['create_time'] = date('Y-m-d H:i:s');
        $data = trimWebUrl($data, ['images']);
        $result = (new Feedback())->saveEntityAndGetId($data);
        if ($result > 0) return Response::success('反馈成功');
        return Response::fail('反馈失败');
    }

    /**
     * 认证
     * @param $userInfo
     * @param $name
     * @param $card
     * @param $card_front_image
     * @param $card_back_image
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    // public function auths($userInfo,$name,$card){
    //     if($userInfo['is_auth'] == 1) return Response::fail('你已实名认证,请勿重复提交');
    //      $arrs=beckoning($card,$userInfo['phone'],$name);
    //     $arrs= json_decode($arrs,true);
    //     // print_r($arrs);
    //     // exit;
    //      if($arrs['code']==200){
    //          if($arrs['data']['result']==0){
    //                  $data['name'] = $name;
    //       $data['card'] = $card;
    //     $data['is_auth'] = 1;
    //     $result =  $this->usersData->updateByWhere(['id'=>$userInfo['id']],$data);
    //     if($result){
    //       if($userInfo['pid']!==1){
    //             $result =  $this->updateGroup_auth($userInfo['pid']);
    //             (new Recommend())->award($userInfo['pid']);   //拉新
    //         }
    //         (new Recommend())->awardkt($userInfo['id']);  //注册

    //         Db::commit();
    //         return Response::success('提交成功');
    //         }
    //       return Response::fail('提交失败');
    //          }else{
    //              return Response::fail('姓名与身份证上的姓名不一致');
    //          }

    //      }else{
    //          return Response::fail($arrs['msg']);
    //      }


    // }
    public function auth($userInfo, $name, $card)
    {
        if ($userInfo['is_auth'] == 1) return Response::fail('你已实名认证,请勿重复提交');
        try {
            $arrs = beckoning($card, $userInfo['phone'], $name);
            $arrs = json_decode($arrs, true);
            if ($arrs['code'] != '0')
                return Response::fail($arrs['message'] ?? '错误');
            if ($arrs['result']['res'] != '1')
                return Response::fail('信息不一致');
        } catch (\Exception $exception) {
            return Response::fail('实名失败');
        }

        $data['name'] = $name;
        $data['card'] = $card;
        // $data['card_front_image'] = $card_front_image;
        // $data['card_back_image'] = $card_back_image;
        $data['is_auth'] = 1;
        $class_id = session_create_id();
//        $classes = CreateChainClasses($userInfo['wallet_address'], $class_id, $userInfo['id']);
// print_r($classes);

        $data['class_id'] = $class_id;
        // $data = trimWebUrl($data,['card_front_image','card_back_image']);
        $result = $this->usersData->updateByWhere(['id' => $userInfo['id']], $data);
        if ($result) {
            if ($userInfo['pid'] != 0) {
                $result = $this->updateGroup_auth($userInfo['pid']);
                (new Recommend())->award($userInfo['pid']);   //拉新
            }
            (new Recommend())->awardkt($userInfo['id'], 1);  //注册

            Db::commit();
            return Response::success('提交成功');
        }
        return Response::fail('提交失败');
    }

    /**
     * 绑定信息
     * @param $wx_open_id
     * @param $wx_union_id
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function wxAuth($wx_open_id, $wx_union_id)
    {
        $user_id = $this->usersData->where(['wx_open_id' => $wx_open_id, 'is_del' => 0])->value('id');
        if (empty($user_id)) {
            $parentInfo = $this->usersData->where(['id' => 1])->find();
            $account = (new WalletLogic())->newAccount();
            if ($account === false) return Response::fail('地址生成失败');
            $uuid = uuid();
            $pid = $parentInfo['id'];
            $data['head_image'] = defaultImage();
            $data['nick_name'] = 'sp_' . rand(111111, 999999);
            $data['pid'] = $pid;
            $data['upid'] = $pid;
            $data['uuid'] = $uuid;
            $data['create_time'] = date('Y-m-d H:i:s');
            $data['parent_member'] = $parentInfo['phone'];
            $data['wallet_address'] = $account['address'];
            $data['wallet_private_key'] = $account['private_key'];
            $data['bsc_wallet_address'] = $account['bsc_address'];
            $data['bsc_private_key'] = $account['bsc_private_key'];
            $data['ht_wallet_address'] = $account['ht_address'];
            $data['ht_private_key'] = $account['ht_private_key'];
            $data['wx_open_id'] = $wx_open_id;
            $data['wx_union_id'] = $wx_union_id;
            Db::startTrans();
            $user_id = $this->usersData->saveEntityAndGetId($data);
            if ($user_id <= 0) {
                Db::rollback();
                return Response::fail('注册失败');
            }
            $result = $this->updateGroup($pid);
            if (!$result) {
                Db::rollback();
                return Response::fail('注册失败');
            }
            Db::commit();
        }
        $redis = GetRedis::getRedis();
        $app_token = uniqueNum();
        $redis->setItem($app_token, $user_id);
        $result = $this->usersData->updateByWhere(['id' => $user_id], ['app_token' => $app_token, 'login_time' => date('Y-m-d H:i:s')]);
        if ($result) return Response::success('登录成功', ['app_token' => $app_token]);
        return Response::fail('登录失败');
    }

    /**
     * 绑定手机号
     * @param $phone
     * @param $code
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function bindPhone($phone, $code)
    {
        $result = validateCode($phone, $code, 3);
        if (!$result) return Response::fail('验证码输入错误');
        $userInfo = $this->usersData->where(['phone' => $phone, 'is_del' => 0])->find();
        $token = Request::instance()->header('token');
        if (empty($token)) return Response::invalidLogin('请先登录');
        $redis = GetRedis::getRedis();
        $uid = $redis->getItem($token);
        if (empty($uid) || $uid == false || !$uid) return Response::invalidLogin('请先登录');
        if (empty($userInfo)) {
            $this->usersData->where(['id' => $uid])->update(['phone' => $phone]);
        } else {
            $info = $this->usersData->find($uid);
            $data['wx_open_id'] = $info['wx_open_id'];
            $data['wx_union_id'] = $info['wx_union_id'];
            $result = $this->usersData->where(['id' => $userInfo['id']])->update($data);
            $res = $this->usersData->where(['id' => $uid])->update(['is_del' => 1, 'status' => 0]);
            if (!$result || !$res) return Response::fail('绑定失败');
            $token = uniqueNum();
            $redis = GetRedis::getRedis();
            $redis->setItem($token, $userInfo['id']);
            $this->usersData->updateByWhere(['id' => $userInfo['id']], ['app_token' => $token, 'login_time' => date('Y-m-d H:i:s')]);
        }
        return Response::success('绑定成功', ['app_token' => $token]);
    }
}