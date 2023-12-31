<?php

namespace epayment;

require_once EXTEND_PATH . './epayment/func.php';

class EpaySubmit
{

    protected $alipay_config;
    /**
     * @var string
     */
    private $alipay_gateway_new;
    /**
     * @var string
     */
    private $api_url;
    /**
     * @var string
     */
    private $mapi_url;

    function __construct($alipay_config)
    {
        $this->alipay_config = $alipay_config;
        $this->alipay_gateway_new = $this->alipay_config['apiurl'] . 'submit.php?';
        $this->mapi_url = $this->alipay_config['apiurl'] . 'mapi.php';
        $this->api_url = $this->alipay_config['apiurl'] . 'api.php';
    }

    function AlipaySubmit($alipay_config)
    {
        $this->__construct($alipay_config);
    }

    /**
     * 生成签名结果
     * @param array $para_sort 已排序要签名的数组
     * @return string 签名结果字符串
     */
    function buildRequestMysign($para_sort)
    {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = createLinkstring($para_sort);
//        var_dump($prestr);

        $mysign = md5Sign($prestr, $this->alipay_config['key']);

        return $mysign;
    }

    /**
     * 生成要请求给支付宝的参数数组
     * @param array $para_temp 请求前的参数数组
     * @return array 要请求的参数数组
     */
    function buildRequestParam($para_temp)
    {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = paraFilter($para_temp);

        //对待签名参数数组排序
        $para_sort = argSort($para_filter);

        //生成签名结果
        $mysign = $this->buildRequestMysign($para_sort);

        //签名结果与签名方式加入请求提交参数组中
        $para_sort['sign'] = $mysign;
        $para_sort['sign_type'] = strtoupper(trim($this->alipay_config['sign_type']));
//        var_dump($para_sort);

        return $para_sort;
    }

    /**
     * 生成要请求给支付宝的参数数组
     * @param array $para_temp 请求前的参数数组
     * @return string 要请求的参数数组字符串
     */
    function buildRequestParaToString($para_temp)
    {
        //待请求参数数组
        $para = $this->buildRequestParam($para_temp);

        //把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
        $request_data = createLinkstringUrlencode($para);

        return $request_data;
    }

    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param array $para_temp 请求参数数组
     * @param string $method 提交方式。两个值可选：post、get
     * @param string $button_name 确认按钮显示文字
     * @return string 提交表单HTML文本
     */
    function buildRequestForm($para_temp, $method = 'get', $button_name = '')
    {
        //待请求参数数组
        $para = $this->buildRequestParam($para_temp);

        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='" . $this->alipay_gateway_new . "_input_charset=" . trim(strtolower($this->alipay_config['input_charset'])) . "' method='" . $method . "'>";
        foreach ($para as $key => $val) {
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }
//		while (list ($key, $val) = each ($para)) {
//            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
//        }

        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml . "<input type='submit' value='" . $button_name . "'></form>";

        $sHtml = $sHtml . "<script>document.forms['alipaysubmit'].submit();</script>";

        return $sHtml;
    }

    // 发起支付（获取链接）
    public function getPayLink($param_tmp)
    {
        $param = $this->buildRequestParam($param_tmp);
        $url = $this->alipay_gateway_new . http_build_query($param);
        return $url;
    }

    // 发起支付（API接口）
    public function apiPay($param_tmp)
    {
        $param = $this->buildRequestParam($param_tmp);
        $response = $this->getHttpResponse($this->mapi_url, http_build_query($param));
        $arr = json_decode($response, true);
        return $arr;
    }

    // 请求外部资源
    private function getHttpResponse($url, $post = false, $timeout = 10)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept: */*";
        $httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
        $httpheader[] = "Connection: close";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}