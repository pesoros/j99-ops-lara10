<?php

namespace Modules\Masterdata\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MasterData;

class MasterdataCoaController extends Controller
{
    public function coaMaster()
    {
        $data['title'] = 'COA';
        $data['expensecategory'] = MasterData::getMasterExpenseCategory();

        return view('masterdata::coa.edit', $data);
    }

    public function coaMasterUpdate(Request $request)
    {
        $input = $request->all();
        $expensecategory = json_decode($request->expensecategory, 1);

        foreach ($expensecategory as $key => $value) {
            if ($input['new_'.$value['id']] != $input['old_'.$value['id']]) {
                $updateData['id'] = $value['id'];
                $updateData['coa'] = $input['new_'.$value['id']];
                $updateCoa = MasterData::updateMasterBusCoa($updateData);
            }
        }

        return back()->with('success', 'Master COA Bus berhasil diubah!');
    }
}
