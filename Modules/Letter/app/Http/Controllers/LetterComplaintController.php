<?php

namespace Modules\Letter\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Complaint;
use App\Models\Bus;

class LetterComplaintController extends Controller
{
    public function listComplaint()
    {
        $data['title'] = 'Surat keluhan';

        $bus = Bus::getBusList();
        foreach ($bus as $key => $value) {
            $value->damages_active = Complaint::getComplaintCount($value->uuid)->counter;
        }
        $data['list'] = $bus;

        return view('letter::complaint.index', $data);
    }

    public function addComplaint()
    {
        $data['title'] = 'Tambah Surat keluhan';
        $data['bus'] = Bus::getBusActiveList();
        $data['partsscope'] = Complaint::getPartsScope();

        return view('letter::complaint.add', $data);
    }

    public function addComplaintStore(Request $request)
    {
        $credentials = $request->validate([
            'bus_uuid'      => ['required', 'string'],
            'description'      => ['required', 'string'],
        ]);

        $uuid = generateUuid();
                
        $saveData = [
            'uuid' => $uuid,
            'created_by' => auth()->user()->uuid,
            'bus_uuid' => $request->bus_uuid,
            'description' => $request->description,
        ];

        $saveDamageData = [];
        foreach ($request->damage_scope as $key => $value) {
            $saveDamageData[] = [
                'uuid' => generateUuid(),
                'complaint_uuid' =>  $uuid,
                'bus_uuid' => $request->bus_uuid,
                'scope_uuid' =>  $value,
                'description' =>  $request->damage_detail[$key],
            ];
        }
        
        $updateBusData['status'] = 0;

        $updateBusStatus = Bus::updateBus($request->bus_uuid,$updateBusData);
        $saveComplaint = Complaint::saveComplaint($saveData);
        $saveDamages = Complaint::saveDamages($saveDamageData);

        if ($saveComplaint) {
            return back()->with('success', 'Keluhan tersimpan!');
        }

        return back()->with('failed', 'Keluhan gagal tersimpan!');   
    }

    public function editComplaint($uuid)
    {
        $data['title'] = 'Edit Keluhan';
        $data['bus'] = Bus::getBusActiveList();
        $data['partsscope'] = Complaint::getPartsScope();
        $data['detailComplaint'] = Complaint::getComplaint($uuid);
        $data['damages'] = Complaint::getComplaintDamages($uuid);

        return view('letter::complaint.edit', $data);
    }

    public function editComplaintUpdate(Request $request, $uuid)
    {
        $credentials = $request->validate([
            'bus_uuid'      => ['required', 'string'],
            'description'      => ['required', 'string'],
        ]);

        $updateData = [
            'updated_by' => auth()->user()->uuid,
            'bus_uuid' => $request->bus_uuid,
            'description' => $request->description,
        ];

        $saveDamageData = [];
        foreach ($request->damage_scope as $key => $value) {
            $saveDamageData[] = [
                'uuid' => generateUuid(),
                'complaint_uuid' =>  $uuid,
                'bus_uuid' => $request->bus_uuid,
                'scope_uuid' =>  $value,
                'description' =>  $request->damage_detail[$key],
            ];
        }
        
        $saveComplaint = Complaint::updateComplaint($uuid, $updateData);
        $removeDamages = Complaint::removeDamages($uuid);
        $saveDamages = Complaint::saveDamages($saveDamageData);

        if ($saveComplaint) {
            return back()->with('success', 'Keluhan berhasil diubah!');
        }

        return back()->with('failed', 'Keluhan gagal diubah!');   
    }

    public function detailComplaint($uuid)
    {
        $data['title'] = 'Detail Keluhan';
        $data['bus'] = Bus::getBus($uuid);
        $data['damages'] = Complaint::getComplaintDamages($uuid);

        return view('letter::complaint.detail', $data);
    }

    public function createWorkorder($uuid)
    {
        $workorderUuid = generateUuid();
        $workorderCount = Complaint::getWorkorderCount();
        $count = !isset($workorderCount->count) ? 1 : $workorderCount->count + 1;

        $saveData = [
            'uuid' => $workorderUuid,
            'created_by' => auth()->user()->uuid,
            'numberid' => genrateLetterNumber('SPK',$count),
            'count' => $count,
        ];

        $updateData['workorder_uuid'] = $workorderUuid;

        $saveWorkorder = Complaint::saveWorkorder($saveData);
        $updateComplaint = Complaint::updateComplaint($uuid, $updateData);

        if ($saveWorkorder) {
            return back()->with('success', 'SPK berhasil dibuat!');
        }

        return back()->with('failed', 'SPK gagal dibuat!');   
    }
}
