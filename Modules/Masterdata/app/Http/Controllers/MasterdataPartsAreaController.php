<?php

namespace Modules\Masterdata\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MasterData;

class MasterdataPartsAreaController extends Controller
{
    public function listMasterPartsArea()
    {
        $data['title'] = 'Ruang lingkup bagian';
        $data['list'] = MasterData::getMasterPartsAreaList();

        return view('masterdata::parts_area.index', $data);
    }

    public function addMasterPartsArea()
    {
        $data['title'] = 'Tambah Master ruang lingkup bagian';

        return view('masterdata::parts_area.add', $data);
    }

    public function addMasterPartsAreaStore(Request $request)
    {
        $credentials = $request->validate([
            'area_name'      => ['required', 'string'],
            'area_code'      => ['required', 'string'],
        ]);
        
        $saveData = [
            'uuid' => generateUuid(),
            'name' => $request->area_name,
            'code' => $request->area_code,
        ];
        
        $saveArea = MasterData::saveMasterPartsArea($saveData);

        if ($saveArea) {
            return back()->with('success', 'Master ruang lingkup bagian tersimpan!');
        }

        return back()->with('failed', 'Master ruang lingkup bagian gagal tersimpan!');   
    }
}
