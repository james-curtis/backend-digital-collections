<?php

namespace app\api\controller;

use think\Controller;
use TiChain\TiChain;

class Debug extends Controller
{
    public function __call($name, $arguments)
    {
        $instance = new TiChain();
        $instance->$name();
    }
}