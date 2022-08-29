<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/1 0001
 * Time: 14:45
 */

namespace logicmodel;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;


class ClientLogic
{
    private $client;
    public function __construct()
    {
        $this->client = new Client();
    }
    /**
     * 发送get请求
     * @param $url
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendGet($url)
    {
        $request = new Request('GET',$url);
        $response = $this->client->send($request);
        return $response->getBody()->getContents();
    }

    /**
     * 发送post 请求
     * @param $url
     * @param $data
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function  sendPost($url,$data=[])
    {
        $response = $this->client->post($url,[
            'form_params' => $data]);
        return $response->getBody()->getContents();

    }

    /**
     * 发起post请求
     * @param $url
     * @param array $data
     * @return string
     */
    public function payPost($url,$data=[]){
        $headers = [
            'Content-Type'  => 'application/x-www-form-urlencoded',
        ];
        $response = $this->client->post($url,[
            'form_params' => $data,'headers'=>$headers]);
        return $response->getBody()->getContents();
    }
}