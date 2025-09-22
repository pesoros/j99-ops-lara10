<?php

namespace Modules\Accurate\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Accurate;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class AccurateManifestController extends Controller
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
        $data['lists'] = Accurate::getManifest();
        $data['beUrl'] = getenv('BACKEND_URL');

        return view('accurate::manifest.index', $data);
    }

    public function sync($manifestUuid)
    {
        try {
            $response = $this->client->request('POST', '/accurate/manifest', [
                'json' => [
                    'manifestUuid' => $manifestUuid,
                ]
            ]);
            $body = $response->getBody()->getContents();

            return back()->with('success', 'Berhasil Sync '.$manifestUuid);
        } catch (ClientException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            $errorResponse = json_decode($responseBody, true);

            return back()->with('failed', $errorResponse['messages']['error']);   
        }
    }

    public function syncBulk()
    {
        try {
            $response = $this->client->request('POST', '/accurate/manifest/bulk');
            $body = $response->getBody()->getContents();

            return back()->with('success', 'Berhasil Sync Bulk');
        } catch (ClientException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            $errorResponse = json_decode($responseBody, true);

            return back()->with('failed', $errorResponse['messages']['error']);   
        }
    }
}
