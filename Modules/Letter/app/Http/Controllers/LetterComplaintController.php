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
}
