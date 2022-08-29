<?php


namespace logicmodel;


use comservice\Response;

class ConfigLogic
{
    /**
     * 系统配置
     * @return array
     */
    public function config(){
        $users_content = config('site.users_content');
        $conceal_content = config('site.conceal_content');
        $data['users_content'] = $users_content;
        $data['conceal_content'] = $conceal_content;
        $data['buy_content'] = config('site.buy_content');
        $data['about_content'] = config('site.about_content');
        $data['app_download'] = config('site.app_download');
        $data['is_trade'] = config('site.is_trade');
        $data = $this->addWebSiteUrl($data,['users_content','conceal_content','buy_content','about_content']);
        return Response::success('success',$data);
    }
    public function follow(){
     $data['wx_image'] =   config('site.wx_image');
     $data['weibo_image'] =   config('site.weibo_image');
     $data['qq_iamge'] =   config('site.qq_iamge');
     $data['douyi_image'] =   config('site.douyi_image');    
     $data['contact_phone_1'] =   config('site.contact_phone_1');
     $data['contact_phone_2'] =   config('site.contact_phone_2');
     $data['contact_phone_3'] =   config('site.contact_phone_3');      
        return Response::success('success',$data);
    }
    public  function addWebSiteUrl($array, $fields = [])
    {
        $url = 'https://'.$_SERVER['HTTP_HOST'];
        if(count($array) <= 0) return $array;
        if (count($array) == count($array, 1)){
            //一维数组
            if(count($fields) > 0){
                foreach ($fields as $v){
                    $content = str_replace('<img src="/uploads/', '<img src="'.$url.'/uploads/' , $array[$v]);
                    $content = str_replace('<video src="/uploads/', '<video src="'.$url.'/uploads/' , $content);
                    $array[$v] = $content;
                }
            }else{
                foreach ($array as $k=>&$v){
                    $array[$k] = str_replace('<img src="/uploads/', '<img src="'.$url.'/uploads/' , $v);
                }
            }
            return $array;
        }else{
            if(count($array) <= 0) return $array;
            foreach ($array as &$v1) {
                foreach ($fields as &$v2) {
                    if(!empty($v1[$v2])){
                        $v1[$v2] =  str_replace('/uploads/', $url.'/uploads/' , $v1[$v2]);
                    }
                }
            }
            return $array;
        }
    }
}