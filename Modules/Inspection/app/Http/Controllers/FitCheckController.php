<?php

namespace Modules\Inspection\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\MasterData;
use Modules\Inspection\app\Models\FitCheck;

class FitCheckController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Fit Check';
        $data['list'] = FitCheck::getList();
        return view('inspection::fitcheck.index', $data);
    }

    public function add()
    {
        $data['title'] = 'Tambah Fit Check';
        $data['crew_list'] = Employee::getCrewList();
        $data['bus_list']   = MasterData::getMasterBusList();
        $data['route_list'] = MasterData::getTripRouteList();
        return view('inspection::fitcheck.add', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'driver_id'                 => ['required'],
            'bus_id'                    => ['required', 'string'],
            'route_id'                  => ['required', 'integer'],
            'date'                      => ['required', 'date'],
            'work_day_count'            => ['required', 'integer', 'min:0'],
            'rest_hours_last_12h'       => ['required', 'numeric', 'min:0'],
            'blood_pressure_systolic'   => ['required', 'integer'],
            'blood_pressure_diastolic'  => ['required', 'integer'],
            'body_temperature'          => ['required', 'numeric'],
            'heart_rate_status'         => ['required', 'string'],
        ]);

        $crew  = collect(Employee::getCrewList())->firstWhere('id', $request->driver_id);
        $bus   = collect(MasterData::getMasterBusList())->firstWhere('uuid', $request->bus_id);
        $route = collect(MasterData::getTripRouteList())->firstWhere('id', (int) $request->route_id);

        $driverName = $crew  ? trim($crew->first_name . ' ' . $crew->second_name) : '';
        $busName    = $bus   ? $bus->name   : '';
        $routeName  = $route ? $route->name : '';

        $insertData = [
            'driver_id'                 => $request->driver_id,
            'driver_name'               => $driverName,
            'bus_id'                    => $request->bus_id,
            'bus_unit'                  => $busName,
            'route_id'                  => $request->route_id,
            'route'                     => $routeName,
            'date'                      => $request->date,
            'work_day_count'            => $request->work_day_count,
            'rest_hours_last_12h'       => $request->rest_hours_last_12h,
            'is_sick'                   => $request->has('is_sick') ? 1 : 0,
            'under_medication'          => $request->has('under_medication') ? 1 : 0,
            'blood_pressure_systolic'   => $request->blood_pressure_systolic,
            'blood_pressure_diastolic'  => $request->blood_pressure_diastolic,
            'body_temperature'          => $request->body_temperature,
            'heart_rate_status'         => $request->heart_rate_status,
            'fit_to_work'               => $request->has('fit_to_work') ? 1 : 0,
            'created_by'                => Auth::id(),
            'created_at'                => now(),
            'updated_at'                => now(),
        ];

        $id = FitCheck::saveFitCheck($insertData);

        if ($id) {
            return redirect(url('inspection/fit-check'))->with('success', 'Data fit check berhasil disimpan!');
        }

        return back()->with('failed', 'Data fit check gagal disimpan!');
    }

    public function edit($id)
    {
        $data['title']      = 'Edit Fit Check';
        $data['current']    = FitCheck::getById($id);
        $data['crew_list']  = Employee::getCrewList();
        $data['bus_list']   = MasterData::getMasterBusList();
        $data['route_list'] = MasterData::getTripRouteList();

        if (!$data['current']) {
            return redirect(url('inspection/fit-check'))->with('failed', 'Data tidak ditemukan!');
        }

        return view('inspection::fitcheck.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'driver_id'                 => ['required'],
            'bus_id'                    => ['required', 'string'],
            'route_id'                  => ['required', 'integer'],
            'date'                      => ['required', 'date'],
            'work_day_count'            => ['required', 'integer', 'min:0'],
            'rest_hours_last_12h'       => ['required', 'numeric', 'min:0'],
            'blood_pressure_systolic'   => ['required', 'integer'],
            'blood_pressure_diastolic'  => ['required', 'integer'],
            'body_temperature'          => ['required', 'numeric'],
            'heart_rate_status'         => ['required', 'string'],
        ]);

        $crew  = collect(Employee::getCrewList())->firstWhere('id', $request->driver_id);
        $bus   = collect(MasterData::getMasterBusList())->firstWhere('uuid', $request->bus_id);
        $route = collect(MasterData::getTripRouteList())->firstWhere('id', (int) $request->route_id);

        $driverName = $crew  ? trim($crew->first_name . ' ' . $crew->second_name) : '';
        $busName    = $bus   ? $bus->name   : '';
        $routeName  = $route ? $route->name : '';

        $updateData = [
            'driver_id'                 => $request->driver_id,
            'driver_name'               => $driverName,
            'bus_id'                    => $request->bus_id,
            'bus_unit'                  => $busName,
            'route_id'                  => $request->route_id,
            'route'                     => $routeName,
            'date'                      => $request->date,
            'work_day_count'            => $request->work_day_count,
            'rest_hours_last_12h'       => $request->rest_hours_last_12h,
            'is_sick'                   => $request->has('is_sick') ? 1 : 0,
            'under_medication'          => $request->has('under_medication') ? 1 : 0,
            'blood_pressure_systolic'   => $request->blood_pressure_systolic,
            'blood_pressure_diastolic'  => $request->blood_pressure_diastolic,
            'body_temperature'          => $request->body_temperature,
            'heart_rate_status'         => $request->heart_rate_status,
            'fit_to_work'               => $request->has('fit_to_work') ? 1 : 0,
            'updated_at'                => now(),
        ];

        $result = FitCheck::updateById($id, $updateData);

        if ($result !== false) {
            return redirect(url('inspection/fit-check'))->with('success', 'Data fit check berhasil diubah!');
        }

        return back()->with('failed', 'Data fit check gagal diubah!');
    }

    public function delete($id)
    {
        FitCheck::deleteById($id);
        return redirect(url('inspection/fit-check'))->with('success', 'Data fit check berhasil dihapus!');
    }

    public function print($id)
    {
        $data['title']  = 'Print Fit Check';
        $data['record'] = FitCheck::getById($id);

        if (!$data['record']) {
            return redirect(url('inspection/fit-check'))->with('failed', 'Data tidak ditemukan!');
        }

        return view('inspection::fitcheck.print', $data);
    }
}