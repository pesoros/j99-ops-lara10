<?php

namespace Modules\Trip\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Trip;

class TripManifestController extends Controller
{
    public function listManifest()
    {
        $data['title'] = 'Manifest';
        $data['manifestData'] = Trip::getManifestList();

        return view('trip::manifest.index', $data);
    }

    public function detailManifest(Request $request, $id)
    {
        $data['title'] = 'Detail Manifest';
        $data['detailManifest'] = Trip::getManifest($id);
        $data['passengerList'] = Trip::getPassengerList($data['detailManifest']->trip_assign, $data['detailManifest']->trip_date);

        return view('trip::manifest.detail', $data);
    }

    public function expensesReport(Request $request, $id)
    {
        $data['title'] = 'Laporan keuangan';
        $data['detailManifest'] = Trip::getManifest($id);
        $data['expensesList'] = Trip::getExpensesList($id);
        $data['id'] = $id;

        return view('trip::manifest.expenses', $data);
    }

    public function closeManifest(Request $request, $id)
    {
        $data['status'] = 2;
        $closeManifest = Trip::updateManifest($id, $data);

        return back()->with('success', 'Status Manifest berhasil diubah menjadi selesai!');
    }

    public function openManifest(Request $request, $id)
    {
        $data['status'] = 1;
        $closeManifest = Trip::updateManifest($id, $data);

        return back()->with('success', 'Status Manifest berhasil diubah menjadi aktif!');
    }

    public function expenseAccept(Request $request, $id)
    {
        $data['status'] = 2;
        $closeManifest = Trip::updateExpense($id, $data);

        return back()->with('success', 'Transaksi berhasil diterima!');
    }

    public function expenseReject(Request $request, $id)
    {
        $data['status'] = 0;
        $closeManifest = Trip::updateExpense($id, $data);

        return back()->with('success', 'Transaksi berhasil ditolak!');
    }

    public function expenseEdit(Request $request, $id, $expenseid)
    {
        $data['title'] = 'Edit transaksi';
        $data['expense'] = Trip::getExpense($expenseid);
        $data['id'] = $id;

        return view('trip::manifest.editexpense', $data);
    }

    public function expenseUpdate(Request $request, $id, $expenseid)
    {
        $credentials = $request->validate([
            'description'   => ['required', 'string'],
            'nominal'       => ['required', 'string'],
        ]);

        $updateData = [
            'description'   => $request->description,
            'nominal'       => numberClearence($request->nominal),
        ];

        $updateExpense = Trip::updateExpense($expenseid, $updateData);

        return redirect('trip/manifest/expenses/'.$id);
    }
}
