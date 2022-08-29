<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/3 0003
 * Time: 17:25
 */

namespace app\lib\exception;


class PhoneException extends BaseException
{
    public $code = 403;
    public $msg  = '绑定微信';


}