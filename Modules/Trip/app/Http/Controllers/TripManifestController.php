<?php

namespace Modules\Trip\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Trip;
use App\Models\Rest;

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

    public function sendWaToPassengers(Request $request, $id)
    {
        $manifest = Trip::getManifest($id);
        $passengers = Trip::getPassengerList($manifest->trip_assign, $manifest->trip_date);
        $point = Trip::getPoint($manifest->trip_assign);

        foreach ($passengers as $key => $value) {
            $text = $this->generateEncodingTextWa($value->name, $manifest->trip_date, $point);
            $sendWa = Rest::sendWaPassenger($value->phone,$text);
            sleep(3);
        }

        return back()->with('success', 'Broadcast berhasil');
    }

    function generateEncodingTextWa($name, $departureDate, $point) {
        $text = 'Selamat Sore Bapak/ibu '.strtoupper($name).', 
Sekedar konfirmasi untuk mengingatkan jam pemberangkatan Bapak/Ibu '.strtoupper($name).' bersama Bus Juragan 99 Trans Unit GARFIELD   besok '.dateFormat($departureDate).'

Keberangkatan:
';
foreach ($point as $key => $value) {
    $text .= ($key + 1).'. '.$value->dep_point.' : '.substr($value->dep_time,0,5).' WIB
';
}
        
        $text .= '
Atas perhatian dan pengertiannya kami sampaikan terima kasih
        
Apabila ada perubahan titik naik mohon segera di Konfirmasikan
        
Mohon sudah berada di titik keberangkatan maksimal 30 menit sebelum jam keberangkatan yang sudah kami informasikan
        
Dan setelah menggunakan armada kami Juragan99Trans, adapun kesan pesan yang ingin disampaikan mengenai pelayanan armada kami silahkan mengisi link : 
go.watzap.id/zHeqjeg
        
Demikian konfirmasi dari kami, mohon maaf mengganggu waktunya 😊🙏
        
Sekian&Terimakasih';

        return rawurlencode($text);
    }
}
