<?php


namespace validate;


class AuthValidate extends BaseValidate
{
    protected $rule = [
        'name'=>'require',
        'card'=>'require|isCard',
        // 'card_front_image'=>'require',
        // 'card_back_image'=>'require',
    ];

    protected $message = [
        'name'=>'真实姓名不能为空',
        'card.require'=>'身份证号不能为空',
        'card.isCard'=>'身份证格式错误',
        // 'card_front_image'=>'身份证正面照片不能为空',
        // 'card_back_image'=>'身份证反面照片不能为空',
    ];

}