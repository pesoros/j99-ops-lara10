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
    
    public function attendanceCrew($uuid)
    {
        $data['title'] = 'Absensi Crew';
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
