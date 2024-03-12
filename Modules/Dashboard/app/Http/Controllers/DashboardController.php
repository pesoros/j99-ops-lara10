<?php

namespace Modules\Dashboard\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Accurate;
use Carbon\Carbon;
use GuzzleHttp\Client;

class DashboardController extends Controller
{
    public function index()
    {
        $data['title'] = 'Dashboard';
        $this->checkToken();
        $data['roleData'] = Session('role_info_session');

        return view('dashboard::dashboard.index', $data);
    }

    function checkToken() 
    {
        $minDiff = 1;
        $now = Carbon::now();

        $tokenExp = Accurate::getToken('token');
        $tokenExp = Carbon::parse($tokenExp->expires_at);
        $tokendiff = $tokenExp->diffInDays($now);
        if ($tokendiff <= $minDiff) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => url('/api/accurate/refreshtoken'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 2,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            echo $response;
        }

        $dbsess = Accurate::getToken('db_session');
        $dbsess = Carbon::parse($dbsess->expires_at);
        $dbsessdiff = $dbsess->diffInDays($now);
        if ($dbsessdiff <= $minDiff) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => url('/api/accurate/dbsession'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 2,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            echo $response;
        }
    }
}
