<?php

namespace Modules\Letter\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LetterComplaintController extends Controller
{
    public function listMasterpartsarea()
    {
        $data['title'] = 'Surat keluhan';
        $data['list'] = MasterData::getMasterpartsareaList();

        return view('masterdata::parts_item.index', $data);
    }

    public function addMasterpartsarea()
    {
        $data['title'] = 'Tambah Surat keluhan';
        $data['scopes'] = MasterData::getMasterPartsAreaList();

        return view('masterdata::parts_item.add', $data);
    }

    public function addMasterpartsareaStore(Request $request)
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
        
        $saveItem = MasterData::saveMasterpartsarea($saveData);

        if ($saveItem) {
            return back()->with('success', 'Master ruang linkup bagian tersimpan!');
        }

        return back()->with('failed', 'Master ruang linkup bagian gagal tersimpan!');   
    }
}
