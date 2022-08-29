<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/5 0005
 * Time: 15:45
 */

namespace app\lib\exception;


class locationException extends BaseException
{
    public $code = 402;
    public $msg  = '请先当前位置';
}