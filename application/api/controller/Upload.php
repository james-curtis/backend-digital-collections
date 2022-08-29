<?php


namespace app\api\controller;


use logicmodel\UploadLogic;

class Upload
{

    private $uploadLogic;
    public function __construct()
    {
        $this->uploadLogic = new UploadLogic();
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function fileUpload(){
        $image = \request()->file('image');
        return json($this->uploadLogic->apiFileUpload($image));
    }
}