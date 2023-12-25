<?php

namespace Modules\Letter\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Workorder;

class LetterWorkorderController extends Controller
{
    public function listWorkorder()
    {
        $data['title'] = 'Surat perintah kerja';
        $data['list'] = Workorder::getWorkorderList();

        return view('letter::workorder.index', $data);
    }

    public function detailWorkorder(Request $request, $uuid)
    {
        $data['title'] = 'Detail Keluhan';
        $data['detailWorkorder'] = Workorder::getWorkorder($uuid);
        $data['damages'] = Workorder::getComplaintDamages($data['detailWorkorder']->complaint_uuid);
        $data['actionlist'] = Workorder::getActionList();

        return view('letter::workorder.detail', $data);
    }

    public function progressWorkorder(Request $request, $uuid)
    {
        $updateData['status'] = 1;

        $updateWorkorder = Workorder::updateWorkorder($uuid, $updateData);

        if ($updateWorkorder) {
            return back()->with('success', 'Status SPK berhasil diubah menjadi dikerjakan!');
        }

        return back()->with('failed', 'Status SPK gagal diubah menjadi dikerjakan!');   
    }

    public function closeWorkorder(Request $request, $uuid)
    {
        $updateData['status'] = 2;

        $updateWorkorder = Workorder::updateWorkorder($uuid, $updateData);

        if ($updateWorkorder) {
            return back()->with('success', 'Status SPK berhasil diubah menjadi selesai!');
        }

        return back()->with('failed', 'Status SPK gagal diubah menjadi selesai!');   
    }

    public function updateAction(Request $request, $uuid)
    {
        foreach ($request->damage_uuid as $key => $value) {
            $updateData = [
                'action_status' =>  $request->action_status[$key],
                'action_description' =>  $request->action_description[$key],
            ];
            $updateDamageAction = Workorder::updateDamage($value, $updateData);
        }

        return back()->with('success', 'Status penanganan kerusakan berhasil diubah!');
    }
}
