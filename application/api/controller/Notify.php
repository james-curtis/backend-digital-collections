<?php


namespace app\api\controller;


use logicmodel\NotifyLogic;

class Notify
{
    private $notifyLogic;
    public function __construct()
    {
        $this->notifyLogic = new NotifyLogic();
    }

    /**
     * 支付宝回调
     * @return string
     */
    public function aliNotify(){
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