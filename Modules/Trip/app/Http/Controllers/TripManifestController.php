<?php

namespace Modules\Trip\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Trip;

class TripManifestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function listManifest()
    {
        $data['title'] = 'Manifest';
        $data['manifestData'] = Trip::getManifestList();

        return view('trip::manifest.index', $data);
    }
}
