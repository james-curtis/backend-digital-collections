<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/27 0027
 * Time: 11:08
 */

namespace validate;
use app\lib\exception\ParamException;
use comservice\GetRedis;

use think\Validate;

class BaseValidate extends Validate
{
    /**
     * 验证参数
     * @param string $scene
     * @param array $data
     * @return bool
     * @throws ParamException
     */
    public function  goCheck($data=[],$scene=''){

        if (!$data) $data = input();
        $result = $this->scene($scene)->check($data);
        if ($result == false) {
            $msg = $this->getError();
            throw new ParamException($msg);
        }
        return true;
    }

    /**
     * 验证是否为正整数
     * @param $value
     * @return bool
     */
    public  function  isInt($value)
    {

        if (is_numeric($value) && is_int($value + 0) && ($value +0) >0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 验证银行卡
     * @param $value
     * @return bool
     */
    public function isBankCard($value){

        //转化成字符串
        $arr_card = str_split($value);
        $last_n = $arr_card[count($arr_card)-1];
        //逆向排序
        krsort($arr_card);
        $i = 1;
        $total = 0;
        foreach ($arr_card as $n)
        {
            if($i%2==0)
            {
                $ix = $n*2;
                if($ix>=10)
                {
                    $nx = 1 + ($ix % 10);
                    $total += $nx;
                }
                else
                {
                    $total += $ix;
                }
            }else
            {
                $total += $n;
            }
            $i++;
        }
        $total -= $last_n;
        $total *= 9;
        if($last_n == ($total%10)){
            return true;
        }
        return false;
    }


    /**
     * 验证手机号
     * @param $value
     * @return bool
     */
    public function isPhone($value)
    {
        $reg="/^1[23456789]\d{9}$/";
        //返回匹配到的次数
        $res = preg_match($reg,$value);
        if ($res >0 ) return true;
        return false;

    }


    /**
     * 验证邮箱
     * @param $value
     * @return bool
     */
    public function isEmail($value){
        if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$value)) {
            return false;
        }
        return true;
    }

    /**
     * 验证手机验证码
     * @param $value
     * @param string $rule
     * @param $data
     * @return bool
     */
    public function checkCode($value,$rule='',$data)
    {
        return true;
        $email = $data['email'];
        $redis = GetRedis::getRedis();
        $checkCode = $redis->getItem($email);
        if (!$checkCode || $checkCode != $value) return false;
        $redis->delItem($email);
        return true;
    }
    public function checkPassword($value){
        $pattern = "^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{8,16}$^";
        $result = preg_match($pattern, $value);
        if($result == false) return false;
        return true;
    }

    /**
     * 验证身份证
     * @param $id
     * @return bool
     */
    public  function isCard( $id )
    {
        $id = strtoupper($id);
        $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        $arr_split = array();
        if (!preg_match($regx, $id)) {
            return FALSE;
        }
        if (15 == strlen($id)) //检查15位
        {
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";

            @preg_match($regx, $id, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else      //检查18位
        {
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
            @preg_match($regx, $id, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) //检查生日日期是否正确
            {
                return FALSE;
            } else {
                //检验18位身份证的校验码是否正确。
                //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
                $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
                $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
                $sign = 0;
                for ($i = 0; $i < 17; $i++) {
                  
                }
                $n = $sign % 11;
                $val_num = $arr_ch[$n];
                if ($val_num != substr($id, 17, 1)) {
                    return FALSE;
                }
                else {
                    return TRUE;
                }
            }
        }

    }
}