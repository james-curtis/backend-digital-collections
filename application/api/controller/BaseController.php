<?php


namespace app\api\controller;


use app\common\controller\Api;
use app\common\library\Auth;
use app\lib\exception\LoginException;
use app\lib\exception\PhoneException;
use comservice\GetRedis;
use datamodel\Users;
use think\Cache;
use think\Config;
use think\Controller;
use think\Cookie;
use think\Hook;
use think\Loader;
use think\Request;

class BaseController extends Api
{
    protected $uid;
    protected $userInfo;

//    public function __construct(Request $request = null)
//    {
//
//        $this->checkLogin();
//        parent::__construct($request);
//    }

//    public function checkLogin()
//    {
//        if (strtolower(\request()->controller()) == 'index' and
//            strtolower(\request()->action()) == strtolower('siteConfig'))
//            return true;
//        $token = Request::instance()->header('token');
//        if (Cookie::has('app_token'))
//            $token = cookie('app_token');
//        if (input('?token'))
//            $token = input('token');
//        if (empty($token) || $token == '') {
//            throw  new LoginException('用户身份失效,请先登录');
//            // $uid = 1;
//        } else {
//            $redis = GetRedis::getRedis();
//            $uid = $redis->getItem($token);
//            if (empty($uid) || $uid == false || !$uid) {
//                throw  new LoginException('用户身份失效,请先登录');
//            }
//        }
//        $field = ['u.*', 'r.name rank_name'];
//        $userInfo = (new Users())->alias('u')
//            ->join('rank r', 'r.id = u.rank_id')
//            ->where(['u.is_del' => 0, 'u.id' => $uid])
//            ->field($field)
//            ->find();
//        if (empty($userInfo)) throw  new LoginException('用户身份信息错误');
//        if (empty($userInfo['phone'])) throw  new PhoneException('请先绑定手机号');
//        if ($userInfo['status'] == 0) throw  new LoginException('账户已冻结');
//        // if($userInfo['app_token'] != $token)  throw  new LoginException('当前账号已在其他设备登录,您已强制下线!');
//        $this->uid = $uid;
//        $this->userInfo = $userInfo->toArray();
//    }

    protected function awscValid()
    {
        if (!session(Validate::$RequestIdConstant) and !Cache::get(session(Validate::$RequestIdConstant))) {
            $this->error('无权限', null, 601);
        }
    }

    protected function _initialize()
    {
        //跨域请求检测
        check_cors_request();

        // 检测IP是否允许
        check_ip_allowed();

        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');

        $this->auth = Auth::instance();

        $modulename = $this->request->module();
        $controllername = Loader::parseName($this->request->controller());
        $actionname = strtolower($this->request->action());

        // token
        $token = Request::instance()->header('token');
        if (Cookie::has('app_token'))
            $token = cookie('app_token');
        if (input('?token'))
            $token = input('token');

        $path = str_replace('.', '/', $controllername) . '/' . $actionname;
        // 设置当前请求的URI
        $this->auth->setRequestUri($path);
        // 检测是否需要验证登录
        if (!$this->auth->match($this->noNeedLogin)) {
            // 滑动验证
            $this->awscValid();

            //检测是否登录
            if (empty($token) || $token == '') {
                throw  new LoginException('用户身份失效,请先登录');
            } else {
                $redis = GetRedis::getRedis();
                $uid = $redis->getItem($token);
                if (empty($uid) || $uid == false || !$uid) {
                    throw  new LoginException('用户身份失效,请先登录');
                }
            }
            $field = ['u.*', 'r.name rank_name'];
            $userInfo = (new Users())->alias('u')
                ->join('rank r', 'r.id = u.rank_id')
                ->where(['u.is_del' => 0, 'u.id' => $uid])
                ->field($field)
                ->find();
            if (empty($userInfo)) throw  new LoginException('用户身份信息错误');
            if (empty($userInfo['phone'])) throw  new PhoneException('请先绑定手机号');
            if ($userInfo['status'] == 0) throw  new LoginException('账户已冻结');
            $this->uid = $uid;
            $this->userInfo = $userInfo->toArray();

            // 判断是否需要验证权限
            if (!$this->auth->match($this->noNeedRight)) {
                // 判断控制器和方法判断是否有对应权限
                if (!$this->auth->check($path)) {
                    $this->error(__('You have no permission'), null, 403);
                }
            }
        }

        $upload = \app\common\model\Config::upload();

        // 上传信息配置后
        Hook::listen("upload_config_init", $upload);

        Config::set('upload', array_merge(Config::get('upload'), $upload));

        // 加载当前控制器语言包
        $this->loadlang($controllername);
    }


}