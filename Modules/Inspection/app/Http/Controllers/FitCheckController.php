<?php

namespace Modules\Inspection\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\MasterData;
use Modules\Inspection\app\Models\FitCheck;

class FitCheckController extends Controller
{
    /**
     * Alur fit check dibagi dua langkah:
     *   1. Dokter mengisi form  -> data divalidasi lalu dititipkan di session.
     *   2. Pengemudi membaca hasil (teks, bukan form) lalu menandatangani.
     * Validasi sengaja dijalankan di langkah 1 supaya pengemudi tidak pernah
     * menandatangani data yang kemudian ditolak validasi.
     */
    private const PENDING_ADD  = 'fitcheck_pending_add';
    private const PENDING_EDIT = 'fitcheck_pending_edit';

    public function index(Request $request)
    {
        $data['title'] = 'Fit Check';
        $data['list'] = FitCheck::getList();
        return view('inspection::fitcheck.index', $data);
    }

    // ---------------------------------------------------------------
    // Langkah 1 - input oleh dokter
    // ---------------------------------------------------------------

    public function add()
    {
        $this->prefillFrom(self::PENDING_ADD);

        $data['title'] = 'Tambah Fit Check';
        $data += $this->formLists();
        return view('inspection::fitcheck.add', $data);
    }

    public function store(Request $request)
    {
        $this->validateExamination($request);

        session([self::PENDING_ADD => $this->buildPayload($request)]);

        return redirect(url('inspection/fit-check/add/confirm'));
    }

    public function edit($id)
    {
        $this->prefillFrom(self::PENDING_EDIT . '_' . $id);

        $data['title']   = 'Edit Fit Check';
        $data['current'] = FitCheck::getById($id);

        if (!$data['current']) {
            return redirect(url('inspection/fit-check'))->with('failed', 'Data tidak ditemukan!');
        }

        $data += $this->formLists();

        return view('inspection::fitcheck.edit', $data);
    }

    public function update(Request $request, $id)
    {
        if (!FitCheck::getById($id)) {
            return redirect(url('inspection/fit-check'))->with('failed', 'Data tidak ditemukan!');
        }

        $this->validateExamination($request);

        session([self::PENDING_EDIT . '_' . $id => $this->buildPayload($request)]);

        return redirect(url('inspection/fit-check/edit/' . $id . '/confirm'));
    }

    // ---------------------------------------------------------------
    // Langkah 2 - dibaca dan ditandatangani oleh pengemudi
    // ---------------------------------------------------------------

    public function addConfirm()
    {
        $payload = session(self::PENDING_ADD);

        if (!$payload) {
            return redirect(url('inspection/fit-check/add'))
                ->with('failed', 'Sesi pengisian sudah berakhir, silakan isi ulang formulir.');
        }

        return view('inspection::fitcheck.confirm', [
            'title'      => 'Konfirmasi & Tanda Tangan',
            'payload'    => $payload,
            'formUrl'    => url('inspection/fit-check/add/confirm'),
            'backUrl'    => url('inspection/fit-check/add'),
        ]);
    }

    public function addConfirmStore(Request $request)
    {
        $payload = session(self::PENDING_ADD);

        if (!$payload) {
            return redirect(url('inspection/fit-check/add'))
                ->with('failed', 'Sesi pengisian sudah berakhir, silakan isi ulang formulir.');
        }

        $signature = $this->cleanSignature($request->driver_signature);

        if (!$signature) {
            return back()->with('failed', 'Tanda tangan pengemudi tidak valid, silakan ulangi.');
        }

        $payload['driver_signature'] = $signature;
        $payload['driver_signed_at'] = now();
        $payload['created_by']       = Auth::id();
        $payload['created_at']       = now();
        $payload['updated_at']       = now();

        $id = FitCheck::saveFitCheck($payload);

        // Selalu dibersihkan supaya refresh / tombol back tidak menyimpan dua kali.
        session()->forget(self::PENDING_ADD);

        if ($id) {
            return redirect(url('inspection/fit-check'))->with('success', 'Data fit check berhasil disimpan!');
        }

        return redirect(url('inspection/fit-check/add'))->with('failed', 'Data fit check gagal disimpan!');
    }

    public function editConfirm($id)
    {
        $payload = session(self::PENDING_EDIT . '_' . $id);

        if (!$payload) {
            return redirect(url('inspection/fit-check/edit/' . $id))
                ->with('failed', 'Sesi pengisian sudah berakhir, silakan isi ulang formulir.');
        }

        return view('inspection::fitcheck.confirm', [
            'title'   => 'Konfirmasi & Tanda Tangan',
            'payload' => $payload,
            'formUrl' => url('inspection/fit-check/edit/' . $id . '/confirm'),
            'backUrl' => url('inspection/fit-check/edit/' . $id),
        ]);
    }

    public function editConfirmStore(Request $request, $id)
    {
        $key     = self::PENDING_EDIT . '_' . $id;
        $payload = session($key);

        if (!$payload) {
            return redirect(url('inspection/fit-check/edit/' . $id))
                ->with('failed', 'Sesi pengisian sudah berakhir, silakan isi ulang formulir.');
        }

        if (!FitCheck::getById($id)) {
            session()->forget($key);
            return redirect(url('inspection/fit-check'))->with('failed', 'Data tidak ditemukan!');
        }

        $signature = $this->cleanSignature($request->driver_signature);

        if (!$signature) {
            return back()->with('failed', 'Tanda tangan pengemudi tidak valid, silakan ulangi.');
        }

        // Hasil pemeriksaan berubah, jadi tanda tangan lama tidak ikut terbawa.
        $payload['driver_signature'] = $signature;
        $payload['driver_signed_at'] = now();
        $payload['updated_at']       = now();

        $result = FitCheck::updateById($id, $payload);

        session()->forget($key);

        if ($result !== false) {
            return redirect(url('inspection/fit-check'))->with('success', 'Data fit check berhasil diubah!');
        }

        return redirect(url('inspection/fit-check/edit/' . $id))->with('failed', 'Data fit check gagal diubah!');
    }

    // ---------------------------------------------------------------

    public function delete($id)
    {
        FitCheck::deleteById($id);
        return redirect(url('inspection/fit-check'))->with('success', 'Data fit check berhasil dihapus!');
    }

    public function print($id)
    {
        $data['title']  = 'Print Fit Check';
        $data['record'] = FitCheck::getById($id);

        if (!$data['record']) {
            return redirect(url('inspection/fit-check'))->with('failed', 'Data tidak ditemukan!');
        }

        return view('inspection::fitcheck.print', $data);
    }

    // ---------------------------------------------------------------
    // Helper
    // ---------------------------------------------------------------

    /**
     * Saat pengemudi menekan "Ubah Data" di layar konfirmasi, isian dokter
     * dikembalikan ke formulir lewat old input sehingga tidak perlu diketik
     * ulang. Tidak dilakukan bila sudah ada old input dari gagal validasi,
     * supaya masukan terbaru tidak tertimpa data lama.
     */
    private function prefillFrom($sessionKey)
    {
        if (session()->hasOldInput()) {
            return;
        }

        $payload = session($sessionKey);

        if ($payload) {
            session()->flashInput($payload);
        }
    }

    private function formLists()
    {
        return [
            'crew_list'  => Employee::getCrewList(),
            'bus_list'   => MasterData::getMasterBusList(),
            'route_list' => MasterData::getTripRouteList(),
        ];
    }

    private function validateExamination(Request $request)
    {
        // Tanda tangan TIDAK divalidasi di sini: diambil pada langkah kedua.
        $request->validate([
            'driver_id'                 => ['required'],
            'bus_id'                    => ['required', 'string'],
            'route_id'                  => ['required', 'integer'],
            'date'                      => ['required', 'date'],
            'work_day_count'            => ['required', 'integer', 'min:0'],
            'rest_hours_last_12h'       => ['required', 'numeric', 'min:0'],
            'blood_pressure_systolic'   => ['required', 'integer'],
            'blood_pressure_diastolic'  => ['required', 'integer'],
            'body_temperature'          => ['required', 'numeric'],
            'heart_rate_status'         => ['required', 'string'],
        ]);
    }

    /**
     * Menyusun data pemeriksaan (tanpa tanda tangan) beserta snapshot nama
     * driver / bus / rute, supaya layar konfirmasi bisa menampilkannya
     * sebagai teks tanpa query ulang.
     */
    private function buildPayload(Request $request)
    {
        $crew  = collect(Employee::getCrewList())->firstWhere('id', $request->driver_id);
        $bus   = collect(MasterData::getMasterBusList())->firstWhere('uuid', $request->bus_id);
        $route = collect(MasterData::getTripRouteList())->firstWhere('id', (int) $request->route_id);

        return [
            'driver_id'                 => $request->driver_id,
            'driver_name'               => $crew ? trim($crew->first_name . ' ' . $crew->second_name) : '',
            'bus_id'                    => $request->bus_id,
            'bus_unit'                  => $bus ? $bus->name : '',
            'route_id'                  => $request->route_id,
            'route'                     => $route ? $route->name : '',
            'date'                      => $request->date,
            'work_day_count'            => $request->work_day_count,
            'rest_hours_last_12h'       => $request->rest_hours_last_12h,
            'is_sick'                   => $request->has('is_sick') ? 1 : 0,
            'under_medication'          => $request->has('under_medication') ? 1 : 0,
            'blood_pressure_systolic'   => $request->blood_pressure_systolic,
            'blood_pressure_diastolic'  => $request->blood_pressure_diastolic,
            'body_temperature'          => $request->body_temperature,
            'heart_rate_status'         => $request->heart_rate_status,
            'fit_to_work'               => $request->has('fit_to_work') ? 1 : 0,
        ];
    }

    /**
     * Terima hanya data URL PNG hasil signature pad, dan batasi ukurannya.
     * Mengembalikan null bila bukan format yang diharapkan.
     */
    private function cleanSignature($value)
    {
        if (!is_string($value) || $value === '') {
            return null;
        }

        if (!preg_match('/^data:image\/png;base64,[A-Za-z0-9+\/]+={0,2}$/', $value)) {
            return null;
        }

        // ~1MB data URL sudah jauh di atas tanda tangan normal (5-15KB).
        if (strlen($value) > 1048576) {
            return null;
        }

        return $value;
    }
}
