<?php


namespace validate;



class RegisterValidate extends BaseValidate
{

    protected $rule = [
        'phone'=>'require|isPhone',
        'code'=>'require',
        'password'=>'require',
        'password_re'=>'require|confirm:password',
    ];
    protected $message = [
        'phone.require'=>'手机号不能为空',
        'phone.isPhone'=>'手机号格式不正确',
        'code'=>'验证码不能为空',
        'password'=>'登录密码不能为空',
        'password_re'=>'登录密码不一致',
    ];

}