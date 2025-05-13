<?php

namespace Modules\Masterdata\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MasterData;

class MasterdataBusController extends Controller
{
    public function listMasterBus()
    {
        $data['title'] = 'Bus';
        $data['list'] = MasterData::getMasterBusList();

        return view('masterdata::bus.index', $data);
    }

    public function addMasterBus()
    {
        $data['title'] = 'Tambah Master Bus';
        $data['class'] = MasterData::getMasterClassList();
        $data['tras'] = MasterData::getTripAssignList();

        return view('masterdata::bus.add', $data);
    }

    public function addMasterBusStore(Request $request)
    {
        $credentials = $request->validate([
            'bus_name'      => ['required', 'string'],
            'registration_number'   => ['required', 'string'],
            'brand'   => ['required', 'string'],
            'model'   => ['required', 'string'],
            'bus_email'   => ['required', 'string'],
            'tras_a'   => ['required'],
            'tras_b'   => ['required'],
        ]);

        $uuid = generateUuid();
        
        $saveData = [
            'uuid' => $uuid,
            'name' => $request->bus_name,
            'registration_number' => $request->registration_number,
            'brand' => $request->brand,
            'model' => $request->model,
            'email' => $request->bus_email,
            'assign_id_a' => $request->tras_a,
            'assign_id_b' => $request->tras_b,
            'category' => 'AKAP',
            'status' => 1,
        ];

        foreach ($request->class as $key => $value) {
            $saveBusClassData[$key]['bus_uuid'] = $uuid;
            $saveBusClassData[$key]['class_uuid'] = $value;
        }
        
        $saveBus = MasterData::saveMasterBus($saveData);
        $saveBusClass = MasterData::saveBusClass($saveBusClassData);

        if ($saveBus) {
            return back()->with('success', 'Master bus tersimpan!');
        }

        return back()->with('failed', 'Master bus gagal tersimpan!');   
    }

    public function editMasterBus($uuid)
    {
        $data['title'] = 'Edit Master Bus';
        $data['class'] = MasterData::getMasterClassList();
        $data['current'] = MasterData::getMasterBus($uuid);
        $tempSelected = MasterData::getBusClass($uuid);
        $data['selectedClass'] = [];
        foreach ($tempSelected as $key => $value) {
            $data['selectedClass'][$key] = $value->uuid;
        }
        $data['tras'] = MasterData::getTripAssignList();

        return view('masterdata::bus.edit', $data);
    }

    public function editMasterBusUpdate(Request $request, $uuid)
    {
        $credentials = $request->validate([
            'bus_name'              => ['required', 'string'],
            'registration_number'   => ['required', 'string'],
            'brand'   => ['required', 'string'],
            'model'   => ['required', 'string'],
            'bus_email'   => ['required', 'string'],
            'tras_a'   => ['required'],
            'tras_b'   => ['required'],
        ]);

        $updateData = [
            'name' => $request->bus_name,
            'registration_number' => $request->registration_number,
            'brand' => $request->brand,
            'model' => $request->model,
            'email' => $request->bus_email,
            'assign_id_a' => $request->tras_a,
            'assign_id_b' => $request->tras_b,
        ];

        foreach ($request->class as $key => $value) {
            $saveBusClassData[$key]['bus_uuid'] = $uuid;
            $saveBusClassData[$key]['class_uuid'] = $value;
        }
        
        $updateBus = MasterData::updateMasterBus($uuid, $updateData);
        $removeClassFacilities = MasterData::removeBusClass($uuid);
        $updateBusClass = MasterData::saveBusClass($saveBusClassData);

        return back()->with('success', 'Master bus berhasil diubah!');
    }

    public function deleteMasterBus($uuid)
    {
        $delete = MasterData::removeMasterBus($uuid);

        return back()->with('success', 'Master bus terhapus!');
    }

    public function coaMasterBus($uuid)
    {
        $data['title'] = 'Bus (COA) ';
        $data['current'] = MasterData::getMasterBus($uuid);
        $data['expensecategory'] = MasterData::getMasterExpenseCategory($uuid);

        return view('masterdata::bus.coa', $data);
    }

    public function coaMasterBusUpdate(Request $request, $busuuid)
    {
        $input = $request->all();
        $expensecategory = json_decode($request->expensecategory, 1);

        foreach ($expensecategory as $key => $value) {
            if ($input['new_'.$value['id']] != $input['old_'.$value['id']]) {
                if ($value['bcoaid'] == NULL) {
                    $saveData['bus_uuid'] = $busuuid;
                    $saveData['expense_category_uuid'] = $value['uuid'];
                    $saveData['coa_number'] = $input['new_'.$value['id']];
                    $updateCoa = MasterData::saveMasterBusCoa($saveData);
                } else {
                    $updateData['id'] = $value['bcoaid'];
                    $updateData['coa_number'] = $input['new_'.$value['id']];
                    $updateCoa = MasterData::updateMasterBusCoa($updateData);
                }
            }
        }

        return back()->with('success', 'Master COA Bus berhasil diubah!');
    }
}
