<?php

namespace Modules\Masterdata\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MasterData;

class MasterdataSnackController extends Controller
{
    public function listMasterSnack()
    {
        $data['title'] = 'Snack';
        $data['list'] = MasterData::getMasterSnackList();

        return view('masterdata::snack.index', $data);
    }

    public function addMasterSnack()
    {
        $data['title'] = 'Tambah Master Snack';

        return view('masterdata::snack.add', $data);
    }

    public function addMasterSnackStore(Request $request)
    {
        $credentials = $request->validate([
            'snackname'      => ['required', 'string'],
            'stock'      => ['required', 'string'],
        ]);

        $lastNumber = MasterData::getLastSnackNumber();
        $numbers = isset($lastNumber->number) ? $lastNumber->number + 1 : 1;
        $code = 'SN-' . str_pad($numbers, 4, '0', STR_PAD_LEFT);
        
        $saveData = [
            'uuid' => generateUuid(),
            'number' => $numbers,
            'code' => $code,
            'name' => $request->snackname,
            'stock' => $request->stock,
        ];
        
        $saveArea = MasterData::saveMasterSnack($saveData);

        if ($saveArea) {
            return back()->with('success', 'Master Snack tersimpan!');
        }

        return back()->with('failed', 'Master Snack gagal tersimpan!');   
    }

    public function editMasterSnack($uuid)
    {
        $data['title'] = 'Edit Master Snack';
        $data['current'] = MasterData::GetMasterSnack($uuid);

        return view('masterdata::snack.edit', $data);
    }

    public function editMasterSnackUpdate(Request $request, $uuid)
    {
        $credentials = $request->validate([
            'snackname'      => ['required', 'string'],
            'stock'      => ['required', 'string'],
        ]);
        
        $datetimeNow = now()->format('Y-m-d H:i:s');

        $updateData = [
            'name' => $request->snackname,
            'stock' => $request->stock,
            'updated_at' => $datetimeNow,
        ];
        
        $updateArea = MasterData::updateMasterSnack($uuid, $updateData);

        if ($updateArea) {
            return back()->with('success', 'Master Snack berhasil diubah!');
        }

        return back()->with('failed', 'Master Snack gagal diubah!');   
    }

    public function deleteMasterSnack($uuid)
    {
        $delete = MasterData::removeMasterSnack($uuid);

        return back()->with('success', 'Master ruang lingkup terhapus!');
    }
}
