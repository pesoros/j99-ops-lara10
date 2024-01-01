<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;

class Rest extends Model
{
    private $client;
    private $token;
    private $dbSession;
    private $headers;

    public function __construct()
    {
        $this->client = new Client(['timeout'  => 2.0]);
        $this->token = Accurate::getToken('token')->token;
        $this->dbSession = Accurate::getToken('db_session')->token;
        $this->headers = [
            'Authorization' => 'Bearer '.$this->token,
            'X-Session-ID' => $this->dbSession
        ];
    }

    public function scopeGetGoodsList()
    {
        $fetch = $this->client->request(
            'GET', env('ACCURATE_APP_URI').'/item/list.do?fields=id,name', [
            'headers' => $this->headers,
        ])->getBody();

        return json_decode($fetch);
    }

    public function scopeGetGoods($query, $id)
    {
        $fetch = $this->client->request(
            'GET', env('ACCURATE_APP_URI').'/item/detail.do?id='.$id, [
            'headers' => $this->headers,
        ])->getBody();

        return json_decode($fetch);
    }
}
