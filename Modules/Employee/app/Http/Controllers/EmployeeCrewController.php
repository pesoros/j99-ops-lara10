<?php

namespace Modules\Employee\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Employee;

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
            'first_name' => ['required', 'string'],
            'last_name'  => ['required', 'string'],
            'phone'      => ['required', 'string'],
            'email'      => ['required', 'string'],
            'position'   => ['required', 'string'],
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

    public function detailCrew($uuid)
    {
        $data['title'] = 'Detail Crew';
        $data['current'] = Employee::getCrew($uuid);
        $data['list'] = Employee::getCrewAttendance($uuid);

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
