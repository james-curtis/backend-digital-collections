<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/24 0024
 * Time: 15:58
 */

namespace logicmodel;


use comservice\Response;
use comservice\Upload;

class UploadLogic
{
    /**
     * 单图file文件上传
     * @param $image
     * @return array
     */
    public function apiFileUpload($image)
    {
        if (empty($image)) return  Response::fail('请选择要上传的图片');
        $result = Upload::uploadOne($image);
        if ($result['code'] == 1){
            $url = 'http://'.$_SERVER['HTTP_HOST'];
            $url = str_replace('/uploads/', $url.'/uploads/' , $result['path']);
            return Response::success('success',['path'=>$url]);
        }else{
            return Response::fail($result['msg']);
        }
    }

}