<?php
/**
 * Created by shihe
 * User: Administrator
 * Date: 2019/4/10
 * Time: 14:07
 */

namespace comservice;



class Upload
{
    //单图
    public static function uploadOne($file)
    {
        $filepath = './uploads/base/';
        if (!file_exists($filepath)) {
            mkdir($filepath, 0700, true);
        }
        $validate = ['size'=>config('upload.size'),'ext'=>config('upload.ext')];
        $info= $file->validate($validate)->move($filepath);
        if (!$info) {
            return ['code' => 0, 'msg' => $file->getError()];
        }
        $imgPath = $filepath.$info->getSaveName();
        $imgPath = str_replace('\\','/',$imgPath);
        $imgPath = str_replace('./','/',$imgPath);
        return ['code'=>1,'path'=>$imgPath];
    }
    //base64图片上传
    public static function uploadBase64($base64)
    {

        $rootPath = config('upload.base_path');
        $new_file = $rootPath;
        if (!file_exists($new_file)) {
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($new_file, 0700);
        }
        $new_file = $new_file.time().mt_rand(1000000,9999999) . ".jpg";
        if (file_put_contents($new_file, base64_decode($base64))) {
            $new_file = str_replace('\\','/',$new_file);
            $new_file = str_replace('./','/',$new_file);
            //返回服务器路径
         //   $new_file = 'http://'.$_SERVER['HTTP_HOST'].'/public/'.$new_file;
            return ['errcode'=>0,'msg'=>'新文件保存成功','result'=>$new_file];

        } else {
            return ['errcode'=>1,'msg'=>'新文件保存失败'];
        }
    }
}