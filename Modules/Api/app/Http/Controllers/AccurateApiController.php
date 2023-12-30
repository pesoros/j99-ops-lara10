<?php

namespace Modules\Api\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Accurate;
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
        $redirect = strval(url(env('ACCURATE_RECEIVETOKEN_ENDPOINT').'/newtoken'));
        return redirect()->to(env('ACCURATE_AUTH_URI').'?client_id='.env('ACCURATE_CLIENTID').'&response_type=code&redirect_uri='.$redirect.'&scope=item_view warehouse_view');
    }

    public function refreshtoken(Request $request)
    {
        $redirect = strval(url(env('ACCURATE_RECEIVETOKEN_ENDPOINT').'/refreshtoken'));
        return redirect()->to(env('ACCURATE_AUTH_URI').'?client_id='.env('ACCURATE_CLIENTID').'&response_type=code&redirect_uri='.$redirect.'&scope=item_view warehouse_view');
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
            $data = [
                'value' => $getDbSession->session,
                'expires_at' => $expires
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
            $data = [
                'value' => $getAccessToken->access_token,
                'refresh_token' => $getAccessToken->refresh_token,
                'expires_at' => $expires
            ];
            $updateToken = Accurate::updateAccurateToken('token',$data);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return $response->getBody()->getContents();
        }

        return $getAccessToken;
    }

    public function refreshtokenreceive(Request $request)
    {
        $params = $request->all();

        if (!isset($params['code'])) {
            return;
        }

        try {
            $getToken = Accurate::getToken('token');
            $getAccessToken = $this->client->request('POST', '/oauth/token',[
                'headers' => [
                    'Authorization' => 'Basic '.env('ACCURATE_APITOKEN')
                ],        
                'form_params' => [
                    'code' => $params['code'],
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $getToken->refresh_token
                ]
            ])->getBody();

            $getAccessToken = json_decode($getAccessToken);
            $expires = Carbon::now()->addSeconds($getAccessToken->expires_in)->toDateTimeString();
            $data = [
                'value' => $getAccessToken->access_token,
                'refresh_token' => $getAccessToken->refresh_token,
                'expires_at' => $expires
            ];
            $updateToken = Accurate::updateAccurateToken('token',$data);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return $response->getBody()->getContents();
        }

        return $getAccessToken;
    }
}
