<?php


namespace app\api\controller;


use logicmodel\NotifyLogic;
use think\Request;

class Notify extends BaseController
{
    private $notifyLogic;

    protected $noNeedLogin = '*';

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->notifyLogic = new NotifyLogic();
    }

    /**
     * 支付宝回调
     * @return string
     */
    public function aliNotify()
    {
        return $this->notifyLogic->aliNotify();
    }

    /**
     * 微信回调
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function wxNotify(){
        return $this->notifyLogic->wxNotify();
    }
     public function wxGzhNotify(){
        return $this->notifyLogic->wxGzhNotify();
    }
    public function wxSmallNotify(){
        return json($this->notifyLogic->wxSmallNotify());
    }

}