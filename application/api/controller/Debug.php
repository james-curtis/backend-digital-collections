<?php

namespace app\api\controller;

use CommonChain\CommonChain;
use think\Controller;

class Debug extends BaseController
{
    protected $noNeedLogin = '*';

    public function reg()
    {
        $name = '15922221111';
        $chain = new \CommonChain\CommonChain();
        $res = $chain->register($name, md5($name));
        var_dump($res);
        die;
    }

    public function __call($name, $arguments)
    {
        $instance = new CommonChain();
        $instance->$name($arguments);
    }

    public function debug()
    {
        var_dump(intval(preg_replace("/^0+/", "", '9063950072802925')));
    }
}