<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use comservice\Response;
use logicmodel\AccountLogic;
use logicmodel\award\Recommend;
use logicmodel\MemberLogic;
use logicmodel\WalletLogic;
use datamodel\GoodsUsers;
use think\Db;
use comservice\GetRedis;
use datamodel\Goods;
use app\admin\model\GoodsMangheUsers;

use Exception;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use think\Env;

// require_once('../xasset/index.php');

/**
 *
 *
 * @icon fa fa-users
 */
class Users extends Backend
{

    /**
     * Users模型对象
     * @var \app\admin\model\Users
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Users;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("nftstatusList", $this->model->getNftstatusList());
        $this->view->assign("isDelList", $this->model->getIsDelList());
        $this->view->assign("isAuthList", $this->model->getIsAuthList());
        $this->view->assign("isBankList", $this->model->getIsBankList());
        $this->view->assign("isAliList", $this->model->getIsAliList());
        $this->view->assign("isWxList", $this->model->getIsWxList());
        $this->view->assign("yxgList", $this->model->getYxgList());


    }

    public function import()
    {
        parent::import();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        $offset = input('offset');
        $limit = input('limit');
        // 获取搜索框的值
        $filter = input('filter');
        $snapshot = $this->request->post('params');

        //exit();
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }


            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            //判断是否有值
            if (strlen($filter) > 2) {
                $filter = json_decode($filter, true);
                if (array_key_exists('goods_id', $filter)) {

                    $wheres['phone'] = $filter['goods_id'];

                    $goodsuserst = Db::name('goods_users')->where('goods_id', $filter['goods_id'])->where('uid', 'not in', 1)->count();
                    $goodsusers = Db::name('goods_users')->where('goods_id', $filter['goods_id'])->where('uid', 'not in', 1)->select();
                    if (empty($goodsuserst)) {
                        //有藏品为空查询
                        $list = $this->model
                            ->with(['role'])
                            ->where(['is_del' => 0])
                            ->where($where)
                            ->order($sort, $order)
                            ->paginate($limit);;
                    } else {
                        //有藏品查询
                        foreach ($goodsusers as $value) {
                            $arr[] = $value['uid'];
                        }
                        unset($filter['goods_id']);
                        $this->request->get(['filter' => json_encode($filter)]);
                        list($where, $sort, $order, $offset, $limit) = $this->buildparams();
                        $list = $this->model
                            ->with(['role'])
                            ->where(['is_del' => 0])
                            ->where($where)
                            ->where('users.id', 'in', $arr)
                            ->order($sort, $order)
                            ->paginate($limit);
                    }


                } else {
                    if (array_key_exists('kz', $filter)) {
                        //快照查询
                        $config = Db::name('config')->where('group', 'timeconfiguration')->select();

                        $goods_users = Db::name('goods_users')->where('create_time', $config[0]['value'])->where('status', 1)->select();
                        print_r($goods_users);
                        $list = $this->model
                            ->with(['role'])
                            ->where(['is_del' => 0])
                            ->where('users.id', 1)
                            ->order($sort, $order)
                            ->paginate($limit);
                    } else {
                        //没有藏品查询
                        $list = $this->model
                            ->with(['role'])
                            ->where(['is_del' => 0])
                            ->where($where)
                            ->order($sort, $order)
                            ->paginate($limit);
                    }

                }
            } else {


                //初始化查询
                $list = $this->model
                    ->with(['role'])
                    ->where(['is_del' => 0])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);
            }


            foreach ($list as $row) {
                $goodsusers = Db::name('goods_users')
                    ->where([
                        'uid' => $row['id'],
                        'is_del' => 0,
                    ])
                    ->count();
                $this->model->where('id', $row['id'])->update(['cpzs' => $goodsusers]);
                $row->visible(['total_direct_auth', 'id', 'member', 'nick_name', 'head_image', 'phone', 'status', 'uuid', 'total_direct', 'group_person_count', 'achievement_money', 'group_achievement_money', 'parent_member', 'nftstatus', 'create_time', 'name', 'card', 'is_bank', 'is_auth', 'is_wx', 'is_ali', 'wholesale_account', 'award_recommend', 'wallet_address', 'cpzs', "Nftstatus", "group_valid_person_count"]);
                $row->visible(['role']);
                $row->getRelation('role')->visible(['name']);
                $row['cpzs'] = $goodsusers;
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }


    public function goods()
    {
        $json = cache('goods_id');
        if ($json === false) {
            $list = Db::name('goods')->select();
            $json = json($list);
            cache('goods_id', $json);
        }
        return $json;
    }

    // 创建nft类别
    // 用户账号上链
    public function nftupdate($ids = "")
    {
        foreach ($ids as $key => $value) {
            $users = Db::name('users')->where('id', $value)->find();
            if ($users['class_id'] == null or true) {
                if (empty($users['wallet_private_key'])) {
                    $this->model->where('id', $value)->update([
                        'wallet_private_key' => uuid() . uuid(),
                    ]);
                }
                $class_id = uniqueNum();
                $classes = CreateChainClasses($users['wallet_address'], $class_id, $users['id'], $users);
                if (array_key_exists('error', $classes)) {
                    return Response::fail($classes['error']);
                }
                $result = $this->model->where('id', $value)->update([
                    'class_id' => $class_id,
                    'wallet_address' => $classes['data']['account'],
                    'Nftstatus' => 1
                ]);
            } else {
                return json(['code' => 0, 'msg' => '请选择未上链的数据！']);
            }
        }

        if ($result) return json(['code' => 1, 'msg' => '上链成功']);
        return json(['code' => 0, 'msg' => '上链失败']);
    }

    public function plstatus($ids = "")
    {
        foreach ($ids as $k => $v) {
            $this->model->where('id', $v)->update(['yxg' => 1]);
        }
        $this->success();
    }

    public function qxstatus($ids = "")
    {
        foreach ($ids as $k => $v) {
            $arrs[] = ['id' => $v, 'yxg' => 0];
        }
        $this->model->saveAll($arrs);
        $this->success();
    }

    public function add()
    {

        if (request()->isPost()) {
            $data = input('post.');
            $data = $data['row'];
            $parent_member = $data['parent_member'];
            $parentInfo = $this->model->where(['is_del' => 0, 'phone' => $parent_member])->find();
            if (empty($parentInfo)) $this->error('推荐人手机号错误');
            $phone = $data['phone'];
            if (empty($phone)) $this->error('手机号不能为空');
            $info = $this->model->where(['phone' => $phone, 'is_del' => 0])->find();
            if ($info) $this->error('手机号已注册');
            //调用地址方法
            $account = CreateChainAccount([], $data['phone']);
            if (array_key_exists('error', $account)) {
                return Response::fail('钱包地址生成失败');
            } else {
                $userData['wallet_address'] = $account['data']['account'];
            }
            $member = $phone;
            $password = $data['password'];
            if (empty($password)) $this->error('请输入登录密码');;
            $salt = rand(1111, 9999);
            $pid = $parentInfo['id'];
            $userData['nick_name'] = 'zz_' . rand(111111, 999999);
            $userData['member'] = $member;
            $userData['salt'] = $salt;
            $userData['password'] = md5(md5($password) . $salt);
            $userData['head_image'] = defaultImage();
            $userData['uuid'] = uuid();
            $userData['pid'] = $pid;
            $userData['upid'] = $pid;
            $userData['phone'] = $phone;
            $userData['parent_member'] = $parentInfo['phone'];
            $userData['create_time'] = date('Y-m-d H:i:s');
            $userData['role_id'] = $data['role_id'];
            Db::startTrans();
            $user_id = $this->model->insertGetId($userData);
            if ($user_id > 0) {
                $result = $this->updateGroup($pid);
                if (!$result) {
                    Db::rollback();
                    $this->error('注册失败');
                }
                Db::commit();
                $this->success('注册成功');

            }
            Db::rollback();
            $this->error('注册失败');
        }
        return $this->fetch();
    }

    //余额充值
    public function plrecharge($ids = "", $account = "")
    {


        //$account = $data['account'];
        if ($account <= 0) return json(['code' => 0, 'msg' => '操作金额需大于0']);
        $type = 1;
        $currency_id = 1;
        Db::startTrans();
        if ($type == 1) {
            //$currency_id = 1;
            //充值
            $bil_type = '后台充值';
            $remark = '后台充值';
            foreach ($ids as $value) {
                $result = (new AccountLogic())->addAccount($value, $currency_id, $account, $bil_type, $remark);
                if ($result == false) {
                    Db::rollback();
                    return json(['code' => 0, 'msg' => '充值失败']);
                }
            }
        }


        foreach ($ids as $values) {
            $fundsData = ['uid' => $values, 'currency_id' => $currency_id, 'account' => $account, 'type' => $type, 'create_time' => date('Y-m-d H:i:s')];
            $result = Db::name('funds')->insertGetId($fundsData);
        }
        if ($result > 0) {
            Db::commit();
            return json(['code' => 1, 'msg' => '操作成功']);
        }
        return json(['code' => 0, 'msg' => '操作失败']);
        return $this->fetch();
    }

    public function plgive($ids = "")
    {
        //$data = input('post.');
        $goodsUsersData = new GoodsUsers();
        if (request()->isPost()) {
            $data = input('post.');
            $idlist = explode(",", $data['id']);
            $goods_id = $data['goods_id'];
            $goods = (new \app\admin\model\Goods())->where(['id' => $goods_id, 'is_del' => 0])->find();
            if (empty($goods)) return json(['code' => 0, 'msg' => '藏品信息错误']);
            if ($goods['surplus'] < 0) return json(['code' => 0, 'msg' => '藏品已用完']);

            $redis = GetRedis::getRedis();

            $user_count = count($idlist);
            if ($user_count > $goods['surplus']) {
                return json(['code' => 0, 'msg' => '藏品剩余' . $goods['surplus'] . '份,不够分配.']);
            }

            $price = $data['price'];
            if (empty($price)) $price = $goods['price'];

            $size = $data['size'];
            if (empty($size)) $price = 1;

            function gen($goods, $idlist, $goodsUsersData, $goods_id, $price, $redis)
            {
                $goodsData = new Goods();

                //生成

                if ($goods['is_manghe'] == 0) {
                    foreach ($idlist as $value) {

                        $users = Db::name('users')->where('id', $value)->find();


                        $goods_user_number = $goodsUsersData->where(['goods_id' => $goods_id])->whereNotNull('number')->order('id', 'desc')->value('number');

                        if ($goods_user_number) {
                            $goods_user_number = str_pad($goods_user_number + 1, 6, '0', STR_PAD_LEFT);
                        } else {
                            $goods_user_number = '000001';
                        }
                        $user = ['uid' => $value, 'goods_id' => $goods_id, 'price' => $price, 'number' => $goods_user_number, 'create_time' => date('Y-m-d H:i:s'), 'is_send' => 1];
                        Db::startTrans();
                        $results = (new \app\admin\model\GoodsUsers())->insertGetId($user);
                        if (!$results) {
                            Db::rollback();
                            return json(['code' => 0, 'msg' => '赠送失败']);
                        }
                        $send = ['uid' => $value, 'goods_id' => $goods_id, 'price' => $price, 'create_time' => date('Y-m-d H:i:s')];

                        Db::name('goods_users')->where('id', $results)->update(['jlstatus' => 1]);
                        $result = (new \app\admin\model\GoodsSend())->insertGetId($send);
                        if (!$result) {
                            Db::rollback();
                            return json(['code' => 0, 'msg' => '赠送失败']);
                        }

//                        // redis 减少库存
//                        $goods_kc_count = $redis->rpop('goods_kc_' . $goods['id']);
//
//                        if (!$goods_kc_count) {
//                            Db::rollback();
//                            return Response::fail('没有库存了');
//                        }

                        //库存剩余递减 销量递增
                        $goodsData->where(['id' => $goods['id']])->setDec('surplus', 1);
                        $goodsData->where(['id' => $goods['id']])->setInc('sales', 1);

                        Db::commit();
                    }

                    return json(['code' => 1, 'msg' => '赠送成功']);
                }


                //生成
                if ($goods['is_manghe'] == 1) {
                    foreach ($idlist as $value) {
                        $mhid = Db::name('goods_manghe_users')->max('id');
                        $mhstr = "mh" . str_pad(($mhid + 1), 5, "0", STR_PAD_LEFT);
                        $users = Db::name('users')->where('id', $value)->find();
                        //调用地址方法
                        //创建nft类别

                        $user = ['user_id' => $value, 'goods_id' => $goods_id, 'createtime' => time(), 'updatetime' => time(), 'status' => 2, 'state' => 1];
                        Db::startTrans();
                        $results = (new \app\admin\model\GoodsMangheUsers())->insertGetId($user);
                        if (!$results) {
                            Db::rollback();
                            return json(['code' => 0, 'msg' => '赠送失败']);
                        }
                        $send = ['uid' => $value, 'goods_id' => $goods_id, 'price' => $price, 'status' => 1, 'create_time' => date('Y-m-d H:i:s')];
                        $result = (new \app\admin\model\GoodsSend())->insertGetId($send);
                        if (!$result) {
                            Db::rollback();
                            return json(['code' => 0, 'msg' => '赠送失败']);
                        }

//                        // redis 减少库存
//                        $goods_kc_count = $redis->rpop('goods_mh_' . $goods['id']);
//
//                        if (!$goods_kc_count) {
//                            Db::rollback();
//                            return Response::fail('没有库存了');
//                        }

                        //库存剩余递减 销量递增
                        //库存剩余递减 销量递增
                        $goodsData->where(['id' => $goods['id']])->setDec('surplus', 1);
                        $goodsData->where(['id' => $goods['id']])->setInc('sales', 1);

                        Db::commit();
                    }

                    return json(['code' => 1, 'msg' => '赠送成功']);
                }
            }

            $res = null;
            while ($size--) {
                $res = gen($goods, $idlist, $goodsUsersData, $goods_id, $price, $redis);
            }
            return $res;

        }
        return $this->fetch();
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
        $result = $this->model->where($where)->setInc('group_person_count', 1); //修改团队成员
        $res = $this->model->where(['id' => $pid])->setInc('total_direct', 1);//修改直推人数
        if ($result > 0 && $res > 0) return true;
        return false;
    }

    /**
     * 更新团队有效（已注册）信息
     * @param $pid
     * @return bool
     * @throws \think\Exception
     */
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

    public function del($ids = "")
    {
        if (in_array('0', explode(',', $ids))) {
            return json(['code' => 0, 'msg' => '无法删除id为0的系统用户']);
        }
        // 启动事务
        Db::startTrans();
        try {
            $result = $this->model->where(['id' => ['in', $ids]])->update(['is_del' => 1]);
            $resultdel = Db::name('goods_users')->where(['uid' => ['in', $ids]])->update(['is_del' => 1]);
            // 提交事务
            Db::commit();
            return json(['code' => 1, 'msg' => '删除成功']);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return json(['code' => 0, 'msg' => '删除失败']);
        }


    }

    public function edit($ids = null)
    {
        if (request()->isPost()) {
            $data = input('post.');
            $data = $data['row'];
            if (!empty($data['password'])) {
                $salt = rand(1111, 9999);
                $password = md5(md5($data['password']) . $salt);
                $data['password'] = $password;
                $data['salt'] = $salt;
            } else {
                unset($data['password']);
            }
            if (!empty($data['pay_password'])) {
                $salt = rand(1111, 9999);
                $password = md5(md5($data['pay_password']) . $salt);
                $data['pay_password'] = $password;
                $data['pay_salt'] = $salt;
            } else {
                unset($data['pay_password']);
            }

            if ($data['group_valid_person_count'] > $data['group_person_count']) {
                return json(['code' => 0, 'msg' => '团队有效人数不能大于团队人数']);

            }

            $result = $this->model->where(['id' => $ids])->update($data);
            if ($result) $this->success('修改成功');
            $this->success('修改失败');
        }
        $row = $this->model->find($ids)->toArray();
        $this->assign('row', $row);
        return $this->fetch();
    }

    /**
     * 资金操作
     * @param $ids
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function funds($ids)
    {
        if (request()->post()) {
            $data = input('post.');
            if (empty($data['currency_id'])) return json(['code' => 0, 'msg' => '请选择要操作币种']);
            if (empty($data['type'])) return json(['code' => 0, 'msg' => '请选择操作类型']);
            if (empty($data['account'])) return json(['code' => 0, 'msg' => '请输入金额']);
            $account = $data['account'];
            if ($account <= 0) return json(['code' => 0, 'msg' => '操作金额需大于0']);
            $type = $data['type'];
            $currency_id = $data['currency_id'];
            Db::startTrans();
            if ($type == 1) {
                $currency_id = $data['currency_id'];
                //充值
                $bil_type = '后台充值';
                $remark = '后台充值';

                $result = (new AccountLogic())->addAccount($ids, $currency_id, $account, $bil_type, $remark);
                if ($result == false) {
                    Db::rollback();
                    return json(['code' => 0, 'msg' => '充值失败']);
                }
            } elseif ($type == 2) {
                //扣费
                //充值
                $bil_type = '后台扣费';
                $remark = '后台扣费';
                $result = (new AccountLogic())->subAccount($ids, $currency_id, $account, $bil_type, $remark);
                if ($result == false) {
                    Db::rollback();
                    return json(['code' => 0, 'msg' => '账户余额不足']);
                }

            } else {
                return json(['code' => 0, 'msg' => '操作类型错误']);
            }
            $fundsData = ['uid' => $ids, 'currency_id' => $currency_id, 'account' => $account, 'type' => $type, 'create_time' => date('Y-m-d H:i:s')];
            $result = Db::name('funds')->insertGetId($fundsData);
            if ($result > 0) {
                Db::commit();
                return json(['code' => 1, 'msg' => '操作成功']);
            }
            return json(['code' => 0, 'msg' => '操作失败']);
        }
        $field = ['ua.*', 'c.name currency_name'];
        $where['uid'] = $ids;
        $userAccount = Db::name('users_account')->alias('ua')
            ->join('currency c', 'c.id = ua.currency_id')
            ->field($field)
            ->where($where)
            ->select();
        $currencyData = Db::name('currency')->where(['is_del' => 0])->select();
        $userInfo = $this->model->find($ids);
        $this->assign('userInfo', $userInfo);
        $this->assign('currencyData', $currencyData);
        $this->assign('userAccount', $userAccount);
        $this->assign('ids', $ids);
        return $this->fetch();
    }

    public function team($phone = '')
    {
        if (request()->isAjax()) {
            if (empty($phone)) {
                $data = $this->model->alias('u')
                    ->join('rank r', 'r.id = u.rank_id')
                    ->join('role ro', 'ro.id = u.role_id')
                    ->where(['u.pid' => 0])
                    ->field(['u.*', 'r.name rank_name'])
                    ->select();
                return json($data);
            }
            $field = ['pu.*', 'r.name rank_name', 'r.name role_name'];
            $data = $this->model->alias('u')
                ->join('users pu', 'u.id = pu.pid')
                ->join('role ro', 'ro.id = u.role_id')
                ->where(['u.phone' => $phone, 'pu.is_del' => 0])
                ->join('rank r', 'r.id = pu.rank_id')
                ->field($field)
                ->select();
            return $data;
        }
        return $this->fetch();
    }

    /**
     * 搜索
     * @param string $phone
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function search($phone = '')
    {

        if (empty($phone)) {

            $where ['u.pid'] = 0;
        } else {
            $where ['u.phone'] = $phone;
            $where['is_del'] = 0;
        }

        $data = $this->model
            ->alias('u')
            ->join('rank r', 'r.id = u.rank_id')
            ->join('role ro', 'ro.id = u.role_id')
            ->where($where)
            ->field(['u.*', 'r.name rank_name', 'ro.name role_name'])
            ->select();
        return json($data);
    }

    public function send($ids = '')
    {
        if (request()->isPost()) {
            $data = input('post.');
            $goodsUsersData = new GoodsUsers();
            $redis = GetRedis::getRedis();
            $goodsData = new Goods();
            $goods_id = $data['goods_id'];
            $goods = (new \app\admin\model\Goods())->where(['id' => $goods_id, 'is_del' => 0])->find();
            if (empty($goods)) return json(['code' => 0, 'msg' => '藏品信息错误']);
            $price = $data['price'];
            if (empty($price)) $price = $goods['price'];
            //生成
            $goods_user_number = $goodsUsersData->where(['goods_id' => $goods_id])->whereNotNull('number')->order('id', 'desc')->value('number');

            if ($goods_user_number) {
                $goods_user_number = str_pad($goods_user_number + 1, 6, '0', STR_PAD_LEFT);
            } else {
                $goods_user_number = '000001';
            }
            $user = [
                'uid' => $ids,
                'goods_id' => $goods_id,
                'price' => $price,
                'number' => $goods_user_number,
                'create_time' => date('Y-m-d H:i:s'),
                'is_send' => 1,
                'jlstatus' => 1
            ];
            Db::startTrans();


            // 上链
            $userInfo = Db::name('users')->where('id', $ids)->find();
            try {

                if ($goods['is_manghe'] == 1) {
                    $redisname_str = "goods_mh_";
                    //start
                    //盲盒id
                    $mhid = Db::name('goods_manghe_users')->max('id');
                    $mhstr = "mh" . str_pad(($mhid + 1), 5, "0", STR_PAD_LEFT);
                    $currentTime = time();
                    $goods_manghe_number = uniqueNum();
                    $goodsManghe['user_id'] = $ids;
                    $goodsManghe['goods_id'] = $goods_id;
                    $goodsManghe['goods_number'] = $goods_manghe_number;
                    $goodsManghe['status'] = 2;
                    $goodsManghe['createtime'] = $currentTime;
                    $goodsManghe['updatetime'] = $currentTime;
                    $goodsManghe['orderNo'] = $mhstr;
                    $goodsMangheUsersData = new GoodsMangheUsers();
                    // $class_id=session_create_id();

                    // //创建nft类别
                    // $mhclasses=CreateChainClasses($users['wallet_address'],$class_id,$uid);
                    // if($mhclasses){
                    //     if(array_key_exists('error', $mhclasses)){
                    //         return Response::fail('抢购人数过多,请重新抢购');
                    //     }
                    //     $goodsManghe['class_id']  = $class_id;
                    // }

                    $goods_manghe_users_id = $goodsMangheUsersData->insertGetId($goodsManghe);
                    if (!$goods_manghe_users_id) {
                        Db::rollback();
                        return json(['code' => 0, 'msg' => '赠送失败']);
                    }
                    //end
                } else {
                    $chain = CreateChainNfts($userInfo, $goods['goods_id'], $goods['goods_id'])['data'];
                    $operation_id = $chain['operation_id'];
                    $contract_address = $chain['contractAddress'];


                    $results = (new \app\admin\model\GoodsUsers())->insertGetId(array_merge($user, [
                        'operation_id' => $operation_id,
                        'contract_address' => $contract_address,
                        'state' => 1
                    ]));
                    $redisname_str = "goods_kc_";
                    if (!$results) {
                        Db::rollback();
                        return json(['code' => 0, 'msg' => '赠送失败']);
                    }
//                Db::name('goods_users')->where('id',$results)->update(['jlstatus'=>1]);
                }
            } catch (\Exception $exception) {
                Db::rollback();
                if (Env::get('app.debug', false)) {
                    throw $exception;
                }
                return Response::fail('上链失败');
            }


            $send = ['uid' => $ids, 'goods_id' => $goods_id, 'price' => $price, 'create_time' => date('Y-m-d H:i:s')];


            $result = (new \app\admin\model\GoodsSend())->insertGetId($send);
            if ($result) {

                // redis 减少库存

//                $goods_kc_count = $redis->rpop($redisname_str . $goods['id']);
//                if (!$goods_kc_count) {
//                    Db::rollback();
//                    return Response::fail('没有库存了');
//                }

                if ($goods['surplus'] > 1) {
                    //库存剩余递减 销量递增
                    $goodsData->where(['id' => $goods['id']])->setDec('surplus', 1);
                    $goodsData->where(['id' => $goods['id']])->setInc('sales', 1);
                } else {
                    Db::rollback();
                    return Response::fail('没有库存了');
                }

                Db::commit();
                return json(['code' => 1, 'msg' => '赠送成功']);
            }
            Db::rollback();
            return json(['code' => 0, 'msg' => '赠送失败']);


        }
        $this->assign('ids', $ids);
        return $this->fetch();
    }

    function GetRandStr($len)
    {
        $chars = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $charsLen = count($chars) - 1;
        shuffle($chars);
        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }


    public function export()
    {
        if ($this->request->isPost()) {
            set_time_limit(0);
            $search = $this->request->post('search');
            $ids = $this->request->post('ids');
            $filter = $this->request->post('filter');
            $op = $this->request->post('op');
            $columns = $this->request->post('columns');


            $excel = new Spreadsheet();
            $excel->getProperties()
                ->setCreator("FastAdmin")
                ->setLastModifiedBy("FastAdmin")
                ->setTitle("标题")
                ->setSubject("Subject");
            $excel->getDefaultStyle()->getFont()->setName('Microsoft Yahei');
            $excel->getDefaultStyle()->getFont()->setSize(12);
            $excel->getDefaultStyle()->applyFromArray(
                array(
                    'fill' => array(
                        'type' => Fill::FILL_SOLID,
                        'color' => array('rgb' => '000000')
                    ),
                    'font' => array(
                        'color' => array('rgb' => "000000"),
                    ),
                    'alignment' => array(
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'indent' => 1
                    ),
                    'borders' => array(
                        'allborders' => array('style' => Border::BORDER_THIN),
                    )
                ));


            $worksheet = $excel->setActiveSheetIndex(0);
            $worksheet->setTitle('标题');
            $whereIds = $ids == 'all' ? '1=1' : ['id' => ['in', explode(',', $ids)]];
            $this->request->get(['search' => $search, 'ids' => $ids, 'filter' => $filter, 'op' => $op]);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $line = 1;
            $list = [];
            $columns_arr = explode(',', $columns);

            unset($columns_arr[4]);
            $columns = implode(",", $columns_arr);
            $columns = "id,nick_name,phone,status,total_direct,group_person_count,group_valid_person_count,cpzs,parent_member,is_auth,name,card,wallet_address,is_bank,is_ali,is_wx,create_time";
//            print_r($worksheet);
//            exit();
//            print_r($where);
//            exit();
            $sql = $this->model
//                ->with(['role'])
                ->field($columns)
                ->where($where)
                ->where($whereIds)
//                ->select();
//            print_r($sql);
//                ->buildsql();
//            var_dump($sql);
                ->chunk(100, function ($items) use (&$list, &$line, &$worksheet) {
                    $styleArray = array(
                        'font' => array(
                            'color' => array('rgb' => '000000'),
                            'size' => 12,
                            'name' => 'Verdana'
                        ));
                    $list = $items = collection($items)->toArray();

                    foreach ($items as $key => $v) {
                        foreach ($v as $k => $ele) {
                            $tmparray = explode("_text", $k);
                            if (count($tmparray) > 1) {
                                $items[$key][$tmparray[0]] = $ele;
                                unset($items[$key][$k]);
                            }
                        }
                    }
                    foreach ($items as $index => $item) {
                        $line++;
                        $col = 0;
                        foreach ($item as $field => $value) {
                            if (is_array($value)) {
                                $value = implode($value);
                            }
                            $worksheet->setCellValueByColumnAndRow($col, $line, $value);
                            $worksheet->getStyleByColumnAndRow($col, $line)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                            $worksheet->getCellByColumnAndRow($col, $line)->getStyle()->applyFromArray($styleArray);
                            $col++;
                        }
                    }
                });

            $first = array_keys($list[0]);
            foreach ($first as $k => $ele) {
                $tmparray = explode("_text", $ele);
                if (count($tmparray) > 1) {
                    unset($first[$k]);
                }
            }

            foreach ($first as $index => $item) {
                $worksheet->setCellValueByColumnAndRow($index, 1, __($item));
            }
            $excel->createSheet();
            // Redirect output to a client’s web browser (Excel2007)
            $title = date("YmdHis");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            $objWriter = IOFactory::createWriter($excel, 'Xlsx');
            $objWriter->save('php://output');
            return;
        }
    }


}
