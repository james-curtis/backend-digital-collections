<?php

namespace app\api\controller;

use CommonChain\CommonChain;
use think\Controller;

class Debug extends Controller
{
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
}