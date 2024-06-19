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

    public function editMasterPartsArea($uuid)
    {
        $data['title'] = 'Edit Master ruang lingkup bagian';
        $data['current'] = MasterData::GetMasterPartsArea($uuid);

        return view('masterdata::parts_area.edit', $data);
    }

    public function editMasterPartsAreaUpdate(Request $request, $uuid)
    {
        $credentials = $request->validate([
            'area_name'      => ['required', 'string'],
            'area_code'      => ['required', 'string'],
        ]);
        
        $updateData = [
            'name' => $request->area_name,
            'code' => $request->area_code,
        ];
        
        $updateArea = MasterData::updateMasterPartsArea($uuid, $updateData);

        if ($updateArea) {
            return back()->with('success', 'Master ruang lingkup bagian berhasil diubah!');
        }

        return back()->with('failed', 'Master ruang lingkup bagian gagal berhasil diubah!');   
    }

    public function deleteMasterPartsArea($uuid)
    {
        $delete = MasterData::removeMasterPartsArea($uuid);

        return back()->with('success', 'Master ruang lingkup terhapus!');
    }
}
