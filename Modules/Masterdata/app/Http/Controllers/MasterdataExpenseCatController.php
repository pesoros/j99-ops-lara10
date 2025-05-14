<?php

namespace Modules\Masterdata\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MasterData;

class MasterdataExpenseCatController extends Controller
{
    public function index()
    {
        $data['title'] = 'Kategori Pengeluaran';
        $data['expensecategory'] = MasterData::getMasterExpenseCat();

        return view('masterdata::expense_cat.index', $data);
    }

    public function addMasterExpenseCat()
    {
        $data['title'] = 'Tambah Master Kategori Pengeluaran';

        return view('masterdata::expense_cat.add', $data);
    }

    public function addMasterExpenseCatStore(Request $request)
    {
        $credentials = $request->validate([
            'category_name'      => ['required', 'string'],
            'coa'   => ['required', 'string'],
            'status'   => ['required', 'string'],
        ]);
        
        $saveData = [
            'uuid' => generateUuid(),
            'name' => $request->category_name,
            'coa' => $request->coa,
            'status' => $request->status,
        ];
        
        $saveCategory = MasterData::saveMasterExpenseCat($saveData);

        if ($saveCategory) {
            return back()->with('success', 'Master bus tersimpan!');
        }

        return back()->with('failed', 'Master bus gagal tersimpan!');   
    }

    public function editMasterExpenseCat($uuid)
    {
        $data['title'] = 'Edit Master Kategori Pengeluaran';
        $data['current'] = MasterData::getExpenseCat($uuid);

        return view('masterdata::expense_cat.edit', $data);
    }

    public function editMasterExpenseCatUpdate(Request $request, $uuid)
    {
        $credentials = $request->validate([
            'category_name'      => ['required', 'string'],
            'coa'   => ['required', 'string'],
            'status'   => ['required', 'string'],
        ]);

        $updateData = [
            'uuid' => $uuid,
            'name' => $request->category_name,
            'coa' => $request->coa,
            'status' => $request->status,
        ];
        
        $updateBus = MasterData::updateMasterExpenseCat('uuid', $updateData);

        return back()->with('success', 'Master bus berhasil diubah!');
    }

    public function deleteMasterExpenseCat($uuid)
    {
        $delete = MasterData::removeMasterExpenseCat($uuid);

        return back()->with('success', 'Master Kategori Pengeluaran terhapus!');
    }

    public function expensecatCoaMaster()
    {
        $data['title'] = 'Kategori Pengeluaran (COA)';
        $data['expensecategory'] = MasterData::getMasterExpenseCat();

        return view('masterdata::expense_cat.coa', $data);
    }

    public function expensecatMasterUpdate(Request $request)
    {
        $input = $request->all();
        $expensecategory = json_decode($request->expensecategory, 1);

        foreach ($expensecategory as $key => $value) {
            if ($input['new_'.$value['id']] != $input['old_'.$value['id']]) {
                $updateData['id'] = $value['id'];
                $updateData['coa'] = $input['new_'.$value['id']];
                $updateCat = MasterData::updateMasterExpenseCat('id', $updateData);
            }
        }

        return back()->with('success', 'Master Kategori Pengeluaran Bus berhasil diubah!');
    }
}
