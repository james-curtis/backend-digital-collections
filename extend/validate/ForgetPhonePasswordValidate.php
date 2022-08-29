<?php


namespace validate;


class ForgetPhonePasswordValidate extends BaseValidate
{

    protected $rule = [
        'phone'=>'require',
        'password'=>'require',
        'password_re'=>'require|confirm:password'
    ];
    protected $message = [
        'phone.require'=>'手机号不能为空',
        'phone.isPhone'=>'手机号格式错误',
        'password'=>'登录密码不能为空',
        'password_re'=>'两次密码输入不一致'
    ];
}