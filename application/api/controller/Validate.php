<?php

namespace app\api\controller;

use AliyunAfsCore\DefaultAcsClient;
use AliyunAfsCore\Profile\DefaultProfile;
use AliyunAfsCore\Regions\EndpointProvider;
use app\admin\command\Api;
use comservice\RedisCache;
use think\Cache;
use think\Controller;
use afs\Request\V20180112 as Afs;
use AliyunAfsCore\Index;
use think\Session;

class Validate extends BaseController
{
    public static $RequestIdConstant = 'RequestId';

    protected $noNeedLogin = '*';

    public function valid(): \think\response\Json
    {
        //YOUR ACCESS_KEY、YOUR ACCESS_SECRET请替换成您的阿里云accesskey id和secret
        $iClientProfile = DefaultProfile::getProfile("cn-hangzhou", config('site.aliyun_access_key'), config('site.aliyun_access_secret'));
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", "afs", "afs.aliyuncs.com");
        $client = new DefaultAcsClient($iClientProfile);

        $request = new Afs\AuthenticateSigRequest();
        $request->setSessionId(input('sessionId'));// 必填参数，从前端获取，不可更改，android和ios只传这个参数即可
        $request->setToken(input('token'));// 必填参数，从前端获取，不可更改
        $request->setSig(input('sig'));// 必填参数，从前端获取，不可更改
        $request->setScene(input('scene'));// 必填参数，从前端获取，不可更改
        $request->setAppKey(config('site.yundun_app_key'));//必填参数，后端填写
        $request->setRemoteIp(request()->ip());//必填参数，后端填写
        $request->setRegionId("cn-hangzhou");
        $request->setProduct("afs");

        $response = $client->getAcsResponse($request);//返回code 100表示验签通过，900表示验签失败

        if ($response->Code === 100) {
            // 验证成功
            Cache::set($response->RequestId, 1, 3600);
            Session::set(self::$RequestIdConstant, $response->RequestId);
        }
        return json($response);
    }
}