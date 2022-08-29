<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/1
 * Time: 16:28
 */
namespace comservice;
use think\Config;

class GetRedis
{
    public static function getRedis()
    {
        $redis = RedisCache::getInstance('127.0.0.1',null);
        $redis->selectDb(0);
        return $redis;
    }
}
