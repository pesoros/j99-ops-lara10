<?php

namespace Modules\Trip\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TripBusStatusController extends Controller
{
    public function busStatuskanban()
    {
        $data['title'] = 'Status ketersediaan bus';

        return view('trip::busstatus.kanban', $data);
    }
}
