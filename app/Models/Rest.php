<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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

    public function scopeGetSpareParts($query, $keyword)
    {
        $fetch = $this->client->request(
            'GET', env('ACCURATE_APP_URI').'/item/list.do?fields=id,name&keywords='.$keyword, [
            'headers' => $this->headers,
        ])->getBody();

        return json_decode($fetch);
    }

    public function scopeGetSparePartsDetail($query, $id)
    {
        $fetch = $this->client->request(
            'GET', env('ACCURATE_APP_URI').'/item/detail.do?id='.$id, [
            'headers' => $this->headers,
        ])->getBody();

        return json_decode($fetch);
    }

    public function scopeGetTrasBus($query, $trasid)
    {
        $query = DB::table("v2_bus AS bus")
            ->select('bus.uuid','bus.name as busname')
            ->where('bus.assign_id_a',$trasid)
            ->orWhere('bus.assign_id_b',$trasid)
            ->orderBy('busname','ASC')
            ->get();

        return $query;
    }
}
