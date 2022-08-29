<?php


namespace validate;


class UpdatePayPasswordValidate extends BaseValidate
{

    protected $rule = [
        'password'=>'require',
        'password_re'=>'require|confirm:password',
        'code'=>'require',
    ];

    public $message = [
        'password'=>'支付密码不能为空',
        'password_re'=>'两次密码输入不一样',
        'code'=>'验证码不能为空'
    ];

}