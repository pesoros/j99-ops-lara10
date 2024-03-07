<?php

namespace Modules\Masterdata\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MasterdataSparePartsController extends Controller
{
    public function listMasterSpareParts()
    {
        $data['title'] = 'Spare parts';

        return view('masterdata::spareparts.index', $data);
    }
}
