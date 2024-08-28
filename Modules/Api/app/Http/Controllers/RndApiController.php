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

class RndApiController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('ACCURATE_BASEURI'),
            'timeout'  => 2.0,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $filename = 'rnd-data.csv';
    
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
    
        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
    
            // Add CSV headers
            fputcsv($handle, [
                'Name',
                'Code',
            ]);
    
             // Write data to a CSV file.
            fputcsv($handle, [
                'Budimen',
                md5('Budimen'),
            ]);
            fputcsv($handle, [
                'Hashmen',
                md5('Hashmen'),
            ]);
            fputcsv($handle, [
                'Asipman',
                md5('Asipman'),
            ]);
            
    
            // Close CSV file handle
            fclose($handle);
        }, 200, $headers);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv',
        ]);
        //read csv file and skip data
        $file = $request->file('file');
        $handle = fopen($file->path(), 'r');

        //skip the header row
        fgetcsv($handle);

        $chunksize = 25;
        while(!feof($handle))
        {
            $chunkdata = [];

            for($i = 0; $i<$chunksize; $i++)
            {
                $data = fgetcsv($handle);
                if($data === false)
                {
                    break;
                }
                $chunkdata[] = $data; 
            }
        }
        fclose($handle);

        return $chunkdata;
    }
}

// sample curl import 

// curl --location 'http://localhost:8000/api/rnd/csv/import' \
// --header 'Cookie: XSRF-TOKEN=eyJpdiI6IkpSNmZobU5MbGgxT085MGQ5aTR3MGc9PSIsInZhbHVlIjoiQ3pLZVZyVjVHV2N4QVJhNlNrcnJQdDVSbklpOVFIajZjMnRFd1ppVWUvemxSUlUzVkhWOHgxYTZDUnFUcXZ4UEtRRUFwZ3NhSWtUS045M1lwR2xkeEtMZlViRDUxREFiYzJlN0FSOEU2dk9MNEpnY3RMbmZuWHAzTjFtOHJXZjkiLCJtYWMiOiIzM2YyNmNkNTc0MjFiZmQ0YzYzNGQwNzY2NzRiNWQ5MDVjOGFhYTYwMjJiYzM5OWY3MzJjNTU5ZmE1NTE3NWYzIiwidGFnIjoiIn0%3D; laravel_session=eyJpdiI6Ik5rSytyNnMzWUdYU0JENS83aU0yamc9PSIsInZhbHVlIjoiNDlzdFBXV1RXOURJRGwyU3BTaUJWVHBBWlFHTjZVTC9la3N2c3FZWVRLOFFyUThIallJRjI2Z2tHSndNS2kzVHVPelY3TTYzNUJCc3JWNWZwSHhpRUoxZlNBbDEzYXFwbDVuS2dySUQ1VFhiQXNnMytOMUdFTEpyZUxxMGJhcHMiLCJtYWMiOiI3YTlhMDliZjAwNDhhOTgzYWY0NmEwMmIxMDQyOTEyYTg2NjA2NjkxNTgyODM3MDQxM2ExODI4MmM3Nzc3MTM1IiwidGFnIjoiIn0%3D' \
// --form 'file=@"/Users/bayuyuhartono/Downloads/rnd-data.csv"'