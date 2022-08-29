<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/10 0010
 * Time: 18:06
 */

namespace logicmodel;


use comservice\GetRedis;
use comservice\Response;
use datamodel\Users;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Sms\V20210111\Models\SendSmsRequest;
use TencentCloud\Sms\V20210111\SmsClient;


class SendLogic
{
    private $redis;
    private $clientLogic;
    private $url;

    public function __construct()
    {
        $this->redis = GetRedis::getRedis();
        $this->clientLogic = new ClientLogic();
        $this->url = "http://v.juhe.cn/sms/send";
        $this->clientLogic = new ClientLogic();
    }

    /**
     * 发送短信
     * @param $phone
     * @param $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sendPhone($phone, $type)
    {
        if (strlen($phone) != 11) return Response::fail('手机号格式错误');
        $info = (new Users())->where(['is_del' => 0, 'phone' => $phone])->find();
        if ($type == 1) {
            if ($info) return Response::fail('手机号已注册');
        } elseif ($type == 2) {
            if (empty($info)) return Response::fail('手机号未注册');
        } elseif ($type == 3) {
            //快捷登录
        } else {
            return Response::fail('短信类型错误');
        }
        $phone_code = $this->redis->getItem($phone . '-' . $type);
        if ($phone_code) {
            return Response::fail('频繁发送');
        }
        $code = rand(1111, 9999);
        $result = $this->send2($phone, $code);
        if ($result) {
            $this->redis->setItem($phone . '-' . $type, $code);
            $this->redis->settime($phone . '-' . $type, 45);
            return Response::success('发送成功');
        }
        return Response::fail('发送失败:' . $result);
    }

    public function send2($phone, $code)
    {
        $res = file_get_contents("http://utf8.api.smschinese.cn/?Uid=比太牛牛&Key=d8sa9dua9sd8ajd9ajda&smsMob={$phone}&smsText=您的验证码是:{$code}");
        return $res;
    }

    public function send($phone, $code)
    {
        try {
            $cred = new Credential("AKIDRmyzpa8UdZHlsdfdsfsd13rTs30kFXabPxFH4bst", "oMdePvBBvQsdfsdfsdfecKmyua0B6lCOOBk4A2g6I");
            // 实例化一个http选项，可选的，没有特殊需求可以跳过
            $httpProfile = new HttpProfile();
            $httpProfile->setReqMethod("GET");  // post请求(默认为post请求)
            $httpProfile->setReqTimeout(30);    // 请求超时时间，单位为秒(默认60秒)
            $httpProfile->setEndpoint("sms.tencentcloudapi.com");  // 指定接入地域域名(默认就近接入)

            // 实例化一个client选项，可选的，没有特殊需求可以跳过
            $clientProfile = new ClientProfile();
            $clientProfile->setSignMethod("TC3-HMAC-SHA256");  // 指定签名算法(默认为HmacSHA256)
            $clientProfile->setHttpProfile($httpProfile);

            // 实例化要请求产品(以sms为例)的client对象,clientProfile是可选的
            // 第二个参数是地域信息，可以直接填写字符串 ap-guangzhou，或者引用预设的常量
            $client = new SmsClient($cred, "ap-guangzhou", $clientProfile);

            // 实例化一个 sms 发送短信请求对象,每个接口都会对应一个request对象。
            $req = new SendSmsRequest();

            /* 短信应用ID: 短信SdkAppId在 [短信控制台] 添加应用后生成的实际SdkAppId，示例如1400006666 */
            $req->SmsSdkAppId = "1400605355";
            /* 短信签名内容: 使用 UTF-8 编码，必须填写已审核通过的签名，签名信息可登录 [短信控制台] 查看 */
            $req->SignName = "元素象限";
            /* 短信码号扩展号: 默认未开通，如需开通请联系 [sms helper] */
            $req->ExtendCode = "";
            /* 下发手机号码，采用 E.164 标准，+[国家或地区码][手机号]
             * 示例如：+8613711112222， 其中前面有一个+号 ，86为国家码，13711112222为手机号，最多不要超过200个手机号*/
            $req->PhoneNumberSet = ["+86" . $phone];
            /* 国际/港澳台短信 SenderId: 国内短信填空，默认未开通，如需开通请联系 [sms helper] */
            $req->SenderId = "";
            /* 用户的 session 内容: 可以携带用户侧 ID 等上下文信息，server 会原样返回 */
            $req->SessionContext = "xxx";
            /* 模板 ID: 必须填写已审核通过的模板 ID。模板ID可登录 [短信控制台] 查看 */
            $req->TemplateId = "1223475";
            /* 模板参数: 若无模板参数，则设置为空*/
            $req->TemplateParamSet = array($code);
            // 通过client对象调用SendSms方法发起请求。注意请求方法名与请求对象是对应的
            // 返回的resp是一个SendSmsResponse类的实例，与请求对象对应
            $resp = $client->SendSms($req);
            // 输出json格式的字符串回包
            $result = $resp->toJsonString();
            json_decode($result, true);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function transfer($phone)
    {
        try {
            $cred = new Credential("AKIDRmyzpa8UdZHlsdfdsfsd13rTs30kFXabPxFH4bst", "oMdePvBBvQsdfsdfsdfecKmyua0B6lCOOBk4A2g6I");
            // 实例化一个http选项，可选的，没有特殊需求可以跳过
            $httpProfile = new HttpProfile();
            $httpProfile->setReqMethod("GET");  // post请求(默认为post请求)
            $httpProfile->setReqTimeout(30);    // 请求超时时间，单位为秒(默认60秒)
            $httpProfile->setEndpoint("sms.tencentcloudapi.com");  // 指定接入地域域名(默认就近接入)

            // 实例化一个client选项，可选的，没有特殊需求可以跳过
            $clientProfile = new ClientProfile();
            $clientProfile->setSignMethod("TC3-HMAC-SHA256");  // 指定签名算法(默认为HmacSHA256)
            $clientProfile->setHttpProfile($httpProfile);

            // 实例化要请求产品(以sms为例)的client对象,clientProfile是可选的
            // 第二个参数是地域信息，可以直接填写字符串 ap-guangzhou，或者引用预设的常量
            $client = new SmsClient($cred, "ap-guangzhou", $clientProfile);

            // 实例化一个 sms 发送短信请求对象,每个接口都会对应一个request对象。
            $req = new SendSmsRequest();

            /* 短信应用ID: 短信SdkAppId在 [短信控制台] 添加应用后生成的实际SdkAppId，示例如1400006666 */
            $req->SmsSdkAppId = "1400605355";
            /* 短信签名内容: 使用 UTF-8 编码，必须填写已审核通过的签名，签名信息可登录 [短信控制台] 查看 */
            $req->SignName = "元素象限";
            /* 短信码号扩展号: 默认未开通，如需开通请联系 [sms helper] */
            $req->ExtendCode = "";
            /* 下发手机号码，采用 E.164 标准，+[国家或地区码][手机号]
             * 示例如：+8613711112222， 其中前面有一个+号 ，86为国家码，13711112222为手机号，最多不要超过200个手机号*/
            $req->PhoneNumberSet = ["+86" . $phone];
            /* 国际/港澳台短信 SenderId: 国内短信填空，默认未开通，如需开通请联系 [sms helper] */
            $req->SenderId = "";
            /* 用户的 session 内容: 可以携带用户侧 ID 等上下文信息，server 会原样返回 */
            $req->SessionContext = "xxx";
            /* 模板 ID: 必须填写已审核通过的模板 ID。模板ID可登录 [短信控制台] 查看 */
            $req->TemplateId = "1257075";
            /* 模板参数: 若无模板参数，则设置为空*/
            $req->TemplateParamSet = array();
            // 通过client对象调用SendSms方法发起请求。注意请求方法名与请求对象是对应的
            // 返回的resp是一个SendSmsResponse类的实例，与请求对象对应
            $resp = $client->SendSms($req);
            // 输出json格式的字符串回包
            $result = $resp->toJsonString();
            json_decode($result, true);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}