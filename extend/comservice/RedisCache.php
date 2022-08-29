<?php
/**
 * Created shihe
 * UserManager: shihe
 * desc: redis缓存的操作类，采用单例模式，防止单个请求重复重建redis实例
 * Date: 2017/4/27
 * Time: 17:18
 */
namespace comservice;
use think\Log;

class RedisCache
{
    //声明私有静态变量
    private static $_instance=null;
    //生命私有redis对象
    private $_redis;
    //私有构造函数，防止重复创建实例
    private function __construct($sever , $auth)
    {
        //初始化redis对象实例
        $this->_redis=new \Redis();
        //连接redis服务器
        $redis_server = $sever ;
        $redis_auth = $auth;
        $this->_redis->connect($redis_server);
        $this->_redis->auth($redis_auth);
        Log::record("redis创建连接成功:{".date('Y-m-d H:i:s')."}","info");
    }
    private function __clone()
    {

    }

    /**
     * 创建本类的实例方法，本类的对象必须通过此方法创建
     * @param string $sever ：服务器地址
     * @param string $auth ：密码
     * @return RedisCache|null
     */
    public static function getInstance($sever , $auth)
    {
        if(!self::$_instance instanceof self)
        {

            self::$_instance=new self($sever,$auth);

            Log::record("单列模式实例创建成功:{".date('Y-m-d H:i:s')."}","info");
        }
        return self::$_instance;
    }

    /**
     * 增加单个元素到key_value缓存
     * @param string $k ：缓存的key值
     * @param string  $v ：缓存的value值，此处必须是string类型，如果要缓存数组，请调用方中把数组json_encode
     * @return bool
     */
    public function setItem($k,$v)
    {

        return $this->_redis->set($k,$v); //成功返回ture,失败返回false

    }

    /**
     * 获取单个元素到（key_value缓存）
     * @param string $k ：要获取缓存的key值
     * @return bool|string
     */
    public function getItem($k)
    {
        return $this->_redis->get($k);
    }

    /**
     * 批量存储key_value数组
     * @param array $arrData ：必须为key=>value类型的数组,Redis存储为每个对应的key_value
     * @return bool
     */
    public function setMItem(array $arrData)
    {
        return $this->_redis->mset($arrData);
    }

    /**
     * 根据key数组批量获取对应的value数组
     * @param array $keyData ：要获取value数组对应的key数组
     * @return array
     */
    public function getMItem(array $keyData)
    {
        return $this->_redis->mget($keyData);
    }

    /**
     * 删除指定key的元素
     * @param string $k ：要删除元素的key
     * @return int:返回删除的数量，0为没有删除成功
     */
    public function delItem($k)
    {
        return $this->_redis->del($k);
    }

    /**
     * 选择redis数据库
     * @param string $k ：数据库索引
     * @return int:返回删除的数量，0为没有删除成功
     */
    public function selectDb($k){
        return $this->_redis->select($k);
    }
    //单个存储哈希类型
    public function setHItem($n,$k,$v)
    {
        return $this->_redis->hset($n,$k,$v);
    }
    //批量存储哈希类型
    public function setHMTtem($k,array $arrData)
    {
        return $this->_redis->hmset($k,$arrData);
    }
    //单个获取哈希类型
    public function getHItem($k,$v)
    {
        return $this->_redis->hget($k,$v);
    }
    //获取哈希类型的所有值
    public function getHMItem($k,$v)
    {
        return $this->_redis->hmget($k,$v);
    }
    //获取一个哈希数据的所有键和值
    public function getHAllItem($keyData)
    {
        return $this->_redis->hgetall($keyData);
    }

    /**
     * 删除hash表中的一个域
     * @param $k
     * @param $v
     * @return bool|int
     */
    public function hdel($k,$v)
    {
        return $this->_redis->hDel($k,$v);
    }
    //设置过期时间
    public function settime($k,$v)
    {
        return $this->_redis->EXPIRE($k,$v);
    }

    /**
     * 从列表头部弹出一个值
     * @param $k
     * @return string
     */
    public function lpop($k)
    {
        return $this->_redis->lPop($k);
    }

    /**
     * 在头部向列表中插入一个值
     * @param $k
     * @param $v
     * @return bool|int
     */
    public function lpush($k,$v)
    {
        return $this->_redis->lPush($k,$v);
    }

    /**
     * 在尾部向列表中插入一个值
     * @param $k
     * @param $v
     * @return bool|int
     */
    public function rpush($k,$v)
    {
        return $this->_redis->rPush($k,$v);
    }

    /**
     * 从尾部弹出列表中的元素
     * @param $k
     * @return string
     */
    public function rpop($k)
    {
        return $this->_redis->rPop($k);
    }

    public function exists($k)
    {
        return $this->_redis->exists($k);
    }

    /**
     * 获取列表的长度
     * @param $k
     * @return int
     */
    public function lLen($k)
    {
        return $this->_redis->lLen($k);
    }

    /**
     * 移除列表中的元素
     * @param $k
     * @param $v
     * @param $count
     * @return int
     */
    public function lRem($k,$v,$count)
    {
        return $this->_redis->lRem($k,$v,$count);
    }

    /**
     * 存储数据到集合
     * @param $k
     * @param $v
     * @return int
     */
    public function sAdd($k,$v)
    {
        return $this->_redis->SADD($k,$v);
    }

    /**
     * 返回集合中所有成员
     * @param $k
     * @return array
     */
    public function smembers($k)
    {
        return $this->_redis->SMEMBERS($k);
    }

    /**
     * 判断一个元素是否在集合中
     * @param $k
     * @param $value
     * @return bool
     */
    public function  sisMember($k,$value){
        return $this->_redis->sIsMember($k,$value);
    }

    public function sRem($key,$value){
        return $this->_redis->sRem($key,$value);
    }
}
