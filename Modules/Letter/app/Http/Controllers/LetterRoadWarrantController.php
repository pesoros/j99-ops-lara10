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
use GuzzleHttp\Client;

class LetterRoadWarrantController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new Client(['timeout'  => 2.0]);
    }

    public function listRoadWarrant()
    {
        $roleInfo = Session('role_info_session');
        $isNeedDraft = false;
        if ($roleInfo->role_slug === 'super-user' || $roleInfo->role_slug === 'operational') {
            $isNeedDraft = true;
        }

        $data['title'] = 'Surat perintah jalan';
        $data['list'] = RoadWarrant::getRoadWarrantList($isNeedDraft);
        $data['bookavailable'] = RoadWarrant::getBookAvailable();
        $data['roleInfo'] = $roleInfo;

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
        if ($request->numberoftrip == "2" && $request->trip_assign == $request->trip_assign_return) {
            return back()->with('failed', 'Trip assign 1 dan 2 tidak boleh sama');
        }

        if ($request->driver_1 == $request->driver_2) {
            return back()->with('failed', 'Driver tidak boleh sama');
        }

        $roadWarrantCount = RoadWarrant::getRoadWarrantCount();
        $count = !isset($roadWarrantCount->count) ? 1 : $roadWarrantCount->count + 1;
        $trip_date = Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
        $busData = Bus::getBus($request->bus_uuid);
        $roadwarrant_uuid = generateUuid();
        $numberOfCrew = isset($request->driver_2) ? 3 : 2;

        $checkTripIsSet = $this->checkTripIsSet($request);

        if ($checkTripIsSet == true) {
            return back()->with('failed', 'Terdapat trip yg sudah di buat');
        }

        $tras = RoadWarrant::getTripAssign($request->trip_assign);
        $saveManifestData = [
            'uuid'                      =>  generateUuid(),
            'roadwarrant_uuid'          =>  $roadwarrant_uuid,
            'status'                    =>  1,
            'trip_date'                 =>  $trip_date,
            'trip_assign'               =>  $request->trip_assign,
            'email_assign'              =>  $busData->email,
            'fleet'                     =>  $request->bus_uuid,
        ];

        $saveRoadWarrantData = [
            'uuid'                      =>  $roadwarrant_uuid,
            'bus_uuid'                  =>  $request->bus_uuid,
            'category'                  =>  1,
            'numberid'                  =>  genrateLetterNumber('SPJ', $count),
            'count'                     =>  $count,
            'driver_1'                  =>  $request->driver_1,
            'driver_2'                  =>  $numberOfCrew > 2 ? $request->driver_2 : null,
            'codriver'                  =>  $request->codriver,
            'resto_id'                  =>  $tras->resto_id,
            'driver_allowance_1'        =>  numberClearence($request->driver_allowance),
            'codriver_allowance'        =>  numberClearence($request->codriver_allowance),
            'trip_allowance'            =>  numberClearence($request->trip_allowance),
            'fuel_allowance'            =>  numberClearence($request->fuel_allowance),
            'etoll_allowance'           =>  numberClearence($request->etoll_allowance),
            'crew_meal_allowance'       =>  numberClearence($request->crew_meal_allowance),
            'description'               =>  $request->description,
            'total_allowance'           =>  numberClearence($request->totalsum),
            'created_by'                =>  auth()->user()->uuid,
            'status'                    =>  0,
            'number_of_trip'            =>  $request->numberoftrip,
            'transferto'                =>  $request->transferto,
            'is_replacement_bus'        =>  $request->is_replacement_bus ?? 0,
            'departure_date'            =>  $trip_date
        ];
        $saveRoadWarrant = RoadWarrant::saveManifest($saveManifestData);

        if ($request->numberoftrip == "2") {
            $trip_date_return = Carbon::createFromFormat('d/m/Y', $request->date_return)->format('Y-m-d');
            $tras2 = RoadWarrant::getTripAssign($request->trip_assign_return);

            $saveManifestDataReturn = [
                'uuid'                      =>  generateUuid(),
                'roadwarrant_uuid'          =>  $roadwarrant_uuid,
                'status'                    =>  1,
                'trip_date'                 =>  $trip_date_return,
                'trip_assign'               =>  $request->trip_assign_return,
                'email_assign'              =>  $busData->email,
                'fleet'                     =>  $request->bus_uuid,
            ];

            $saveRoadWarrantDataAdditional = [
                'resto_id_return'           =>  $tras2->resto_id
            ];

            $saveRoadWarrantData['departure_date'] = $trip_date.' - '.$trip_date_return;

            $saveRoadWarrantData = $saveRoadWarrantData + $saveRoadWarrantDataAdditional;

            $saveRoadWarrantReturn = RoadWarrant::saveManifest($saveManifestDataReturn);
        }

        $saveRoadWarrant = RoadWarrant::saveRoadWarrant($saveRoadWarrantData);

        $puloGebangBoarding = $this->sendBoardingPuloGebang($saveManifestData, $busData->registration_number);
        if ($request->numberoftrip == "2") {
            $puloGebangBoarding = $this->sendBoardingPuloGebang($saveManifestDataReturn, $busData->registration_number);
        }

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
            $manifest = RoadWarrant::getManifestTrip($roadWarrant->uuid);
            $busclass = RoadWarrant::getBusClass($bus->busuuid);

            $isMarkerReady = true;
            foreach ($manifest as $key => $value) {
                $value->manifestBefore = Roadwarrant::getManifestByTrasBefore(
                    $value->trip_assign, 
                    $value->trip_date, 
                    $roadWarrant->bus_uuid
                );
                if (count($value->manifestBefore) > 0) {
                    $isMarkerReady = false;
                }
            }

            $data['roadwarrant'] = $roadWarrant;
            $data['crewCount'] = isset($roadWarrant->driver_2_name) ? 3 : 2;
            $data['bus'] = $bus;
            $data['manifest'] = $manifest;
            $data['busclass'] = $busclass;
            $data['isMarkerReady'] = $isMarkerReady;

            $data['expensesList'] = Roadwarrant::getExpensesList($uuid);

            $incomeSum = 0;
            $spendSum = 0;
            foreach ($data['expensesList'] as $key => $value) {
                if ($value->action == 'income') {
                    $incomeSum += $value->nominal;
                }

                if ($value->action == 'spend') {
                    $spendSum += $value->nominal;
                }
            }

            $data['incomeSum'] = $incomeSum;
            $data['spendSum'] = $spendSum;
            $data['totalSum'] = $spendSum - $incomeSum;
            $data['restMoney'] = $roadWarrant->total_allowance - $data['totalSum'] ;
            $data['roleInfo'] = Session('role_info_session');

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
            $manifest = RoadWarrant::getManifestTrip($uuid);
            $data['manifest'] = $manifest[0];
            $data['manifest_return'] = $manifest[1] ?? [];

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
            'description'               =>  $request->description,
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

    function editRoadWarrantAkapStore(Request $request, $uuid) {
        if ($request->numberoftrip == "2" && $request->trip_assign == $request->trip_assign_return) {
            return back()->with('failed', 'Trip assign 1 dan 2 tidak boleh sama');
        }

        if ($request->driver_1 == $request->driver_2) {
            return back()->with('failed', 'Driver tidak boleh sama');
        }

        $trip_date = Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
        $busData = Bus::getBus($request->bus_uuid);
        $numberOfCrew = isset($request->driver_2) ? 3 : 2;

        $removeManifest = RoadWarrant::removeManifest($uuid);

        $tras = RoadWarrant::getTripAssign($request->trip_assign);
        $saveManifestData = [
            'uuid'                      =>  generateUuid(),
            'roadwarrant_uuid'          =>  $uuid,
            'status'                    =>  1,
            'trip_date'                 =>  $trip_date,
            'trip_assign'               =>  $request->trip_assign,
            'email_assign'              =>  $busData->email,
            'fleet'                     =>  $request->bus_uuid,
        ];

        $editRoadWarrantData = [
            'bus_uuid'                  =>  $request->bus_uuid,
            'category'                  =>  1,
            'driver_1'                  =>  $request->driver_1,
            'driver_2'                  =>  $numberOfCrew > 2 ? $request->driver_2 : null,
            'codriver'                  =>  $request->codriver,
            'resto_id'                  =>  $tras->resto_id,
            'driver_allowance_1'        =>  numberClearence($request->driver_allowance),
            'codriver_allowance'        =>  numberClearence($request->codriver_allowance),
            'trip_allowance'            =>  numberClearence($request->trip_allowance),
            'fuel_allowance'            =>  numberClearence($request->fuel_allowance),
            'etoll_allowance'           =>  numberClearence($request->etoll_allowance),
            'crew_meal_allowance'       =>  numberClearence($request->crew_meal_allowance),
            'total_allowance'           =>  numberClearence($request->totalsum),
            'created_by'                =>  auth()->user()->uuid,
            'number_of_trip'            =>  $request->numberoftrip,
            'transferto'                =>  $request->transferto,
            'is_replacement_bus'        =>  $request->is_replacement_bus ?? 0,
            'departure_date'            =>  $trip_date
        ];
        $saveRoadWarrant = RoadWarrant::saveManifest($saveManifestData);
        
        if ($request->numberoftrip == "2") {
            $trip_date_return = Carbon::createFromFormat('d/m/Y', $request->date_return)->format('Y-m-d');
            $tras2 = RoadWarrant::getTripAssign($request->trip_assign_return);

            $saveManifestDataReturn = [
                'uuid'                      =>  generateUuid(),
                'roadwarrant_uuid'          =>  $uuid,
                'status'                    =>  1,
                'trip_date'                 =>  $trip_date_return,
                'trip_assign'               =>  $request->trip_assign_return,
                'email_assign'              =>  $busData->email,
                'fleet'                     =>  $request->bus_uuid,
            ];

            $editRoadWarrantData['resto_id_return'] = $tras2->resto_id;
            $editRoadWarrantData['departure_date'] = $trip_date.' - '.$trip_date_return;

            $saveRoadWarrantReturn = RoadWarrant::saveManifest($saveManifestDataReturn);
        }

        if (strval($request->transferto) === strval($request->old_transferto)) {
            $editRoadWarrantData['transferto_changed'] = 1;
        }

        $editRoadWarrant = RoadWarrant::updateRoadWarrant($uuid, $editRoadWarrantData);

        if ($editRoadWarrant) {
            return back()->with('success', 'Anda berhasil edit data SPJ AKAP');
        }

        return back()->with('failed', 'SPJ gagal di edit!');
    }

    public function roadWarrantStatus(Request $request, $status, $category, $uuid)
    {
        switch ($status) {
            case 'waitingmarker':
                $editRoadWarrantData = ['status'    =>  1];
                break;
            case 'marker':
                $editRoadWarrantData = ['status'    =>  2];
                break;
            case 'active':
                $editRoadWarrantData = ['status'    =>  3];
        }
        $editRoadWarrant = RoadWarrant::updateRoadWarrant($uuid, $editRoadWarrantData);
        return back()->with('success', 'Status berhasil dirubah!');
    }

    public function withdrawRoadWarrant(Request $request, $category, $uuid)
    {
        if ($category === '1') {
            $data['title'] = 'Withdraw Surat perintah jalan AKAP';
            $roadWarrant = RoadWarrant::getRoadWarrantAkap($uuid);
            $withdraw = RoadWarrant::getWithdraw($uuid);
            
            $data['roadwarrant'] = $roadWarrant;
            $data['withdraw'] = $withdraw;
            $data['crewCount'] = isset($roadWarrant->driver_2_name) ? 3 : 2;

            return view('letter::roadwarrantakap.withdraw', $data);
        } 
        
        return;
    }

    public function withdrawRoadWarrantStore(Request $request, $category, $uuid)
    {
        $credentials = $request->validate([
            'amount'            => ['required', 'string'],
        ]);

        $path = 'uploads/images/withdraw';
        $transfer_file_name = '-';
        
        if ($image = $request->file('image')){
            $transfer_file_name = 'SPJWD'.time().'-'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->move($path, $transfer_file_name);
        }

        $filepathname = $path.'/'.$transfer_file_name;

        $withdrawData = [
            'uuid'              => generateUuid(),
            'category'          => 'SPJ',
            'parent_uuid'       => $uuid,
            'amount'            => $request->amount,
            'transaction_id'    => $request->transaction_id ?? "",
            'image_file'        => $filepathname,
        ];

        $create = RoadWarrant::saveWithdraw($withdrawData);

        $roadWarrant = RoadWarrant::getRoadWarrantAkap($uuid);
        $remainAllowance = $roadWarrant->remaining_trip_allowance != null ? $roadWarrant->remaining_trip_allowance : 0;
        $remainAllowance = intval($remainAllowance) + intval($roadWarrant->total_allowance);
        $editRoadWarrantData = [
            'status'                        =>  4,
            'remaining_trip_allowance'      =>  $remainAllowance,
        ];

        $editRoadWarrant = RoadWarrant::updateRoadWarrant($uuid, $editRoadWarrantData);
        $sendAccurate = $this->accurateTransfer($uuid);

        if ($create) {
            return redirect('letter/roadwarrant/show/detail/1/'.$uuid)->with('success', 'Withdraw tersimpan!');
        }

        return back()->with('failed', 'Withdraw gagal tersimpan!');        
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

    public function accurateTransfer($uuid)
    {
        $path = "accurate/roadwarrant/moneytransfer";
        $fetch = $this->httpPost($path, $uuid);
        return $fetch;
    }

    public function accurateLpj(Request $request, $uuid)
    {
        $editRoadWarrantData = ['status'    =>  6];
        $editRoadWarrant = RoadWarrant::updateRoadWarrant($uuid, $editRoadWarrantData);
        $path = "accurate/roadwarrant/tripexpenses";
        $fetch = $this->httpPost($path, $uuid);
        
        return back()->with('success', 'Laporan LPJ berhasil!');
    }
    
    function httpPost($path, $uuid) {
        $props = ["uuid" => $uuid];
        $url = getenv('BACKEND_URL').'/'.$path;
        $header = array(
            'Content-Type: application/json'
        );
    
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($props),
            CURLOPT_HTTPHEADER => $header,
        ));
        
        $response = curl_exec($curl);
    
        curl_close($curl);
        return $response;
    }

    function checkTripIsSet($request) {
        $trip_date = Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
        $getManifest = RoadWarrant::getManifestByTras($request->trip_assign, $trip_date);
        if (count($getManifest) > 0) {
            return true;
        }

        if ($request->numberoftrip == "2") {
            $getManifestReturn = RoadWarrant::getManifestByTras($request->trip_assign_return, $trip_date);
            if (count($getManifestReturn) > 0) {
                return true;
            }
        }

        return false;
    }

    public function closeRoadwarrant(Request $request, $roadwarrantuuid, $manifestuuid)
    {
        $data = [
            'roadwarrant_uuid' => $roadwarrantuuid,
            'manifest_uuid'    => $manifestuuid,
        ];

        $setClose = $this->setCloseSpj($data);

        // ✅ Check API response
        if ($setClose && $setClose['status'] === 200) {
            return back()->with('success', 'Close SPJ Berhasil');
        }

        return back()->with('failed', 'Close SPJ Gagal!');
    }

    public function setCloseSpj($raw)
    {
        $response = $this->client->request(
            'POST',
            env('BE_BASEURL') . '/manifest/v3/close',
            [
                'form_params' => $raw,
            ]
        );

        return json_decode($response->getBody(), true); // return assoc array
    }
}
