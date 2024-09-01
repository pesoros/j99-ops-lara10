<?php

namespace Modules\Masterdata\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MasterData;

class MasterdataSparePartsDummyController extends Controller
{
    public function listMasterSpareParts()
    {
        $data['title'] = 'Spare parts';
        $data['list'] = MasterData::getMasterPartsListsDummy();

        return view('masterdata::spareparts_dummy.index', $data);
    }
}
