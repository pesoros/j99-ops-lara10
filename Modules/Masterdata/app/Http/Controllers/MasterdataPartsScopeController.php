<?php

namespace Modules\Masterdata\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MasterData;

class MasterdataPartsScopeController extends Controller
{
    public function listMasterPartsScope()
    {
        $data['title'] = 'Item bagian';
        $data['list'] = MasterData::getMasterPartsScopeList();

        return view('masterdata::parts_scope.index', $data);
    }

    public function addMasterPartsScope()
    {
        $data['title'] = 'Tambah Master item bagian';
        $data['scopes'] = MasterData::getMasterPartsAreaList();

        return view('masterdata::parts_scope.add', $data);
    }

    public function addMasterPartsScopeStore(Request $request)
    {
        $credentials = $request->validate([
            'item_name'      => ['required', 'string'],
            'item_code'      => ['required', 'string'],
            'area_uuid'      => ['required', 'string'],
        ]);
        
        $saveData = [
            'uuid' => generateUuid(),
            'name' => $request->item_name,
            'code' => $request->item_code,
            'parts_area_uuid' => $request->area_uuid,
        ];
        
        $saveItem = MasterData::saveMasterPartsScope($saveData);

        if ($saveItem) {
            return back()->with('success', 'Master ruang linkup bagian tersimpan!');
        }

        return back()->with('failed', 'Master ruang linkup bagian gagal tersimpan!');   
    }
}
