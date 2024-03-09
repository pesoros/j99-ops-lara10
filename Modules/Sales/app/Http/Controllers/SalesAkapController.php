<?php

namespace Modules\Sales\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SalesAkapController extends Controller
{
    public function listAkap()
    {
        $data['title'] = 'Penjualan AKAP';

        return view('sales::akap.index', $data);
    }

    public function listPariwisata()
    {
        $data['title'] = 'Penjualan Pariwisata';

        return view('sales::pariwisata.index', $data);
    }
}
