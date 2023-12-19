<?php

namespace Modules\Masterdata\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MasterData;

class MasterdataComplaintController extends Controller
{
    public function listMasterComplaintScope()
    {
        $data['title'] = 'Bus';
        $data['list'] = MasterData::getMasterComplaintScopeList();

        return view('masterdata::bus.index', $data);
    }

    public function addMasterComplaintScope()
    {
        $data['title'] = 'Tambah Master Bus';
        $data['class'] = MasterData::getMasterClassList();

        return view('masterdata::bus.add', $data);
    }

    public function addMasterComplaintScopeStore(Request $request)
    {
        $credentials = $request->validate([
            'bus_name'      => ['required', 'string'],
            'registration_number'   => ['required', 'string'],
            'brand'   => ['required', 'string'],
            'model'   => ['required', 'string'],
            'class_uuid'   => ['required', 'string'],
        ]);
        
        $saveData = [
            'uuid' => generateUuid(),
            'name' => $request->bus_name,
            'registration_number' => $request->registration_number,
            'brand' => $request->brand,
            'model' => $request->model,
            'class_uuid' => $request->class_uuid,
            'status' => 1,
        ];
        
        $saveBus = MasterData::SaveMasterComplaintScope($saveData);

        if ($saveBus) {
            return back()->with('success', 'Master bus tersimpan!');
        }

        return back()->with('failed', 'Master bus gagal tersimpan!');   
    }
}
