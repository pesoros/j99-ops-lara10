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
        $fields = 'id,name,quantity';
        $fetch = $this->client->request(
            'GET', env('ACCURATE_APP_URI').'/item/list.do?fields='.$fields.'&keywords='.$keyword, [
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

    public function scopeGetBus($query, $busuuid)
    {
        $query = DB::table("v2_bus AS bus")
            ->select('bus.uuid', 'bus.assign_id_a','bus.assign_id_b')
            ->where('bus.uuid',$busuuid)
            ->first();

        return $query;
    }

    function scopeGetTripAssign($query, $trasid)
    {
        $query = DB::table("trip_assign AS tras")
            ->select('tras.id as trasid','tras.trip as trip','trip.trip_title')
            ->join("trip", "trip.trip_id", "=", "tras.trip")
            ->where('tras.status','1')
            ->where('tras.id',$trasid)
            ->orderBy('trasid','ASC')
            ->first();

        return $query;
    }
}
