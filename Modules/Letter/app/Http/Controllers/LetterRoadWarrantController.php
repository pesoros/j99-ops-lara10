<?php

namespace Modules\Letter\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Workorder;
use App\Models\RoadWarrant;
use App\Models\Bus;
use App\Models\Trip;
use App\Models\Rest;
use Carbon\Carbon;

class LetterRoadWarrantController extends Controller
{
    public function listRoadWarrant()
    {
        $data['title'] = 'Surat perintah jalan';
        $data['list'] = RoadWarrant::getRoadWarrantList();
        $data['bookavailable'] = RoadWarrant::getBookAvailable();

        return view('letter::roadwarrant.index', $data);
    }

    public function addRoadWarrant($book_uuid)
    {
        $data['title'] = 'Tambah Surat perintah jalan Pariwisata';
        $data['book'] = RoadWarrant::getBook($book_uuid);
        $data['bookbus'] = RoadWarrant::getBookBus($book_uuid);
        $data['employee'] = RoadWarrant::getEmployee();

        return view('letter::roadwarrant.add', $data);
    }

    public function addRoadWarrantStore(Request $request, $book_uuid)
    {
        $roadWarrantCount = RoadWarrant::getRoadWarrantCount();
        $count = !isset($roadWarrantCount->count) ? 1 : $roadWarrantCount->count + 1;

        foreach ($request->bus_uuid as $key => $value) {
            $counter = $count + $key;
            $saveRoadWarrantData[] = [
                'uuid'                      =>  generateUuid(),
                'bus_uuid'                  =>  $value,
                'manifest_uuid'             =>  $book_uuid,
                'category'                  =>  2,
                'numberid'                  =>  genrateLetterNumber('SPJ', $counter),
                'count'                     =>  $counter,
                'km_start'                  =>  $request->km_start[$key],
                'driver_1'                  =>  $request->driver_1[$key],
                'driver_2'                  =>  $request->driver_2[$key],
                'codriver'                  =>  $request->codriver[$key],
                'driver_allowance_1'        =>  numberClearence($request->driver_allowance_1[$key]),
                'driver_allowance_2'        =>  numberClearence($request->driver_allowance_2[$key]),
                'codriver_allowance'        =>  numberClearence($request->codriver_allowance[$key]),
                'trip_allowance'            =>  numberClearence($request->trip_allowance[$key]),
                'fuel_allowance'            =>  0,
                'crew_meal_allowance'       =>  numberClearence($request->crew_meal_allowance[$key]),
                'created_by'                =>  auth()->user()->uuid,
                'status'                    =>  1,
            ];
        }

        $updateBookData['status'] = 1;

        $saveRoadWarrant = RoadWarrant::saveRoadWarrant($saveRoadWarrantData);
        $saveComplaint = RoadWarrant::updateBook($book_uuid, $updateBookData);

        if ($saveRoadWarrant) {
            return back()->with('success', 'Anda berhasil membuat SPJ pariwisata');
        }

        return back()->with('failed', 'SPJ gagal tersimpan!');
    }

    public function addRoadWarrantAkap()
    {
        $data['title'] = 'Tambah Surat perintah jalan AKAP';
        $data['bus'] = RoadWarrant::getBusAkap();
        $data['employee'] = RoadWarrant::getEmployee();

        return view('letter::roadwarrantakap.add', $data);
    }

    public function addRoadWarrantAkapStore(Request $request)
    {
        $roadWarrantCount = RoadWarrant::getRoadWarrantCount();
        $count = !isset($roadWarrantCount->count) ? 1 : $roadWarrantCount->count + 1;
        $trip_date = Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
        $busData = Bus::getBus($request->bus_uuid);
        $manifest_uuid = generateUuid();
        $tras = RoadWarrant::getTripAssign($request->trip_assign);
        $numberOfCrew = isset($request->driver_2) ? 3 : 2;
        $totalCrewMealAllowance = numberClearence($request->crew_meal_allowance) * $numberOfCrew;

        $saveManifestData = [
            'uuid'                      =>  $manifest_uuid,
            'status'                    =>  1,
            'trip_date'                 =>  $trip_date,
            'trip_assign'               =>  $request->trip_assign,
            'email_assign'              =>  $busData->email,
            'fleet'                     =>  $request->bus_uuid,
        ];

        $saveRoadWarrantData = [
            'uuid'                      =>  generateUuid(),
            'bus_uuid'                  =>  $request->bus_uuid,
            'manifest_uuid'             =>  $manifest_uuid,
            'category'                  =>  1,
            'numberid'                  =>  genrateLetterNumber('SPJ', $count),
            'count'                     =>  $count,
            'km_start'                  =>  $request->km_start,
            'driver_1'                  =>  $request->driver_1,
            'driver_2'                  =>  $numberOfCrew > 2 ? $request->driver_2 : null,
            'codriver'                  =>  $request->codriver,
            'resto_id'                  =>  $tras->resto_id,
            'driver_allowance_1'        =>  numberClearence($request->driver_allowance),
            'driver_allowance_2'        =>  $numberOfCrew > 2 ? numberClearence($request->driver_allowance) : null,
            'codriver_allowance'        =>  numberClearence($request->codriver_allowance),
            'trip_allowance'            =>  numberClearence($request->trip_allowance),
            'fuel_allowance'            =>  numberClearence($request->fuel_allowance),
            'etoll_allowance'           =>  numberClearence($request->etoll_allowance),
            'crew_meal_allowance'       =>  $totalCrewMealAllowance,
            'created_by'                =>  auth()->user()->uuid,
            'status'                    =>  1,
        ];

        $saveRoadWarrant = RoadWarrant::saveManifest($saveManifestData);
        $saveRoadWarrant = RoadWarrant::saveRoadWarrant($saveRoadWarrantData);
        $puloGebangBoarding = $this->sendBoardingPuloGebang($saveManifestData, $busData->registration_number);

        if ($saveRoadWarrant) {
            return back()->with('success', 'Anda berhasil membuat SPJ AKAP');
        }

        return back()->with('failed', 'SPJ gagal tersimpan!');
    }

    public function detailRoadWarrant(Request $request, $category, $uuid)
    {
        if ($category === '1') {
            $data['title'] = 'Detail SPJ AKAP';

            $roadWarrant = RoadWarrant::getRoadWarrantAkap($uuid);
            $bus = RoadWarrant::getBus($roadWarrant->bus_uuid);
            $manifest = RoadWarrant::getManifest($roadWarrant->manifest_uuid);
            $tras = RoadWarrant::getTripAssign($manifest->trip_assign);
            $busclass = RoadWarrant::getBusClass($bus->busuuid);

            $data['roadwarrant'] = $roadWarrant;
            $data['crewCount'] = isset($roadwarrant->driver_2_name) ? 3 : 2;
            $data['bus'] = $bus;
            $data['manifest'] = $manifest;
            $data['tras'] = $tras;
            $data['busclass'] = $busclass;
            $data['expensesList'] = Roadwarrant::getExpensesList($uuid);

            return view('letter::roadwarrantakap.detail', $data);
        } else if ($category === '2') {
            $data['title'] = 'Detail SPJ Pariwisata';
            $data['roadwarrant'] = RoadWarrant::getRoadWarrant($uuid);
            $data['expensesList'] = Roadwarrant::getExpensesList($uuid);

            return view('letter::roadwarrant.detail', $data);
        }
    }

    public function editRoadWarrant(Request $request, $category, $uuid)
    {
        if ($category === '1') {
            $data['title'] = 'Edit Surat perintah jalan AKAP';
            $data['bus'] = RoadWarrant::getBusAkap();
            $data['employee'] = RoadWarrant::getEmployee();
            $data['roadwarrant'] = RoadWarrant::getRoadWarrantAkap($uuid);

            return view('letter::roadwarrantakap.edit', $data);
        } else if ($category === '2') {
            $data['title'] = 'Edit Surat perintah jalan Pariwisata';
            $roadwarrant = RoadWarrant::getRoadWarrant($uuid);

            $data['roadwarrant'] = $roadwarrant;
            $data['book'] = RoadWarrant::getBook($roadwarrant->manifest_uuid);
            $data['employee'] = RoadWarrant::getEmployee();

            return view('letter::roadwarrant.edit', $data);
        }
    }

    public function editRoadWarrantStore(Request $request, $category, $uuid)
    {
        $editRoadWarrantData = [
            'km_start'                  =>  $request->km_start,
            'km_end'                    =>  $request->km_end,
            'driver_1'                  =>  $request->driver_1,
            'driver_2'                  =>  $request->driver_2,
            'codriver'                  =>  $request->codriver,
            'driver_allowance_1'        =>  numberClearence($request->driver_allowance_1),
            'driver_allowance_2'        =>  numberClearence($request->driver_allowance_2),
            'codriver_allowance'        =>  numberClearence($request->codriver_allowance),
            'trip_allowance'            =>  numberClearence($request->trip_allowance),
            'fuel_allowance'            =>  numberClearence($request->fuel_allowance),
            'etoll_allowance'           =>  numberClearence($request->etoll_allowance),
            'crew_meal_allowance'       =>  numberClearence($request->crew_meal_allowance),
            'updated_by'                =>  auth()->user()->uuid,
            'updated_at'                =>  Carbon::now(),
        ];

        $editRoadWarrant = RoadWarrant::updateRoadWarrant($uuid, $editRoadWarrantData);

        if ($editRoadWarrant) {
            $categoryname = $category === '1' ? 'AKAP' : 'Pariwisata';
            return back()->with('success', 'Anda berhasil edit data SPJ ' . $categoryname);
        }

        return back()->with('failed', 'SPJ gagal di edit!');
    }

    public function expenseStatusUpdate(Request $request, $category, $uuid, $expense_uuid, $status_id)
    {
        $updateExpense['status'] = $status_id;
        $saveComplaint = RoadWarrant::updateExpense($expense_uuid, $updateExpense);

        return back()->with('success', 'Status berhasil dirubah');
    }

    public function editRoadWarrantExpense(Request $request, $uuid)
    {
        $data['title'] = 'Edit Pengeluaran';
        $data['expense'] = Roadwarrant::getExpense($uuid);
        return view('letter::roadwarrant.editExpense', $data);
    }

    public function editRoadWarrantExpenseStore(Request $request, $uuid)
    {
        $updateExpense = [
            'description'   =>  $request->description,
            'nominal'   =>  $request->nominal,
        ];
        $saveExpense = RoadWarrant::updateExpense($uuid, $updateExpense);

        if ($saveExpense) {
            return back()->with('success', 'Anda berhasil edit data Pengeluaran');
        }

        return back()->with('failed', 'Pengeluaran gagal di edit!');
    }

    public function sendBoardingPuloGebang($manifestData, $registrationNumber)
    {
        $passengers = Trip::getPassengerList($manifestData['trip_assign'], $manifestData['trip_date']);

        $logData = [];
        foreach ($passengers as $key => $value) {
            if ($value->pickup_trip_location != 'Pulo Gebang') {
                continue;
            }
            $body = [
                'po_id'             =>  env('PULOGEBANG_PO_ID'),
                'ticket_id'         =>  $value->ticket_number,
                'date_of_departure' =>  $manifestData['trip_date'],
                'time_of_departure' =>  $value->dep_time,
                'destination_id'    =>  $value->ttpg_id,
                'nopol'             =>  $registrationNumber,
                // 'nik'               =>  $value->,
                'name'              =>  $value->name,
                // 'address'           =>  $value->,
                // 'city'              =>  $value->,
                // 'province'          =>  $value->,
                // 'sex'               =>  $value->,
                'telp'              =>  $value->phone,
                // 'email'             =>  $value->,
            ];
            $sendBoarding = Rest::postBoardingPuloGebang($body);
            $logData[] = [
                'ticket_number' => $value->ticket_number,
                'response' => STRVAL(json_encode($sendBoarding))
            ];
            sleep(1);
        }

        foreach ($logData as $key => $value) {
            RoadWarrant::saveBoardingPuloGebang($value);
        }

        return $logData;
    }
}
