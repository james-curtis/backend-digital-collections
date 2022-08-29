<?php


namespace validate;


class ForgetEmailPasswordValidate extends BaseValidate
{

    protected $rule = [
        'email'=>'require|isEmail',
        'code'=>'require',
        'password'=>'require|checkPassword',
        'password_re'=>'require|confirm:password'
    ];
    protected $message = [
        'email.require'=>'邮箱不能为空',
        'email.isEmail'=>'邮箱格式错误',
        'code.require'=>"验证码不能为空",
        'password.require'=>'登录密码不能为空',
        'password.checkPassword'=>'密码格式错误',
        'password_re'=>'两次密码输入不一致'
    ];
}