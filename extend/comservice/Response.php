<?php
namespace comservice;



class Response
{

    /**
     * 返回成功
     * @param string $msg
     * @param array|null $data
     * @return array
     */
    public static function success(string $msg='success',array $data=null)
    {


        if($data===null)
        {
           return ["code"=>1,"msg"=>$msg];
        }

        return  ["code"=>1,"msg"=>$msg,"data"=>$data];
    }


    /**
     *  返回失败，一般核心业务逻辑出现错误，比如入库失败
     * @param string $msg
     * @return array
     */
    public static function fail(string $msg='操作失败，请稍后重试')
    {
        return ["code"=>0,"msg"=>$msg];

    }

    /**
     * 参数有误
     * @param string $msg
     * @return array
     */
    public static function invalidParam(string $msg='参数错误')
    {
        return ["code"=>0,"msg"=>$msg];
    }
    /**
     * 没有登录，返回错误
     * @param string $msg
     * @return array
     */
    public static function invalidLogin(string $msg='请登录后重试')
    {
        return  ["code"=>401,"msg"=>$msg];
    }
    /**
     * 没有登录，返回错误
     * @param string $msg
     * @return array
     */
    public static function invalidPassword(string $msg='请先设置支付密码')
    {
        return  ["code"=>402,"msg"=>$msg];
    }



}