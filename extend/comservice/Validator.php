<?php

namespace comservice;
class Validator{


        //邮箱验证
        public static function isEmail($email){
         
        $reg = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        //匹配成功返回的次数
         return preg_match($reg,$email);
         }

         
        //身份证验证 
    public static function is_idcard( $id )
    {
        $id = strtoupper($id);
        $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        $arr_split = array();
        if(!preg_match($regx, $id))
        {
            return FALSE;
        }
        if(15==strlen($id)) //检查15位
        {
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";

            @preg_match($regx, $id, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19".$arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
            date_default_timezone_set('Asia/Shanghai');
            if(!strtotime($dtm_birth))
            {
                return FALSE;
            } else {
                return TRUE;
            }
        }
        else      //检查18位
        {
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
            @preg_match($regx, $id, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3]. '/' .$arr_split[4];
            if(!strtotime($dtm_birth)) //检查生日日期是否正确
            {
                return FALSE;
            }
            else
            {
                //检验18位身份证的校验码是否正确。
                //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
                $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
                $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
                $sign = 0;
                for ( $i = 0; $i < 17; $i++ )
                {
                    $b = (int) $id{$i};
                    $w = $arr_int[$i];
                    $sign += $b * $w;
                }
                $n = $sign % 11;
                $val_num = $arr_ch[$n];
                if ($val_num != substr($id,17, 1))
                {
                    return FALSE;
                } 
                else
                {
                    return TRUE;
                }
            }
        }

    }


    //手机号验证

    public static function isPhone($phone){

        $reg="/^1[34578]\d{9}$/";
        //返回匹配到的次数
        return preg_match($reg,$phone);

     }
     //url验证
    public static function isUrl($url){
        // 正则
        $reg="/^((ht|f)tps?):\/\/[\w\-]+(\.[\w\-]+)+([\w\-\.,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?$/";
        //返回匹配到的次数
        return preg_match($reg,$url);
     }
     //ip的验证
    public static  function isIp($ip){
         $reg="/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/";
        return preg_match($reg,$ip);
     }

         //时间验证 
    public static function isTime($datatime){
        //时间格式 $datatime="2010-6-4 00:00:00"
        $reg="/^(?:19|20)[0-9][0-9]-(?:(?:0[1-9])|(?:1[0-2]))-(?:(?:[0-2][1-9])|(?:[1-3][0-1])) (?:(?:[0-2][0-3])|(?:[0-1][0-9])):[0-5][0-9]:[0-5][0-9]$/";
        //返回匹配到的次数
        return preg_match($reg,$datatime);
     }
         //银行卡验证
   public static function isBankCard($card){
       //转化成字符串
        $arr_card = str_split($card);
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
    //验证中文
    public static function isChinese($sInBuf)
    {
        if (preg_match("/^[\x7f-\xff]+$/", $sInBuf)) {
            return false;
        }else{
            return true;
        }    
	}


}

