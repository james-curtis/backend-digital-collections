<?php


namespace app\api\controller;


use logicmodel\UploadLogic;
use think\Request;

class Upload extends BaseController
{
    protected $noNeedLogin = '*';

    private $uploadLogic;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->uploadLogic = new UploadLogic();
    }

    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function fileUpload()
    {
        $image = \request()->file('image');
        return json($this->uploadLogic->apiFileUpload($image));
    }
}