<?php

// 公共助手函数

use Symfony\Component\VarExporter\VarExporter;
use think\Db;

// require_once('../xasset/index.php');


//创建链账户地址
function CreateChainAccount($config = [], $name)
{
    if (!config('?site.tichain_appid')) {

        $body = [
            "name" => $name,
            "operation_id" => "operationid" . uniqueNum(),
        ];

        $res = requests("/v1beta1/account", [], $body, "POST", $config);
        return $res;
    }
    $chain = new \commonChain\CommonChain();
    $res = $chain->register($name, md5($name));
    if (intval($res['code']) != 0) {
        return [
            'error' => $res['message'],
        ];
    }
    return [
        'data' => [
            'account' => $res['data']['pubKey']['address']
        ]
    ];
}


//创建 NFT 类别
// 用户账号上链
function CreateChainClasses($account, $goods_id, $userid, $users)
{
    if (!config('?site.tichain_appid')) {
        $body = [
            "name" => "{$userid}",
            "class_id" => 'nft' . $goods_id,
            "owner" => $account,
            "operation_id" => "operationid" . uniqueNum(),
        ];
        $res = requests("/v1beta1/nft/classes", [], $body, "POST", []);
        return $res;
    }
    return CreateChainAccount([], $users['wallet_address']);
}


//发行 NFT
function CreateChainNfts($user, $goods_id, $url)
{
    if (!config('?site.tichain_appid')) {
        $body = [
            "name" => "{$user['class_id']}",
            "class_id" => 'nft' . $goods_id,
            "uri" => $url,
            "operation_id" => "operationid" . uniqueNum(),
        ];

        $res = requests("/v1beta1/nft/nfts/nft" . $goods_id, [], $body, "POST", []);
        //operationid1654506994
        return $res;
    }
    $name = $user['wallet_private_key'];
    $chain = new \commonChain\CommonChain();
    $config = [
        'name' => 'nft' . $goods_id,
        'pieceCount' => 1,
        'feature' => $url,
        'productIds' => [$goods_id],
    ];
    $res = $chain->publish($name, md5($name), $config);
    if (intval($res['code']) != 0) {
        return [
            'error' => $res['message'],
        ];
    }
    return [
        'data' => [
            // 交易哈希
            'operation_id' => $res['data']['products'][0]['transactionHash'],
            // 产品地址
            'address' => $res['data']['products'][0]['address'],
            // 合约地址
            'contractAddress' => $res['data']['contractAddress'],
        ]
    ];
}

//批量发行 NFT
function PlNfts($userid, $class_id, $url, $recipient)
{

    $body = [
        "name" => "{$userid}",
        "class_id" => 'nft' . $class_id,
        "uri" => $url,
        "operation_id" => "operationid" . uniqueNum(),
        'recipients' => $recipient,
    ];


    // exit();
    // https://apis.avata.bianjie.ai/v1beta1/nft/batch/nfts/{class_id}
    $res = requests("/v1beta1/nft/batch/nfts/" . $class_id, [], $body, "POST", []);
    //operationid1654506994
    return $res;
}

//上链查询
/**
 * @param $operation_id string 交易哈希
 * @param $phone
 * @param $transactionHash
 * @param $methodName
 * @return array|bool|float|int|mixed|stdClass|string|要转换的数据|null
 */
function CreateChainTx($operation_id, $phone = '', $methodName = 'transferFrom')
{
    if (!config('?site.tichain_appid')) {
        $res = requests("/v1beta1/tx/" . $operation_id, [], [], "GET", []);
        return $res;
    }
    $chain = new \commonChain\CommonChain();
    $config = [
        'transactionHash' => $operation_id,
        'methodName' => $methodName,
    ];
    $res = $chain->detail($phone, md5($phone), $config);
    if (intval($res['code']) != 0) {
        return [
            'error' => $res['message'],
        ];
    }
    return [
        'data' => $res['data']
    ];
}

//转让
function Nfttransfers($nft_id, $owner, $class_id, $account, $ownerInfo = null)
{
    if (!config('?site.tichain_appid')) {
        $body = [
            "recipient" => $account,
            "operation_id" => "operationid" . uniqueNum(),
        ];
        $res = requests("/v1beta1/nft/nft-transfers/" . $class_id . "/" . $owner . "/" . $nft_id, [], $body, "POST", []);
        return $res;
    }
    $chain = new \commonChain\CommonChain();
    $config = [
        'contractAddress' => $nft_id,
        'tokenId' => $class_id,
        'from' => $owner,
        'to' => $account,
    ];
    $res = $chain->transfer($ownerInfo['wallet_private_key'], md5($ownerInfo['wallet_private_key']), $config);
    if (intval($res['code']) != 0) {
        return [
            'error' => $res['message'],
        ];
    }
    return [
        'data' => $res['data']
    ];
}

function requests($path, $query = [], $body = [], $method = 'GET', $config = [])
{
    if ($config) {
        $apiKey = $config['apiKey'];
        $apiSecret = $config['apiSecret'];//
        $domain = $config['url'];//t
    } else {
        $config = Db::name('config')->where('group', 'wenchangchain')->select();
        $apiKey = $config[0]['value'];
        $apiSecret = $config[1]['value'];//
        $domain = $config[2]['value'];//test
    }

    $method = strtoupper($method);
    $apiGateway = rtrim($domain, '/') . '/' . ltrim($path,
            '/') . ($query ? '?' . http_build_query($query) : '');
    $timestamp = uniqueNum();
    $params = ["path_url" => $path];
    if ($query) {
        foreach ($query as $k => $v) {
            $params["query_{$k}"] = $v;
        }
    }
    if ($body) {
        foreach ($body as $k => $v) {
            $params["body_{$k}"] = $v;
        }
    }
    ksort($params);
    $hexHash = hash("sha256", "{$timestamp}" . $apiSecret);
    if (count($params) > 0) {
        $s = json_encode($params, JSON_UNESCAPED_UNICODE);
        $hexHash = hash("sha256", stripcslashes($s . "{$timestamp}" . $apiSecret));
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiGateway);
    $header = [
        "Content-Type:application/json",
        "X-Api-Key:{$apiKey}",
        "X-Signature:{$hexHash}",
        "X-Timestamp:{$timestamp}",
    ];
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    $jsonStr = $body ? json_encode($body) : ''; //转换为json格式
    if ($method == 'POST') {

        curl_setopt($ch, CURLOPT_POST, 1);
        if ($jsonStr) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        }
    } elseif ($method == 'PATCH') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        if ($jsonStr) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        }
    } elseif ($method == 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($jsonStr) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        }
    } elseif ($method == 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        if ($jsonStr) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        }
    } elseif ($method == 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        if ($jsonStr) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        }
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    // $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $response = json_decode($response, true);

    return $response;

}

/** get timestamp
 *
 * @return float
 */
function getMillisecond()
{
    list($t1, $t2) = explode(' ', microtime());
    $rand = rand(1111, 9999);
    return (float)sprintf('%.0f', (floatval($t1) + floatval($t2) + floatval($rand)));
}


// //获取接口地址
// function cloudPz(){
//      $configs=Db::name('config')->where('group','baiduchain')->field('id,title,value')->select();
//      $appId =$configs[0]['value'];
//      $binPath =ROOT_PATH . 'public'.'/tools/xasset-cli/xasset-cli';
//       $crypto = new \EcdsaCrypto($binPath);
//      $config = new \XassetConfig($crypto);
//      $ak =$configs[1]['value'];
//      $sk =$configs[2]['value'];
//      if($configs[4]['value']==0){
//          $config->setCredentials(md5($appId),md5($ak),md5($sk));
//      }else{
//          $config->setCredentials($appId,$ak,$sk);
//      }

//      $config->endPoint = $configs[3]['value'];
//      $xHandle = new \XassetClient($config);

//     if($configs[5]['value']){
//         $addr =$configs[5]['value'];
//         $pubKey = $configs[6]['value'];
//       $privtKey = $configs[7]['value'] ;
//     }else{
//          $addr =0;
//         $pubKey = 0;
//       $privtKey = 0;
//     }


//       $account = array(
//                   'address' => $addr,
//                   'public_key' => $pubKey,
//                  'private_key' => $privtKey,
//                   );
//      $shardId = gen_asset_id($appId);
//      $assetId = gen_asset_id($appId);
//      $arrs=array('binPath'=>$binPath,'xHandle'=>$xHandle,'account'=>$account,'assetId'=>$assetId,'shardId'=>$shardId);
//     return $arrs;
// }


//身份证 手机号 姓名验证
//身份证 手机号 姓名验证
function beckoning($idcard, $mobile, $name)
{
//    $config = Db::name('config')->where('group', 'beckoning')->field('id,value')->select();

    $host = config('site.host');
//    $path = "/mobile/3-validate";
    $path = "";

    $method = "POST";
    $appcode = config('site.appcode');


    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    //根据API的要求，定义相对应的Content-Type
    array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");
    $querys = "";
    $bodys = "idcard={$idcard}&mobile={$mobile}&name={$name}";
    $url = $host . $path;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //设定返回信息中是否包含响应信息头，启用时会将响应信息头作为数据流输出，true 表示输出信息头, false表示不输出信息头
    //如果想将响应结果json字符串转为json数组，建议将 CURLOPT_HEADER 设置成 false
    curl_setopt($curl, CURLOPT_HEADER, false);
    if (1 == strpos("$" . $host, "https://")) {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
    $result = curl_exec($curl);//执行请求
    curl_close($curl);//关闭curl，释放资源
    // print_r($result);
    return $result;


}

if (!function_exists('__')) {

    /**
     * 获取语言变量值
     * @param string $name 语言变量名
     * @param array $vars 动态变量值
     * @param string $lang 语言
     * @return mixed
     */
    function __($name, $vars = [], $lang = '')
    {
        if (is_numeric($name) || !$name) {
            return $name;
        }

        if (!is_array($vars)) {
            $vars = func_get_args();
            array_shift($vars);
            $lang = '';
        }
        return \think\Lang::get($name, $vars, $lang);
    }
}

if (!function_exists('format_bytes')) {

    /**
     * 将字节转换为可读文本
     * @param int $size 大小
     * @param string $delimiter 分隔符
     * @param int $precision 小数位数
     * @return string
     */
    function format_bytes($size, $delimiter = '', $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 6; $i++) {
            $size /= 1024;
        }
        return round($size, $precision) . $delimiter . $units[$i];
    }
}

if (!function_exists('datetime')) {

    /**
     * 将时间戳转换为日期时间
     * @param int $time 时间戳
     * @param string $format 日期时间格式
     * @return string
     */
    function datetime($time, $format = 'Y-m-d H:i:s')
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        return date($format, $time);
    }
}

if (!function_exists('human_date')) {

    /**
     * 获取语义化时间
     * @param int $time 时间
     * @param int $local 本地时间
     * @return string
     */
    function human_date($time, $local = null)
    {
        return \fast\Date::human($time, $local);
    }
}

if (!function_exists('cdnurl')) {

    /**
     * 获取上传资源的CDN的地址
     * @param string $url 资源相对地址
     * @param boolean $domain 是否显示域名 或者直接传入域名
     * @return string
     */
    function cdnurl($url, $domain = false)
    {
        $regex = "/^((?:[a-z]+:)?\/\/|data:image\/)(.*)/i";
        $cdnurl = \think\Config::get('upload.cdnurl');
        $url = preg_match($regex, $url) || ($cdnurl && stripos($url, $cdnurl) === 0) ? $url : $cdnurl . $url;
        if ($domain && !preg_match($regex, $url)) {
            $domain = is_bool($domain) ? request()->domain() : $domain;
            $url = $domain . $url;
        }
        return $url;
    }
}


if (!function_exists('is_really_writable')) {

    /**
     * 判断文件或文件夹是否可写
     * @param string $file 文件或目录
     * @return    bool
     */
    function is_really_writable($file)
    {
        if (DIRECTORY_SEPARATOR === '/') {
            return is_writable($file);
        }
        if (is_dir($file)) {
            $file = rtrim($file, '/') . '/' . md5(mt_rand());
            if (($fp = @fopen($file, 'ab')) === false) {
                return false;
            }
            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);
            return true;
        } elseif (!is_file($file) or ($fp = @fopen($file, 'ab')) === false) {
            return false;
        }
        fclose($fp);
        return true;
    }
}

if (!function_exists('rmdirs')) {

    /**
     * 删除文件夹
     * @param string $dirname 目录
     * @param bool $withself 是否删除自身
     * @return boolean
     */
    function rmdirs($dirname, $withself = true)
    {
        if (!is_dir($dirname)) {
            return false;
        }
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        if ($withself) {
            @rmdir($dirname);
        }
        return true;
    }
}

if (!function_exists('copydirs')) {

    /**
     * 复制文件夹
     * @param string $source 源文件夹
     * @param string $dest 目标文件夹
     */
    function copydirs($source, $dest)
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        foreach (
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            ) as $item
        ) {
            if ($item->isDir()) {
                $sontDir = $dest . DS . $iterator->getSubPathName();
                if (!is_dir($sontDir)) {
                    mkdir($sontDir, 0755, true);
                }
            } else {
                copy($item, $dest . DS . $iterator->getSubPathName());
            }
        }
    }
}

if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_strtolower(mb_substr($string, 1));
    }
}

if (!function_exists('addtion')) {

    /**
     * 附加关联字段数据
     * @param array $items 数据列表
     * @param mixed $fields 渲染的来源字段
     * @return array
     */
    function addtion($items, $fields)
    {
        if (!$items || !$fields) {
            return $items;
        }
        $fieldsArr = [];
        if (!is_array($fields)) {
            $arr = explode(',', $fields);
            foreach ($arr as $k => $v) {
                $fieldsArr[$v] = ['field' => $v];
            }
        } else {
            foreach ($fields as $k => $v) {
                if (is_array($v)) {
                    $v['field'] = isset($v['field']) ? $v['field'] : $k;
                } else {
                    $v = ['field' => $v];
                }
                $fieldsArr[$v['field']] = $v;
            }
        }
        foreach ($fieldsArr as $k => &$v) {
            $v = is_array($v) ? $v : ['field' => $v];
            $v['display'] = isset($v['display']) ? $v['display'] : str_replace(['_ids', '_id'], ['_names', '_name'], $v['field']);
            $v['primary'] = isset($v['primary']) ? $v['primary'] : '';
            $v['column'] = isset($v['column']) ? $v['column'] : 'name';
            $v['model'] = isset($v['model']) ? $v['model'] : '';
            $v['table'] = isset($v['table']) ? $v['table'] : '';
            $v['name'] = isset($v['name']) ? $v['name'] : str_replace(['_ids', '_id'], '', $v['field']);
        }
        unset($v);
        $ids = [];
        $fields = array_keys($fieldsArr);
        foreach ($items as $k => $v) {
            foreach ($fields as $m => $n) {
                if (isset($v[$n])) {
                    $ids[$n] = array_merge(isset($ids[$n]) && is_array($ids[$n]) ? $ids[$n] : [], explode(',', $v[$n]));
                }
            }
        }
        $result = [];
        foreach ($fieldsArr as $k => $v) {
            if ($v['model']) {
                $model = new $v['model'];
            } else {
                $model = $v['name'] ? \think\Db::name($v['name']) : \think\Db::table($v['table']);
            }
            $primary = $v['primary'] ? $v['primary'] : $model->getPk();
            $result[$v['field']] = isset($ids[$v['field']]) ? $model->where($primary, 'in', $ids[$v['field']])->column("{$primary},{$v['column']}") : [];
        }

        foreach ($items as $k => &$v) {
            foreach ($fields as $m => $n) {
                if (isset($v[$n])) {
                    $curr = array_flip(explode(',', $v[$n]));

                    $v[$fieldsArr[$n]['display']] = implode(',', array_intersect_key($result[$n], $curr));
                }
            }
        }
        return $items;
    }
}

if (!function_exists('var_export_short')) {

    /**
     * 使用短标签打印或返回数组结构
     * @param mixed $data
     * @param boolean $return 是否返回数据
     * @return string
     */
    function var_export_short($data, $return = true)
    {
        return var_export($data, $return);
        $replaced = [];
        $count = 0;

        //判断是否是对象
        if (is_resource($data) || is_object($data)) {
            return var_export($data, $return);
        }

        //判断是否有特殊的键名
        $specialKey = false;
        array_walk_recursive($data, function (&$value, &$key) use (&$specialKey) {
            if (is_string($key) && (stripos($key, "\n") !== false || stripos($key, "array (") !== false)) {
                $specialKey = true;
            }
        });
        if ($specialKey) {
            return var_export($data, $return);
        }
        array_walk_recursive($data, function (&$value, &$key) use (&$replaced, &$count, &$stringcheck) {
            if (is_object($value) || is_resource($value)) {
                $replaced[$count] = var_export($value, true);
                $value = "##<{$count}>##";
            } else {
                if (is_string($value) && (stripos($value, "\n") !== false || stripos($value, "array (") !== false)) {
                    $index = array_search($value, $replaced);
                    if ($index === false) {
                        $replaced[$count] = var_export($value, true);
                        $value = "##<{$count}>##";
                    } else {
                        $value = "##<{$index}>##";
                    }
                }
            }
            $count++;
        });

        $dump = var_export($data, true);

        $dump = preg_replace('#(?:\A|\n)([ ]*)array \(#i', '[', $dump); // Starts
        $dump = preg_replace('#\n([ ]*)\),#', "\n$1],", $dump); // Ends
        $dump = preg_replace('#=> \[\n\s+\],\n#', "=> [],\n", $dump); // Empties
        $dump = preg_replace('#\)$#', "]", $dump); //End

        if ($replaced) {
            $dump = preg_replace_callback("/'##<(\d+)>##'/", function ($matches) use ($replaced) {
                return isset($replaced[$matches[1]]) ? $replaced[$matches[1]] : "''";
            }, $dump);
        }

        if ($return === true) {
            return $dump;
        } else {
            echo $dump;
        }
    }
}

if (!function_exists('letter_avatar')) {
    /**
     * 首字母头像
     * @param $text
     * @return string
     */
    function letter_avatar($text)
    {
        $total = unpack('L', hash('adler32', $text, true))[1];
        $hue = $total % 360;
        list($r, $g, $b) = hsv2rgb($hue / 360, 0.3, 0.9);

        $bg = "rgb({$r},{$g},{$b})";
        $color = "#ffffff";
        $first = mb_strtoupper(mb_substr($text, 0, 1));
        $src = base64_encode('<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="100" width="100"><rect fill="' . $bg . '" x="0" y="0" width="100" height="100"></rect><text x="50" y="50" font-size="50" text-copy="fast" fill="' . $color . '" text-anchor="middle" text-rights="admin" dominant-baseline="central">' . $first . '</text></svg>');
        $value = 'data:image/svg+xml;base64,' . $src;
        return $value;
    }
}

if (!function_exists('hsv2rgb')) {
    function hsv2rgb($h, $s, $v)
    {
        $r = $g = $b = 0;

        $i = floor($h * 6);
        $f = $h * 6 - $i;
        $p = $v * (1 - $s);
        $q = $v * (1 - $f * $s);
        $t = $v * (1 - (1 - $f) * $s);

        switch ($i % 6) {
            case 0:
                $r = $v;
                $g = $t;
                $b = $p;
                break;
            case 1:
                $r = $q;
                $g = $v;
                $b = $p;
                break;
            case 2:
                $r = $p;
                $g = $v;
                $b = $t;
                break;
            case 3:
                $r = $p;
                $g = $q;
                $b = $v;
                break;
            case 4:
                $r = $t;
                $g = $p;
                $b = $v;
                break;
            case 5:
                $r = $v;
                $g = $p;
                $b = $q;
                break;
        }

        return [
            floor($r * 255),
            floor($g * 255),
            floor($b * 255)
        ];
    }
}

if (!function_exists('check_nav_active')) {
    /**
     * 检测会员中心导航是否高亮
     */
    function check_nav_active($url, $classname = 'active')
    {
        $auth = \app\common\library\Auth::instance();
        $requestUrl = $auth->getRequestUri();
        $url = ltrim($url, '/');
        return $requestUrl === str_replace(".", "/", $url) ? $classname : '';
    }
}

if (!function_exists('check_cors_request')) {
    /**
     * 跨域检测
     */
    function check_cors_request()
    {
        if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN']) {
            $info = parse_url($_SERVER['HTTP_ORIGIN']);
            $domainArr = explode(',', config('fastadmin.cors_request_domain'));
            $domainArr[] = request()->host(true);
            if (in_array("*", $domainArr) || in_array($_SERVER['HTTP_ORIGIN'], $domainArr) || (isset($info['host']) && in_array($info['host'], $domainArr))) {
                header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
            } else {
                header('HTTP/1.1 403 Forbidden');
                exit;
            }

            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');

            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
                }
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                }
                exit;
            }
        }
    }
}

if (!function_exists('xss_clean')) {
    /**
     * 清理XSS
     */
    function xss_clean($content, $is_image = false)
    {
        return \app\common\library\Security::instance()->xss_clean($content, $is_image);
    }
}

if (!function_exists('check_ip_allowed')) {
    /**
     * 检测IP是否允许
     * @param string $ip IP地址
     */
    function check_ip_allowed($ip = null)
    {
        $ip = is_null($ip) ? request()->ip() : $ip;
        $forbiddenipArr = config('site.forbiddenip');
        $forbiddenipArr = !$forbiddenipArr ? [] : $forbiddenipArr;
        $forbiddenipArr = is_array($forbiddenipArr) ? $forbiddenipArr : array_filter(explode("\n", str_replace("\r\n", "\n", $forbiddenipArr)));
        if ($forbiddenipArr && \Symfony\Component\HttpFoundation\IpUtils::checkIp($ip, $forbiddenipArr)) {
            header('HTTP/1.1 403 Forbidden');
            exit;
        }
    }
}
if (!function_exists('validateCode')) {
    //生成唯一字符串
    function validateCode($member, $code, $type)
    {
        $redis = \comservice\GetRedis::getRedis();
        $checkCode = $redis->getItem($member . '-' . $type);
        if ($checkCode == $code) {
            return true;
        }
//        else{
//            $universal_code = \think\Db::name('config')->where(['id'=>36])->value('value');
//            if($universal_code == $code) return true;
//        }
        return false;
    }
}

if (!function_exists('checkEmail')) {
    function checkEmail($email)
    {
        $result = trim($email);

        if (filter_var($result, FILTER_VALIDATE_EMAIL)) return true;
        return false;

    }
}
if (!function_exists('checkPhone')) {
    function checkPhone($phone)
    {
        $reg = "/^1[23456789]\d{9}$/";
        //返回匹配到的次数
        $res = preg_match($reg, $phone);
        if ($res > 0) return true;
        return false;
    }
}
if (!function_exists('uniqueNum')) {
    //生成唯一字符串
    function uniqueNum()
    {

        $order_no = strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(111, 999));
        return $order_no;
    }
}
if (!function_exists('addWebSiteUrl')) {
    function addWebSiteUrl($array, $fields = [])
    {
        $url = config('site.server_url');
        if (count($array) <= 0) return $array;
        if (count($array) == count($array, 1)) {
            //一维数组
            if (count($fields) > 0) {
                foreach ($fields as $v) {
                    if (strpos($array[$v], 'http') !== 0)
                        $array[$v] = str_replace('/uploads/', $url . '/uploads/', $array[$v]);
                }
            } else {
                foreach ($array as $k => &$v) {
                    if (strpos($v, 'http') !== 0)
                        $array[$k] = str_replace('/uploads/', $url . '/uploads/', $v);
                }
            }
            return $array;
        } else {
            if (count($array) <= 0) return $array;
            foreach ($array as &$v1) {
                foreach ($fields as &$v2) {
                    if (!empty($v1[$v2])) {
                        if (strpos($v1[$v2], 'http') !== 0)
                            $v1[$v2] = str_replace('/uploads/', $url . '/uploads/', $v1[$v2]);
                    }
                }
            }
            return $array;
        }
    }
}
if (!function_exists('trimWebUrl')) {
    function trimWebUrl($array, $fields)
    {
        $url = 'https://' . $_SERVER['HTTP_HOST'];
        if (count($array) <= 0) return $array;
        if (count($array) == count($array, 1)) {
            //一维数组
            foreach ($fields as $v) {
                $array[$v] = str_replace($url . '/uploads/', '/uploads/', $array[$v]);
            }
            return $array;
        } else {
            if (count($array) <= 0) return $array;
            foreach ($array as &$v1) {
                foreach ($fields as &$v2) {
                    if (!empty($v1[$v2])) {
                        $v1[$v2] = str_replace($url . '/uploads/', '/uploads/', $v1[$v2]);
                    }
                }
            }
            return $array;
        }
    }
}
if (!function_exists('content')) {
    function content($content)
    {
        $url = config('site.server_url');
        $content = preg_replace('/src="(?!http)/i', 'src="' . $url, $content);
        return $content;
    }
}
if (!function_exists('trimContent')) {
    function trimContent($content)
    {
        $url = config('site.server_url');
        $content = str_replace('src="' . $url, 'src="', $content);
        return $content;
    }
}
if (!function_exists('bug')) {
    function bug($data)
    {
        echo "<pre/>";
        dump($data);
        die;
    }
}
if (!function_exists('uuid')) {
    function uuid()
    {
        $code = "ABCDEFGHIGKLMNOPQRSTUVWXYZ";
        $rand = $code[rand(0, 25)] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        for (
            $a = md5($rand, true),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < 6;
            $g = ord($a[$f]), // ord（）函数获取首字母的 的 ASCII值
            $d .= $s[($g ^ ord($a[$f + 8])) - $g & 0x1F],
            $f++) ;

        $userInfo = (new \datamodel\Users())->where(['uuid' => $d])->find();
        if ($userInfo) {
            return uuid();
        }
        return $d;
    }
}


/**
 * 默认头像
 * @return mixed
 */

if (!function_exists('defaultImage')) {
    function defaultImage()
    {
        $images = \think\Config::get('site.default_image');
        return $images[array_rand($images)];
    }
}

if (!function_exists('splitTime')) {
    function splitTime($time)
    {
        $time = explode(' - ', $time);
        return [$time[0], $time[1]];
    }
}

/**
 * 随机抽奖算法
 */
if (!function_exists('getWinRand')) {
    function getWinRand($proArr)
    {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }
}



