<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/3 0003
 * Time: 17:25
 */

namespace app\lib\exception;


class ParamException extends BaseException
{
    public $code = 0;
    public $msg  = '参数错误';
}