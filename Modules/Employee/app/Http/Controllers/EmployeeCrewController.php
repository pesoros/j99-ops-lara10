<?php

namespace Modules\Employee\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Employee\app\Imports\CrewImport;
use Modules\Employee\app\Exports\CrewTemplateExport;

class EmployeeCrewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function listCrew()
    {
        $data['title'] = 'Crew';
        $data['list'] = Employee::getCrewList();

        return view('employee::crew.index', $data);
    }

    public function addCrew()
    {
        $data['title'] = 'Tambah Crew';
        $data['position'] = Employee::getPosition();

        return view('employee::crew.add', $data);
    }

    public function addCrewStore(Request $request)
    {
        $credentials = $request->validate([
            'first_name'    => ['required', 'string'],
            'last_name'     => ['required', 'string'],
            'phone'         => ['required', 'string'],
            'email'         => ['required', 'string'],
            'position'      => ['required', 'string'],
            'bank_name'     => ['required', 'string'],
            'bank_number'   => ['required', 'string'],
        ]);

        $imageNameCrew = '-';
        $imageNameIdCard = '-';
        
        if ($image = $request->file('crew_image')){
            $imageNameCrew = time().'-'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->move('uploads/images/crew', $imageNameCrew);
        }

        if ($image = $request->file('idcard_image')){
            $imageNameIdCard = time().'-'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->move('uploads/images/idcard', $imageNameIdCard);
        }

        $crewData = [
            'first_name'     => $request->first_name,
            'second_name'    => $request->last_name,
            'position'       => $request->position,
            'phone_no'       => $request->phone,
            'picture'        => $imageNameCrew,
            'document_pic'   => $imageNameIdCard,
            'email_no'       => $request->email,
            'document_id'    => $request->idcard,
            'address_line_1' => $request->address,
            'blood_group'    => $request->blood_group,
            'city'           => $request->city,
            'zip'            => $request->zipcode,
            'bank_name'      => $request->bank_name,
            'bank_number'    => $request->bank_number,
        ];

        $create = Employee::saveCrew($crewData);

        if ($create) {
            return back()->with('success', 'Crew tersimpan!');
        }

        return back()->with('failed', 'Crew gagal tersimpan!');        
    }

    public function editCrew($uuid)
    {
        $data['title'] = 'Edit Crew';
        $data['position'] = Employee::getPosition();
        $data['current'] = Employee::getCrew($uuid);

        return view('employee::crew.edit', $data);
    }

    public function editCrewStore(Request $request, $uuid)
    {
        $credentials = $request->validate([
            'first_name' => ['required', 'string'],
            'last_name'  => ['required', 'string'],
            'phone'      => ['required', 'string'],
            'email'      => ['required', 'string'],
            'position'   => ['required', 'string'],
            'bank_name'     => ['required', 'string'],
            'bank_number'   => ['required', 'string'],
        ]);

        $crewData = [
            'first_name'     => $request->first_name,
            'second_name'    => $request->last_name,
            'position'       => $request->position,
            'phone_no'       => $request->phone,
            'email_no'       => $request->email,
            'document_id'    => $request->idcard,
            'address_line_1' => $request->address,
            'blood_group'    => $request->blood_group,
            'city'           => $request->city,
            'zip'            => $request->zipcode,
            'bank_name'      => $request->bank_name,
            'bank_number'    => $request->bank_number,
        ];
        
        if ($image = $request->file('crew_image')){
            $imageNameCrew = time().'-'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->move('uploads/images/crew', $imageNameCrew);
            $crewData['picture'] = $imageNameCrew;
        }

        if ($image = $request->file('idcard_image')){
            $imageNameIdCard = time().'-'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->move('uploads/images/idcard', $imageNameIdCard);
            $crewData['document_pic'] = $imageNameIdCard;
        }

        $update = Employee::updateCrew($uuid, $crewData);

        if ($update) {
            return back()->with('success', 'Crew berhasil diubah!');
        }

        return back()->with('failed', 'Crew gagal diubah!');        
    }

    public function downloadTemplate()
    {
        return Excel::download(new CrewTemplateExport(), 'template_crew.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function importCrew(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt'],
        ]);

        Excel::import(new CrewImport(), $request->file('file'));

        return back()->with('success', 'Data crew berhasil diimport!');
    }

    public function toggleActiveCrew($id)
    {
        Employee::toggleCrewActive($id);

        return back()->with('success', 'Status crew berhasil diubah!');
    }

    public function deleteCrew($id)
    {
        $crew = Employee::getCrew($id);

        if ($crew) {
            Employee::logDeleteCrew([
                'employee_id'   => $crew->id,
                'employee_name' => trim($crew->first_name . ' ' . $crew->second_name),
                'deleted_data'  => json_encode($crew),
                'deleted_by'    => Auth::id(),
                'deleted_at'    => now(),
            ]);
        }

        Employee::deleteCrew($id);

        return back()->with('success', 'Data crew berhasil dihapus!');
    }

    public function detailCrew($uuid)
    {
        $data['title'] = 'Detail Crew';
        $data['current'] = Employee::getCrew($uuid);
        $data['list'] = Employee::getCrewAttendance($uuid);
        $data['driving_history'] = Employee::getCrewDrivingHistory($uuid);

        $spj_totals = [];

        foreach ($data['driving_history'] as $key => $value) {
            $spj_key = $value->spj_number ?? $value->roadwarrant_uuid;

            if (!isset($spj_totals[$spj_key])) {
                $spj_totals[$spj_key] = ['total_distance' => 0, 'total_minutes' => 0];
            }

            if ($value->start_at && $value->finish_at) {
                $start = Carbon::parse($value->start_at);
                $finish = Carbon::parse($value->finish_at);
                $diff = $start->diff($finish);
                $data['driving_history'][$key]->duration = $diff->days > 0
                    ? $diff->days.'h '.$diff->h.'j '.$diff->i.'m'
                    : $diff->h.'j '.$diff->i.'m';
                $spj_totals[$spj_key]['total_minutes'] += ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
            } else {
                $data['driving_history'][$key]->duration = null;
            }

            if (!$value->latitude || !$value->longitude || !$value->checkout_latitude || !$value->checkout_longitude) {
                $data['driving_history'][$key]->distance = null;
            } else {
                $theta = $value->longitude - $value->checkout_longitude;
                $dist = sin(deg2rad($value->latitude)) * sin(deg2rad($value->checkout_latitude)) + cos(deg2rad($value->latitude)) * cos(deg2rad($value->checkout_latitude)) * cos(deg2rad($theta));
                $dist = min(1.0, max(-1.0, $dist));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $km = $miles * 1.609344;
                $data['driving_history'][$key]->distance = round($km, 2);
                $spj_totals[$spj_key]['total_distance'] += round($km, 2);
            }
        }

        $data['spj_totals'] = $spj_totals;

        foreach ($data['list'] as $key => $value) {
            if ($value->check_out_time == null) {
                $data['list'][$key]->distance = 0;
            } else {
                $theta = $value->check_in_long - $value->check_out_long;
                $dist = sin(deg2rad($value->check_in_lat)) * sin(deg2rad($value->check_out_lat)) + cos(deg2rad($value->check_in_lat)) * cos(deg2rad($value->check_out_lat)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $km = $miles * 1.609344;
                $data['list'][$key]->distance = round($km, 2);
            }
        }
        return view('employee::crew.detail', $data);
    }
}
