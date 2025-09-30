<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class Watzapp extends Model
{
    private $client;

    public function __construct()
    {
        $this->client = new Client(['timeout'  => 2.0]);
    }

    public function scopeSendWaPassenger($query, $phone, $text)
    {
        $formatPhone = formatPhone($phone);
        $formatPhone = str_replace('+', '', $formatPhone);
        $url = env('WATZAP_BASEURL') . '/send_message';

        $payload = [
            "api_key"    => env('WATZAP_KEY'),
            "number_key" => env('WATZAP_NUMBER'),
            "phone_no"   => $formatPhone,
            "message"    => $text
        ];

        $response = $this->client->request('POST', $url, [
            'json' => $payload, // otomatis encode JSON dan set header
        ]);

        return json_decode($response->getBody(), true);
    }
}
