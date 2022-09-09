<?php

namespace TiChain;


class TiChain
{

    private $getaway;

    public $defaultConfig = [
        'appId' => '',
        'appKey' => '',
    ];

    private $config = [
        'appId' => '',
        'appKey' => '',
    ];

    private $api = [
        'register' => '/api/v2/user',
        'publish' => '/api/v2/nfr/publish',
        'transfer' => '/api/v2/nfr/transfer',
        'destroy' => '/api/v2/nfr/burn',
        'detail' => '/api/v2/transaction/detail',
        'userUpdate' => '/api/v2/user/update',
        'userInfo' => '/api/v2/user/info',
        'status' => '/api/v2/nfr/transaction/status',
    ];

    private $getawayList = [
        'dev' => 'https://test.api.tichain.tianhecloud.com',
        'prod' => 'https://api.tichain.tianhecloud.com',
    ];

    private $client;

    public function __construct($config)
    {
        $this->config['appId'] = $config['appId'];
        $this->config['appKey'] = $config['appKey'];
        $this->getaway = $config['getaway'] ? $this->getawayList[$config['getaway']] : $this->getawayList['prod'];
    }

    public function parseApi($action, $withGetaway = false)
    {
        return ($withGetaway ? $this->getaway : '') . $this->api[$action];
    }

    private function getClientInstance()
    {
        if (!$this->client) {
            $this->client = new \GuzzleHttp\Client([
                'base_uri' => $this->getaway,
            ]);
        }
        return $this->client;
    }

    private static function response2json($response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * 用户链上身份注册
     * @param $userId
     * @param $userKey
     * @return array|bool|float|int|mixed|\stdClass|string|\要转换的数据|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function register($userId, $userKey)
    {
        $param = array_merge($this->config, [
            'userId' => $userId,
            'userKey' => $userKey,
        ]);
        $res = $this->getClientInstance()->post($this->parseApi(__FUNCTION__), [
            \GuzzleHttp\RequestOptions::JSON => $param
        ]);
        return self::response2json($res);
    }

    /**
     * 数字商品发行
     * @param $userId
     * @param $userKey
     * @param $config
     * @return array|bool|float|int|mixed|\stdClass|string|\要转换的数据|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function publish($userId, $userKey, $config)
    {
        $param = array_merge($this->config, [
            'userId' => $userId,
            'userKey' => $userKey,
            'name' => $config['name'],
            'pieceCount' => $config['pieceCount'],
        ]);
        $res = $this->getClientInstance()->post($this->parseApi(__FUNCTION__), [
            'json' => $param,
        ]);
        return self::response2json($res);
    }

    /**
     * 数字商品交易
     * @param $userId
     * @param $userKey
     * @param $config
     * @return array|bool|float|int|mixed|\stdClass|string|\要转换的数据|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function transfer($userId, $userKey, $config)
    {
        $param = array_merge($this->config, [
            'userId' => $userId,
            'userKey' => $userKey,
            'contractAddress' => $config['contractAddress'], // 合约地址
            'tokenId' => $config['tokenId'], // token编号
            'from' => $config['from'], // 发起方公钥地址
            'to' => $config['to'], // 接收方公钥地址
        ]);
        $res = $this->getClientInstance()->post($this->parseApi(__FUNCTION__), [
            'json' => $param,
        ]);
        return self::response2json($res);
    }

    /**
     * 销毁数字商品
     * @param $userId
     * @param $userKey
     * @param $config
     * @return array|bool|float|int|mixed|\stdClass|string|\要转换的数据|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function destroy($userId, $userKey, $config)
    {
        $param = array_merge($this->config, [
            'userId' => $userId,
            'userKey' => $userKey,
            'contractAddress' => $config['contractAddress'], // 合约地址
            'tokenId' => $config['tokenId'], // token编号
        ]);
        $res = $this->getClientInstance()->post($this->parseApi(__FUNCTION__), [
            'json' => $param,
        ]);
        return self::response2json($res);
    }

    /**
     * 交易结果查询
     * @param $userId
     * @param $userKey
     * @param $config
     * @return array|bool|float|int|mixed|\stdClass|string|\要转换的数据|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function detail($userId, $userKey, $config)
    {
        $param = array_merge($this->config, [
            'userId' => $userId,
            'userKey' => $userKey,
            'transactionHash' => $config['transactionHash'], // 交易哈希
            // 查询的方法名(查询数字商品交易的情况，输入"transferFrom"；查询销毁数字商品的情况，输入："burn"；查询数字商品发行输入"mint"。)
            'methodName' => $config['methodName'],
        ]);
        $res = $this->getClientInstance()->post($this->parseApi(__FUNCTION__), [
            'json' => $param,
        ]);
        return self::response2json($res);
    }

    /**
     * 用户链上身份秘钥修改
     * @param $userId
     * @param $userKey
     * @param $config
     * @return array|bool|float|int|mixed|\stdClass|string|\要转换的数据|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function userUpdate($userId, $userKey, $config)
    {
        $param = array_merge($this->config, [
            'userId' => $userId,
            'userKey' => $userKey,
            'transactionHash' => $config['newUserKey'], // 用户新密钥
        ]);
        $res = $this->getClientInstance()->post($this->parseApi(__FUNCTION__), [
            'json' => $param,
        ]);
        return self::response2json($res);
    }

    /**
     * 用户链上身份查询
     * @param $userId
     * @param $userKey
     * @param $config
     * @return array|bool|float|int|mixed|\stdClass|string|\要转换的数据|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function userInfo($userId, $userKey, $config)
    {
        $param = array_merge($this->config, [
            'userId' => $userId,
            'userKey' => $userKey,
        ]);
        $res = $this->getClientInstance()->post($this->parseApi(__FUNCTION__), [
            'json' => $param,
        ]);
        return self::response2json($res);
    }

    /**
     * 查询交易状态
     * 根据交易哈希查询 TokenID （目前仅限测试网使用）
     * @param $userId
     * @param $userKey
     * @param $config
     * @return array|bool|float|int|mixed|\stdClass|string|\要转换的数据|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function status($userId, $userKey, $config)
    {
        $param = array_merge($this->config, [
            'userId' => $userId,
            'userKey' => $userKey,
            'transactionHash' => $config['transactionHash'], // 交易哈希
        ]);
        $res = $this->getClientInstance()->post($this->parseApi(__FUNCTION__), [
            'json' => $param,
        ]);
        return self::response2json($res);
    }

}