<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/3 0003
 * Time: 17:06
 */

namespace app\lib\exception;


use think\Exception;


class BaseException extends Exception
{
    public $code = 0;
    public $msg  = '参数错误';

    public function __construct($msg)
    {
        parent::__construct();
        $this->msg = $msg;
    }
}