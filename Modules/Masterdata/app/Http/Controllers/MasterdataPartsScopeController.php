<?php

namespace Modules\Masterdata\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MasterData;

class MasterdataPartsScopeController extends Controller
{
    public function listMasterPartsScope()
    {
        $data['title'] = 'Ruang lingkup bagian';
        $data['list'] = MasterData::getMasterPartsScopeList();

        return view('masterdata::parts_scope.index', $data);
    }

    public function addMasterPartsScope()
    {
        $data['title'] = 'Tambah Master ruang lingkup bagian';

        return view('masterdata::parts_scope.add', $data);
    }

    public function addMasterPartsScopeStore(Request $request)
    {
        $credentials = $request->validate([
            'scope_name'      => ['required', 'string'],
            'scope_code'      => ['required', 'string'],
        ]);
        
        $saveData = [
            'uuid' => generateUuid(),
            'name' => $request->scope_name,
            'code' => $request->scope_code,
        ];
        
        $saveScope = MasterData::saveMasterPartsScope($saveData);

        if ($saveScope) {
            return back()->with('success', 'Master ruang lingkup bagian tersimpan!');
        }

        return back()->with('failed', 'Master ruang lingkup bagian gagal tersimpan!');   
    }
}
