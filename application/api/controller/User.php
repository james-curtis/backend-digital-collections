<?php


namespace app\api\controller;


use comservice\Response;
use logicmodel\UserLogic;
use logicmodel\WxLogic;
use think\Request;
use validate\AuthValidate;
use think\Db;

class User extends BaseController
{
    protected $noNeedRight = ['*'];
    private $userLogic;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->userLogic = new UserLogic();
    }

    /**
     * 个人信息
     * @return \think\response\Json
     */
    public function userInfo()
    {
        return json($this->userLogic->userInfo($this->userInfo));
    }

    /**
     * 编辑个人信息
     * @param $nick_name
     * @param $head_image
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function editUserInfo($nick_name, $head_image)
    {
        return json($this->userLogic->editUserInfo($this->uid, $nick_name, $head_image));
    }

    /**
     * 切换手机号
     * @param $phone
     * @param $code
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function checkPhone($phone, $code)
    {
        return json($this->userLogic->checkPhone($this->userInfo, $phone, $code));
    }

    /**
     * 邀请分享
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function share()
    {
        return json($this->userLogic->share($this->userInfo));
    }

    /**
     * 修改登录密码
     * @param $password
     * @param $password_re
     * @param $code
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function updatePassword($password, $password_re, $code)
    {
        return json($this->userLogic->updatePassword($this->userInfo, $password, $password_re, $code));
    }

    /**
     * 修改支付密码
     * @param $pay_password
     * @param $pay_password_re
     * @param $code
     * @param $type
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function updatePayPassword($pay_password, $pay_password_re, $code, $type)
    {
        return json($this->userLogic->updatePayPassword($this->userInfo, $pay_password, $pay_password_re, $code, $type));
    }

    /**
     * 团队信息
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function team()
    {
        return json($this->userLogic->team($this->userInfo));
    }

    /**
     * 收款信息
     * @return \think\response\Json
     */
    public function collection()
    {
        return json($this->userLogic->collection($this->userInfo));
    }

    /**
     * 获取充值提现相关信息
     * @return \think\response\Json
     */
    public function getPurchaseConfig()
    {
        return json(Response::success('', [
            'purchase_notify' => config('site.purchase_notify'),
        ]));
    }

    /**
     * 编辑收款信息
     * @param $bank_name
     * @param $bank_number
     * @param $bank_owner
     * @param $bank_branch
     * @param $ali_name
     * @param $ali_image
     * @param $wx_name
     * @param $wx_image
     * @param string $code
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function collectMoney($bank_name, $bank_number, $bank_owner, $bank_branch, $ali_name, $ali_image, $wx_name, $wx_image, $bank_comment, string $code = '')
    {
        return json($this->userLogic->collectMoney($this->userInfo, $bank_name, $bank_number, $bank_owner, $bank_branch, $ali_name, $ali_image, $wx_name, $wx_image, $code, $bank_comment));
    }

    /**
     * 问题反馈
     * @param $images
     * @param $remark
     * @return \think\response\Json
     */
    public function feedback($images, $remark)
    {
        return json($this->userLogic->feedback($this->uid, $images, $remark));
    }

    /**
     * 实名认证
     * @param $name
     * @param $card
     * @param $card_front_image
     * @param $card_back_image
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function auth($name, $card)
    {
        (new AuthValidate())->goCheck();
        return json($this->userLogic->auth($this->userInfo, $name, $card));
    }

    /**
     * 小程序授权
     * @param $code
     * @return \think\response\Json
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function wxSmallAuth($code)
    {
        return json((new WxLogic())->auth($this->userInfo, $code));
    }

//团队人数
    public function tdusers()
    {
        $uid = $this->uid;
        $user = Db::name('users')->where('pid', $uid)->where('is_del', 0)->count();
        //我的团队
        $data['wdtd'] = $user;
        $userrz = Db::name('users')->where('pid', $uid)->where('is_auth', 1)->where('is_del', 0)->count();
        //认证人数
        $data['rzrs'] = $userrz;
        return json($data);
    }

//排行榜
    public function Ranking()
    {
        $page = $this->request->post('page');
        $pagesize = $this->request->post('pagesize');
        $count = Db::name('users')
            ->where('id', 'not in', 1)
            ->where('is_del', 0)
            ->where('is_auth', 1)
            ->count();
        $users = Db::name('users')
            ->where('id', 'not in', 1)
            ->where('is_del', 0)
            ->where('is_auth', 1)
            ->orderRaw('auth_count desc,all_person_count desc')
            ->page($page, $pagesize)
            ->field('id,nick_name,total_direct_auth auth_count,total_direct all_person_count')
            ->select();
        $data = ['count' => $count, 'data' => $users, 'page' => $page, 'pagesize' => $pagesize];
        return json($data);
    }
}