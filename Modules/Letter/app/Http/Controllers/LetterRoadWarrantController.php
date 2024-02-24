<?php

namespace Modules\Letter\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Workorder;
use App\Models\RoadWarrant;
use App\Models\Bus;
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
                'numberid'                  =>  genrateLetterNumber('SPJ',$counter),
                'count'                     =>  $counter,
                'driver_1'                  =>  $request->driver_1[$key],
                'driver_2'                  =>  $request->driver_2[$key],
                'codriver'                  =>  $request->codriver[$key],
                'driver_allowance_1'        =>  numberClearence($request->driver_allowance_1[$key]),
                'driver_allowance_2'        =>  numberClearence($request->driver_allowance_2[$key]),
                'codriver_allowance'        =>  numberClearence($request->codriver_allowance[$key]),
                'trip_allowance'            =>  numberClearence($request->trip_allowance[$key]),
                'fuel_allowance'            =>  numberClearence($request->fuel_allowance[$key]),
                'crew_meal_allowance'       =>  numberClearence($request->crew_meal_allowance[$key]),
                'created_by'                =>  auth()->user()->uuid,
            ];
        }
        
        $updateBookData['status'] = 1;
        
        $saveRoadWarrant = RoadWarrant::saveRoadWarrant($saveRoadWarrantData);
        $saveComplaint = RoadWarrant::updateBook($book_uuid,$updateBookData);

        if ($saveRoadWarrant) {
            return back()->with('success', 'Anda berhasil membuat SPJ pariwisata');
        }

        return back()->with('failed', 'SPJ gagal tersimpan!');   
    }

    public function addRoadWarrantAkap()
    {
        $data['title'] = 'Tambah Surat perintah jalan AKAP';
        $data['tripAssign'] = RoadWarrant::getTripAssign();

        return view('letter::roadwarrant.addAkap', $data);
    }

    public function addRoadWarrantAkapStore(Request $request)
    {
        $roadWarrantCount = RoadWarrant::getRoadWarrantCount();
        $count = !isset($roadWarrantCount->count) ? 1 : $roadWarrantCount->count + 1;

        $saveRoadWarrantData = [
            'uuid'                      =>  generateUuid(),
            'bus_uuid'                  =>  $request->bus_uuid,
            // 'manifest_uuid'             =>  $book_uuid,
            'category'                  =>  1,
            'numberid'                  =>  genrateLetterNumber('SPJ',$count),
            'count'                     =>  $count,
            'start_date'                =>  Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'),
            // 'driver_1'                  =>  $request->driver_1[$key],
            // 'driver_2'                  =>  $request->driver_2[$key],
            // 'codriver'                  =>  $request->codriver[$key],
            // 'driver_allowance_1'        =>  numberClearence($request->driver_allowance_1[$key]),
            // 'driver_allowance_2'        =>  numberClearence($request->driver_allowance_2[$key]),
            // 'codriver_allowance'        =>  numberClearence($request->codriver_allowance[$key]),
            // 'trip_allowance'            =>  numberClearence($request->trip_allowance[$key]),
            // 'fuel_allowance'            =>  numberClearence($request->fuel_allowance[$key]),
            // 'crew_meal_allowance'       =>  numberClearence($request->crew_meal_allowance[$key]),
            'created_by'                =>  auth()->user()->uuid,
        ];
                
        $saveRoadWarrant = RoadWarrant::saveRoadWarrant($saveRoadWarrantData);

        if ($saveRoadWarrant) {
            return back()->with('success', 'Anda berhasil membuat SPJ AKAP');
        }

        return back()->with('failed', 'SPJ gagal tersimpan!');   
    }

    public function detailRoadWarrant(Request $request, $uuid)
    {
        $data['title'] = 'Detail SPJ';
        $data['roadwarrant'] = RoadWarrant::getRoadWarrant($uuid);

        return view('letter::roadwarrant.detail', $data);
    }
}
