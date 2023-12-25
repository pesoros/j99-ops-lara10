<?php

namespace Modules\Letter\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Workorder;
use App\Models\RoadWarrant;
use App\Models\Bus;

class LetterRoadWarrantController extends Controller
{
    public function listRoadWarrant()
    {
        $data['title'] = 'Surat perintah jalan';
        $data['list'] = RoadWarrant::getRoadWarrantList();

        return view('letter::roadwarrant.index', $data);
    }
    
    public function addRoadWarrant()
    {
        $data['title'] = 'Tambah Surat perintah jalan';
        $data['employee'] = RoadWarrant::getEmployee();

        return view('letter::roadwarrant.add', $data);
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
        
        $updateBusData['status'] = 0;

        $saveComplaint = Bus::updateBus($request->bus_uuid,$updateBusData);
        $saveComplaint = Complaint::saveComplaint($saveData);
        $saveDamages = Complaint::saveDamages($saveDamageData);

        if ($saveComplaint) {
            return back()->with('success', 'Keluhan tersimpan!');
        }

        return back()->with('failed', 'Keluhan gagal tersimpan!');   
    }
}
