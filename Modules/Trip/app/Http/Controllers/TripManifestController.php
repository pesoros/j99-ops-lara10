<?php

namespace Modules\Trip\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Trip;

class TripManifestController extends Controller
{
    public function listManifest()
    {
        $data['title'] = 'Manifest';
        $data['manifestData'] = Trip::getManifestList();

        return view('trip::manifest.index', $data);
    }

    public function detailManifest(Request $request, $id)
    {
        $data['title'] = 'Detail Manifest';
        $data['detailManifest'] = Trip::getManifest($id);
        $data['passengerList'] = Trip::getPassengerList($data['detailManifest']->trip_assign, $data['detailManifest']->trip_date);

        return view('trip::manifest.detail', $data);
    }
}
