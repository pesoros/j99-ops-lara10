<?php

namespace Modules\Letter\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Complaint;

class LetterComplaintController extends Controller
{
    public function listComplaint()
    {
        $data['title'] = 'Surat keluhan';
        $data['list'] = Complaint::getComplaintList();

        return view('letter::complaint.index', $data);
    }

    public function addComplaint()
    {
        $data['title'] = 'Tambah Surat keluhan';
        $data['bus'] = Complaint::getBusList();
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
                'scope_uuid' =>  $value,
                'description' =>  $request->damage_detail[$key],
            ];
        }
        
        $saveComplaint = Complaint::saveComplaint($saveData);
        $saveDamages = Complaint::saveDamages($saveDamageData);

        if ($saveComplaint) {
            return back()->with('success', 'Keluhan tersimpan!');
        }

        return back()->with('failed', 'Keluhan gagal tersimpan!');   
    }

    public function detailComplaint(Request $request, $uuid)
    {
        $data['title'] = 'Detail Keluhan';
        $data['detailComplaint'] = Complaint::getComplaint($uuid);
        $data['damages'] = Complaint::getComplaintDamages($uuid);

        return view('letter::complaint.detail', $data);
    }

    public function createWorkorder($uuid)
    {
        $wotkorderCount = Complaint::getWorkorderCount();
        $workorderUuid = generateUuid();
        $saveData = [
            'uuid' => $workorderUuid,
            'created_by' => auth()->user()->uuid,
            'numberid' => genrateLetterNumber('SPK',$wotkorderCount + 1),
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
