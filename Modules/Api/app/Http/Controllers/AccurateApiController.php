<?php

namespace Modules\Api\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Accurate;
use App\Models\MasterData;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class AccurateApiController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('ACCURATE_BASEURI'),
            'timeout'  => 2.0,
        ]);
    }

    public function newtoken(Request $request)
    {
        $type = 'code';
        $redirect = strval(url(env('ACCURATE_RECEIVETOKEN_ENDPOINT').'/newtoken'));
        return redirect()->to(env('ACCURATE_AUTH_URI').'?client_id='.env('ACCURATE_CLIENTID').'&response_type='.$type.'&redirect_uri='.$redirect.'&scope='.env('ACCURATE_SCOPE'));
    }

    public function refreshtoken(Request $request)
    {
        try {
            $getToken = Accurate::getToken('token');
            $getAccessToken = $this->client->request('POST', '/oauth/token',[
                'headers' => [
                    'Authorization' => 'Basic '.env('ACCURATE_APITOKEN')
                ],        
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $getToken->refresh_token
                ]
            ])->getBody();

            $getAccessToken = json_decode($getAccessToken);
            $expires = Carbon::now()->addSeconds($getAccessToken->expires_in)->toDateTimeString();
            $now = Carbon::now();
            $data = [
                'value' => $getAccessToken->access_token,
                'refresh_token' => $getAccessToken->refresh_token,
                'expires_at' => $expires,
                'updated_at' => $now
            ];
            $updateToken = Accurate::updateAccurateToken('token',$data);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return $response->getBody()->getContents();
        }

        return $getAccessToken;
    }

    public function dbsession()
    {
        $getToken = Accurate::getToken('token');
        try {
            $getDbSession = $this->client->request('GET', '/api/open-db.do?id='.env('ACCURATE_DBID'),[
                'headers' => [
                    'Authorization' => 'Bearer '.$getToken->token
                ],             
            ])->getBody();

            $getDbSession = json_decode($getDbSession);
            $expires = Carbon::parse($getDbSession->accessibleUntil);
            $now = Carbon::now();
            $data = [
                'value' => $getDbSession->session,
                'expires_at' => $expires,
                'updated_at' => $now
            ];
            $updateToken = Accurate::updateAccurateToken('db_session',$data);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return $response->getBody()->getContents();
        }

        return $getDbSession;
    }
    
    public function newtokenreceive(Request $request)
    {
        $params = $request->all();

        if (!isset($params['code'])) {
            return;
        }

        try {
            $getAccessToken = $this->client->request('POST', '/oauth/token',[
                'headers' => [
                    'Authorization' => 'Basic '.env('ACCURATE_APITOKEN')
                ],        
                'form_params' => [
                    'code' => $params['code'],
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => strval(url(env('ACCURATE_RECEIVETOKEN_ENDPOINT').'/newtoken'))
                ]
            ])->getBody();

            $getAccessToken = json_decode($getAccessToken);
            $expires = Carbon::now()->addSeconds($getAccessToken->expires_in)->toDateTimeString();
            $now = Carbon::now();
            $data = [
                'value' => $getAccessToken->access_token,
                'refresh_token' => $getAccessToken->refresh_token,
                'expires_at' => $expires,
                'updated_at' => $now
            ];
            $updateToken = Accurate::updateAccurateToken('token',$data);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return $response->getBody()->getContents();
        }

        return $getAccessToken;
    }

    public function syncDataCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv',
        ]);
        $file = $request->file('file');
        $fileContents = file($file->getPathname());
        $savedCount = 0;
        $updatedCount = 0;

        foreach ($fileContents as $line) {
            $row = str_getcsv($line);
            if (strtoupper($row[0]) === strtoupper('nama barang') || trim($row[0]) === '') {
                continue;
            }
            $getPart = MasterData::getMasterPartDummy($row[0]);
            if (!isset($getPart->name)) {
                $savedCount++;
                $saveData = [
                    'uuid' => generateUuid(),
                    'name' => $row[0],
                    'code' => '-',
                    'unit' => $row[2],
                    'qty' => $row[3],
                ];
                MasterData::saveMasterPartDummy($saveData);
            } else {
                $updatedCount++;
            }
        }

        $result = [
            'saved' => $savedCount,
            'updated' => $updatedCount,
        ];

        return $result;
    }
    
}
