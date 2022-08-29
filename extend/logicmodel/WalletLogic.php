<?php


namespace logicmodel;


use comservice\Response;
use xtype\Ethereum\Client;

class WalletLogic
{

    private $client;
    public function __construct()
    {
        $this->client = new Client('https://kovan.infura.io/v3/a0d810fdff64493baba47278f3ebad27');
    }

    /**
     * 创建新账号
     * @return array|bool
     */
    public function newAccount(){
        try {
            list($address, $privateKey) = $this->client->newAccount();
            list($bsc_address, $bsc_privateKey) = $this->client->newAccount();
            list($ht_address, $ht_privateKey) = $this->client->newAccount();
            return  ['address'=>$address,'private_key'=>$privateKey,'bsc_address'=>$bsc_address,'bsc_private_key'=>$bsc_privateKey,'ht_address'=>$ht_address,'ht_private_key'=>$ht_privateKey];
        }catch (\Exception $e){
            return false;
        }
    }



}