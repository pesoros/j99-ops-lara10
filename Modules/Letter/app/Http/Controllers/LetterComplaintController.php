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
            $value->damages_active = Complaint::getDamagesCount($value->uuid)->counter;
        }
        $data['list'] = $bus;

        return view('letter::complaint.index', $data);
    }

    public function addComplaintStore(Request $request)
    {
        $credentials = $request->validate([
            'bus_uuid'      => ['required', 'string'],
        ]);

        $validateDamage = Complaint::getValidateDamage($request->bus_uuid, $request->damage_scope);
        if (COUNT($validateDamage) > 0) {
            return back()->with('failed', 'Keluhan sudah ada!');   
        }

        $saveDamageData = [
            'uuid' => generateUuid(),
            'created_by' => auth()->user()->uuid,
            'bus_uuid' => $request->bus_uuid,
            'scope_uuid' =>  $request->damage_scope,
            'description' =>  $request->damage_detail,
            'is_closed' =>  0,
        ];
        
        $updateBusData['status'] = 0;

        $updateBusStatus = Bus::updateBus($request->bus_uuid, $updateBusData);
        $saveDamages = Complaint::saveDamages($saveDamageData);

        if ($saveDamages) {
            return back()->with('success', 'Keluhan tersimpan!');
        }

        return back()->with('failed', 'Keluhan gagal tersimpan!');   
    }

    public function detailComplaint($uuid)
    {
        $data['title'] = 'Detail Keluhan';
        $data['bus'] = Bus::getBus($uuid);
        $data['workorder'] = Complaint::getWorkorderActive($uuid);
        $data['damages'] = Complaint::getDamages($uuid);
        $data['partsscope'] = Complaint::getPartsScope();

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
