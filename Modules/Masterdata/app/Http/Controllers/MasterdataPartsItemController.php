<?php

namespace Modules\Masterdata\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MasterData;

class MasterdataPartsItemController extends Controller
{
    public function listMasterPartsItem()
    {
        $data['title'] = 'Item bagian';
        $data['list'] = MasterData::getMasterPartsItemList();

        return view('masterdata::parts_item.index', $data);
    }

    public function addMasterPartsItem()
    {
        $data['title'] = 'Tambah Master item bagian';
        $data['scopes'] = MasterData::getMasterPartsScopeList();

        return view('masterdata::parts_item.add', $data);
    }

    public function addMasterPartsItemStore(Request $request)
    {
        $credentials = $request->validate([
            'item_name'      => ['required', 'string'],
            'item_code'      => ['required', 'string'],
            'scope_uuid'      => ['required', 'string'],
        ]);
        
        $saveData = [
            'uuid' => generateUuid(),
            'name' => $request->item_name,
            'code' => $request->item_code,
            'parts_scope_uuid' => $request->scope_uuid,
        ];
        
        $saveItem = MasterData::saveMasterPartsItem($saveData);

        if ($saveItem) {
            return back()->with('success', 'Master ruang linkup bagian tersimpan!');
        }

        return back()->with('failed', 'Master ruang linkup bagian gagal tersimpan!');   
    }
}
