@extends('layouts.main', ['title' => $title ])

@section('content')

@if (session('failed'))
<div class="alert alert-danger alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  {{ session('failed') }}
</div>
@endif

<div class="callout callout-info">
  <h5 class="mb-1"><i class="fas fa-hand-point-right"></i> Serahkan tablet kepada pengemudi</h5>
  Mohon dibaca terlebih dahulu. Tanda tangan di bawah menyatakan hasil pemeriksaan ini benar.
</div>

<div class="card card-primary card-outline">
  <div class="card-header">
    <h3 class="card-title">Hasil Pemeriksaan Kesehatan</h3>
  </div>

  <div class="card-body">

    {{-- Putusan kelaikan ditaruh paling atas: inilah yang ditandatangani --}}
    <div class="text-center mb-4">
      @if ($payload['fit_to_work'])
        <span class="badge badge-success" style="font-size:1.4rem;padding:.6rem 1.4rem;">
          FIT / LAYAK BERTUGAS
        </span>
      @else
        <span class="badge badge-danger" style="font-size:1.4rem;padding:.6rem 1.4rem;">
          TIDAK LAYAK BERTUGAS
        </span>
      @endif
    </div>

    <dl class="row mb-0">
      <dt class="col-sm-4 col-5">Nama Driver</dt>
      <dd class="col-sm-8 col-7">{{ $payload['driver_name'] ?: '-' }}</dd>

      <dt class="col-sm-4 col-5">Unit Bus</dt>
      <dd class="col-sm-8 col-7">{{ $payload['bus_unit'] ?: '-' }}</dd>

      <dt class="col-sm-4 col-5">Rute</dt>
      <dd class="col-sm-8 col-7">{{ $payload['route'] ?: '-' }}</dd>

      <dt class="col-sm-4 col-5">Tanggal</dt>
      <dd class="col-sm-8 col-7">{{ \Carbon\Carbon::parse($payload['date'])->format('d/m/Y') }}</dd>

      <dt class="col-sm-4 col-5">Jumlah Hari Kerja</dt>
      <dd class="col-sm-8 col-7">{{ $payload['work_day_count'] }} hari</dd>

      <dt class="col-sm-4 col-5">Jam Istirahat (12 jam terakhir)</dt>
      <dd class="col-sm-8 col-7">{{ $payload['rest_hours_last_12h'] }} jam</dd>

      <dt class="col-sm-4 col-5">Tekanan Darah</dt>
      <dd class="col-sm-8 col-7">
        {{ $payload['blood_pressure_systolic'] }}/{{ $payload['blood_pressure_diastolic'] }} mmHg
      </dd>

      <dt class="col-sm-4 col-5">Suhu Tubuh</dt>
      <dd class="col-sm-8 col-7">{{ $payload['body_temperature'] }} &deg;C</dd>

      <dt class="col-sm-4 col-5">Status Denyut Jantung</dt>
      <dd class="col-sm-8 col-7">{{ ucfirst($payload['heart_rate_status']) }}</dd>

      <dt class="col-sm-4 col-5">Sedang Sakit</dt>
      <dd class="col-sm-8 col-7">{{ $payload['is_sick'] ? 'Ya' : 'Tidak' }}</dd>

      <dt class="col-sm-4 col-5">Sedang Konsumsi Obat</dt>
      <dd class="col-sm-8 col-7">{{ $payload['under_medication'] ? 'Ya' : 'Tidak' }}</dd>
    </dl>

  </div>
</div>

<form action="{{ $formUrl }}" method="POST" id="sign_form">
  @csrf

  <div class="card card-primary card-outline">
    <div class="card-header">
      <h3 class="card-title">Tanda Tangan Pengemudi</h3>
    </div>

    <div class="card-body">
      <small class="d-block text-muted mb-2">
        Tanda tangan hanya disimpan pada dokumen ini.
      </small>

      <div class="sigpad-wrap">
        <canvas id="signature_canvas" class="sigpad-canvas"></canvas>
      </div>

      <input type="hidden" name="driver_signature" id="driver_signature">

      <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="signature_clear">
        <i class="fas fa-eraser"></i> Hapus / Ulangi
      </button>
      <span class="text-danger ml-2 d-none" id="signature_error">
        Tanda tangan pengemudi wajib diisi.
      </span>
    </div>

    <div class="card-footer">
      <button type="submit" class="btn btn-primary btn-lg" id="sign_submit">
        <i class="fas fa-check"></i> Selesai &amp; Simpan
      </button>
      <a href="{{ $backUrl }}" class="btn btn-secondary btn-lg">Ubah Data</a>
    </div>
  </div>

</form>

@endsection

@push('extra-styles')
<style>
  .sigpad-wrap {
    position: relative;
    border: 1px dashed #adb5bd;
    border-radius: .25rem;
    background: #fff;
    height: 220px;
  }
  .sigpad-canvas {
    display: block;
    width: 100%;
    height: 100%;
    /* wajib: tanpa ini layar ikut ter-scroll saat jari menggores */
    touch-action: none;
    cursor: crosshair;
  }
  .sigpad-wrap.is-invalid { border-color: #dc3545; }
  dl.row dt { font-weight: 600; }
  dl.row dd { margin-bottom: .5rem; }
</style>
@endpush

@push('extra-scripts')
<script src="{{ asset('assets/ui/plugins/signature-pad/signature_pad.umd.min.js') }}"></script>
<script>
  (function () {
    const canvas  = document.getElementById('signature_canvas');
    const hidden  = document.getElementById('driver_signature');
    const wrap    = canvas.closest('.sigpad-wrap');
    const errorEl = document.getElementById('signature_error');
    const form    = document.getElementById('sign_form');
    const submit  = document.getElementById('sign_submit');

    const pad = new SignaturePad(canvas, {
      penColor: '#000',
      backgroundColor: 'rgba(255,255,255,0)',
      minWidth: 0.8,
      maxWidth: 2.2,
    });

    // Menyesuaikan canvas ke ukuran tampilan + devicePixelRatio, jika tidak
    // garis blur dan meleset dari ujung jari di layar retina. Mengubah ukuran
    // canvas selalu mengosongkan isinya, jadi goresan digambar ulang.
    function resizeCanvas() {
      const data  = pad.isEmpty() ? null : pad.toDataURL('image/png');
      const ratio = Math.max(window.devicePixelRatio || 1, 1);

      canvas.width  = canvas.offsetWidth  * ratio;
      canvas.height = canvas.offsetHeight * ratio;
      canvas.getContext('2d').scale(ratio, ratio);

      pad.clear();

      if (data) {
        pad.fromDataURL(data, { width: canvas.offsetWidth, height: canvas.offsetHeight });
      }
    }

    window.addEventListener('resize', resizeCanvas);
    window.addEventListener('orientationchange', resizeCanvas);
    resizeCanvas();

    document.getElementById('signature_clear').addEventListener('click', function () {
      pad.clear();
      hidden.value = '';
    });

    pad.addEventListener('endStroke', function () {
      wrap.classList.remove('is-invalid');
      errorEl.classList.add('d-none');
    });

    let submitted = false;

    form.addEventListener('submit', function (e) {
      if (submitted) {
        e.preventDefault();
        return;
      }

      if (pad.isEmpty()) {
        e.preventDefault();
        wrap.classList.add('is-invalid');
        errorEl.classList.remove('d-none');
        wrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }

      hidden.value = pad.toDataURL('image/png');

      // Cegah dobel-tap pada tablet mengirim dua kali.
      submitted = true;
      submit.disabled = true;
      submit.innerHTML = 'Menyimpan...';
    });
  })();
</script>
@endpush
