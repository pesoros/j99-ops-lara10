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
            ->select('tras.id as trasid','tras.trip as trip','trip.trip_title')
            ->join("trip", "trip.trip_id", "=", "tras.trip")
            ->where('tras.status','1')
            ->where('tras.id',$trasid)
            ->orderBy('trasid','ASC')
            ->first();

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
        $formatPhone = formatPhone('081288855773');
        $formatPhone = str_replace('+', '', $formatPhone);
        $url = getenv('WA_BASEURL').'/api/sendText?phone='.$formatPhone.'&text='.$text.'&session=default';

        $fetch = $this->client->request(
            'GET', $url, [
            'headers' => $headers,
        ])->getBody();

        return json_decode($fetch);
    }

    public function scopeSendWaPassengerPost($query, $phone, $text)
    {
        $file =[
            'mimetype'  => 'image/jpg',
            'filename'  => 'j99/jpg',
            'url'       => 'https://github.com/devlikeapro/whatsapp-http-api/raw/core/examples/dev.likeapro.jpg'
        ];
        try {
            $url = getenv('WA_BASEURL').'/api/sendText';
            $formatPhone = formatPhone('081288855773');
            $formatPhone = str_replace('+', '', $formatPhone);
            $fetch = $this->client->request('POST', $url,[
                'headers' => [
                    ...$this->headers,
                    'Content-Type' => 'application/json'
                ],        
                'form_params' => [
                    'session'   => 'default',
                    'caption'   => $text,
                    'chatId'    => $formatPhone,
                    'file'      => $file
                ]
            ])->getBody();
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return $response->getBody()->getContents();
        }

        return $fetch;
    }
}
