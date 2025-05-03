<?php

namespace Modules\Accurate\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Accurate;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class AccurateSalesController extends Controller
{
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => getenv('BACKEND_URL'),
            'timeout'  => 120,
        ]);
    }

    public function index()
    {
        $data['title'] = 'Accurate Sales';
        $data['lists'] = Accurate::getSales();

        return view('accurate::sales.index', $data);
    }

    public function sync($bokingcode)
    {
        try {
            $response = $this->client->request('POST', '/accurate/sales', [
                'json' => [
                    'booking_code' => $bokingcode,
                ]
            ]);
            $body = $response->getBody()->getContents();

            return back()->with('success', 'Berhasil Sync '.$bokingcode);
        } catch (ClientException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            $errorResponse = json_decode($responseBody, true);

            return back()->with('failed', $errorResponse['messages']['error']);   
        }
    }

    public function syncBulk()
    {
        try {
            $response = $this->client->request('POST', '/accurate/sales/bulk');
            $body = $response->getBody()->getContents();

            return back()->with('success', 'Berhasil Sync Bulk');
        } catch (ClientException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            $errorResponse = json_decode($responseBody, true);

            return back()->with('failed', $errorResponse['messages']['error']);   
        }
    }
}
