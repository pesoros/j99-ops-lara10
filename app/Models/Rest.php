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
        $fields = 'id,name,quantity,no';
        $pageSize = '&sp.pageSize=80';
        $search = '&filter.keywords.op=CONTAIN&filter.keywords.val='.$keyword;

        $fetch = $this->client->request(
            'GET', env('ACCURATE_APP_URI').'/item/list.do?fields='.$fields.$pageSize.$search, [
            'headers' => $this->headers,
        ])->getBody();

        return json_decode($fetch);
    }

    public function scopeGetSparePartsDetail($query, $no)
    {
        $fetch = $this->client->request(
            'GET', env('ACCURATE_APP_URI').'/item/detail.do?no='.$no, [
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
            ->select(
                'tras.id as trasid',
                'tras.trip as trip',
                'trip.trip_title',
                'trip.route',
                'trip_route.crew_meal',
                'trip_route.premi_driver',
                'trip_route.premi_codriver',
                'trip_route.etoll',
            )
            ->join("trip", "trip.trip_id", "=", "tras.trip")
            ->join("trip_route", "trip_route.id", "=", "trip.route")
            // ->where('tras.status','1')
            ->where('tras.id',$trasid)
            ->orderBy('trasid','ASC')
            ->first();

        return $query;
    }

    public function scopeGetFuelAllowance($query, $busUuid, $route)
    {
        $query = DB::table("fuel_allowance")
            ->select('fuel_allowance.allowance')
            ->where('bus_uuid',$busUuid)
            ->where('route',$route)
            ->orderBy('id','DESC')
            ->get();

        return $query;
    }

    public function scopeGetInvoice($query, $page, $startDate, $endDate)
    {
        $fields = 'id,billNumber,description,transDate,totalAmount,approvalStatus';
        $paging = '';
        $dateRange = '';
        if ($page) {
            $paging = '&sp.page='.$page;
        }
        if ($startDate) {
            if ($endDate) {
                $dateRange = '&filter.transDate.op=BETWEEN&filter.transDate.val='.$startDate.'&filter.transDate.val='.$endDate;
            } else {
                $dateRange = '&filter.transDate.val='.$startDate;
            }
        }

        $fetch = $this->client->request(
            'GET', env('ACCURATE_APP_URI').'/purchase-invoice/list.do?fields='.$fields.'&sp.sort=transDate|asc'.$paging.$dateRange, [
            'headers' => $this->headers,
        ])->getBody();

        return json_decode($fetch);
    }

    public function scopeGetInvoiceDetail($query, $id)
    {
        $fetch = $this->client->request(
            'GET', env('ACCURATE_APP_URI').'/purchase-invoice/detail.do?id='.$id, [
            'headers' => $this->headers,
        ])->getBody();

        return json_decode($fetch);
    }

    public function scopePostItemStock($query, $raw)
    {
        $headers = [
            ...$this->headers,
            'Content-Type' => 'application/json'
        ];

        $fetch = $this->client->request(
            'POST', env('ACCURATE_APP_URI').'/item-adjustment/save-target-quantity.do', [
            'body' => json_encode($raw),
            'headers' => $headers,
        ])->getBody();

        return json_decode($fetch);
    }

    public function scopeSendWaPassenger($query, $phone, $text)
    {
        $headers = [
            ...$this->headers,
            'Content-Type' => 'application/json'
        ];
        $formatPhone = formatPhone($phone);
        $formatPhone = str_replace('+', '', $formatPhone);
        $url = env('WA_BASEURL').'/api/sendText?phone='.$formatPhone.'&text='.$text.'&session=default';

        $fetch = $this->client->request(
            'GET', $url, [
            'headers' => $headers,
        ])->getBody();

        return json_decode($fetch);
    }

    public function scopeSendWaPassengerPost($query, $phone, $text)
    {
        try {
            $url = env('WA_BASEURL').'/api/sendImage';
            $formatPhone = formatPhone($phone);
            $formatPhone = str_replace('+', '', $formatPhone);
            $fetch = $this->client->request('POST', $url,[
                'form_params' => [
                    'session'   => 'default',
                    'caption'   => $text,
                    'chatId'    => $formatPhone,
                    'file'      => [
                        'mimetype'  => 'image/jpg',
                        'filename'  => 'j99/jpg',
                        'url'       => 'https://github.com/devlikeapro/whatsapp-http-api/raw/core/examples/dev.likeapro.jpg'
                    ]            
                ]
            ])->getBody();
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return $response->getBody()->getContents();
        }

        return $fetch;
    }


    public function scopePostBoardingPuloGebang($query, $raw)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'X-TTPG-KEY' => env('PULOGEBANG_KEY')
        ];

        $fetch = $this->client->request(
            'POST', env('PULOGEBANG_URL').'/boarding', [
            'body' => json_encode($raw),
            'headers' => $headers,
        ])->getBody();

        return json_decode($fetch);
    }
}
