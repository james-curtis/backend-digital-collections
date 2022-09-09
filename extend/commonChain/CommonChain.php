<?php

namespace CommonChain;

use TiChain\TiChain;

class CommonChain
{
    private $chainInstance;

    public function __construct()
    {
        $this->chainInstance = new TiChain([
            'appId' => config('site.tichain_appid'),
            'appKey' => config('site.tichain_appkey'),
            'getaway' => config('site.tichain_getaway'),
        ]);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->chainInstance, $name], $arguments);
    }
}