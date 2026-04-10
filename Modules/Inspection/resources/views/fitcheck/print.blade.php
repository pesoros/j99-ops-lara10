<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fit Check - {{ $record->driver_name }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Courier New', Courier, monospace;
      background: #f0f0f0;
      display: flex;
      justify-content: center;
      padding: 30px 10px;
    }

    .ticket-wrap {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 12px;
    }

    .ticket {
      background: #fff;
      width: 320px;
      border-radius: 10px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.15);
      overflow: hidden;
      position: relative;
    }

    .ticket-header {
      padding: 16px 20px 12px;
      text-align: center;
      color: #fff;
    }

    .ticket-header.fit    { background: #D5031E; }
    .ticket-header.notfit { background: #D5031E; }

    .ticket-header .logo {
      height: 28px;
      margin-bottom: 8px;
      filter: brightness(0) invert(1);
    }

    .ticket-header .company {
      font-size: 10px;
      letter-spacing: 2px;
      text-transform: uppercase;
      opacity: 0.85;
      margin-bottom: 4px;
    }

    .ticket-header .form-title {
      font-size: 18px;
      font-weight: bold;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .ticket-header .status-badge {
      display: inline-block;
      margin-top: 8px;
      padding: 4px 16px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: bold;
      letter-spacing: 1px;
      border: 2px solid rgba(255,255,255,0.6);
      color: #fff;
      text-transform: uppercase;
    }

    /* Zigzag tear */
    .tear {
      width: 100%;
      height: 14px;
      position: relative;
      overflow: hidden;
    }
    .tear::before {
      content: '';
      position: absolute;
      top: 0; left: -4px; right: -4px;
      height: 28px;
      background: radial-gradient(circle at 50% 0, #fff 6px, transparent 7px) repeat-x,
                  radial-gradient(circle at 50% 100%, #f0f0f0 6px, transparent 7px) repeat-x;
      background-size: 14px 14px;
    }
    .tear.top    { background: #f0f0f0; }
    .tear.bottom { background: #f0f0f0; }

    .ticket-body {
      padding: 12px 20px 16px;
    }

    .driver-section {
      text-align: center;
      margin-bottom: 14px;
      padding-bottom: 10px;
      border-bottom: 1px dashed #ccc;
    }

    .driver-section .driver-name {
      font-size: 15px;
      font-weight: bold;
      color: #222;
    }

    .driver-section .driver-info {
      font-size: 11px;
      color: #666;
      margin-top: 2px;
    }

    .row-item {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      font-size: 11.5px;
      padding: 4px 0;
      border-bottom: 1px dotted #eee;
    }

    .row-item:last-child { border-bottom: none; }

    .row-item .label {
      color: #666;
      flex: 0 0 55%;
    }

    .row-item .value {
      color: #111;
      font-weight: bold;
      text-align: right;
      flex: 0 0 44%;
    }

    .section-title {
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: #999;
      margin: 10px 0 4px;
    }

    .badge-ok   { color: #1a7a3c; }
    .badge-warn { color: #c0392b; }

    .ticket-footer {
      background: #f8f8f8;
      padding: 10px 20px;
      border-top: 1px dashed #ddd;
      text-align: center;
    }

    .ticket-footer .id-label {
      font-size: 9px;
      color: #aaa;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .ticket-footer .id-value {
      font-size: 13px;
      font-weight: bold;
      color: #555;
      letter-spacing: 2px;
    }

    .ticket-footer .timestamp {
      font-size: 9px;
      color: #bbb;
      margin-top: 4px;
    }

    .print-actions {
      text-align: center;
    }

    .print-actions button {
      padding: 10px 30px;
      font-size: 14px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin: 0 6px;
    }

    .btn-print { background: #2980b9; color: #fff; }
    .btn-back  { background: #7f8c8d; color: #fff; }

    @media print {
      body { background: none; padding: 0; }
      .print-actions { display: none; }
      .ticket { box-shadow: none; }
    }
  </style>
</head>
<body>

<div class="ticket-wrap">

  <!-- Print Actions -->
  <div class="print-actions">
    <button class="btn-print" onclick="window.print()">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px;">
        <polyline points="6 9 6 2 18 2 18 9"></polyline>
        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
        <rect x="6" y="14" width="12" height="8"></rect>
      </svg>
      Print
    </button>
    <button class="btn-back" onclick="window.history.back()">&#8592; Kembali</button>
  </div>

  <!-- Ticket Card -->
  <div class="ticket">

    <div class="ticket-header {{ $record->fit_to_work ? 'fit' : 'notfit' }}">
      <img src="{{ asset('assets/images/logo/j99-logo-wide.png') }}" alt="J99" class="logo">
      <div class="company">Pemeriksaan Kesehatan Pengemudi</div>
      <div class="form-title">Fit Check</div>
      <div class="status-badge">
        {{ $record->fit_to_work ? 'FIT TO WORK' : 'NOT FIT TO WORK' }}
      </div>
    </div>

    <div class="tear top"></div>

    <div class="ticket-body">

      <div class="driver-section">
        <div class="driver-name">{{ $record->driver_name }}</div>
        <div class="driver-info">{{ $record->bus_unit }} &nbsp;&bull;&nbsp; {{ $record->route }}</div>
        <div class="driver-info">{{ \Carbon\Carbon::parse($record->date)->format('d F Y') }}</div>
      </div>

      <div class="section-title">Jadwal & Kesiapan</div>

      <div class="row-item">
        <span class="label">Hari Kerja Beruntun</span>
        <span class="value">{{ $record->work_day_count }} hari</span>
      </div>
      <div class="row-item">
        <span class="label">Istirahat 12 Jam Terakhir</span>
        <span class="value">{{ $record->rest_hours_last_12h }} jam</span>
      </div>

      <div class="section-title">Kondisi Kesehatan</div>

      <div class="row-item">
        <span class="label">Sedang Sakit</span>
        <span class="value {{ $record->is_sick ? 'badge-warn' : 'badge-ok' }}">
          {{ $record->is_sick ? 'Ya' : 'Tidak' }}
        </span>
      </div>
      <div class="row-item">
        <span class="label">Konsumsi Obat</span>
        <span class="value {{ $record->under_medication ? 'badge-warn' : 'badge-ok' }}">
          {{ $record->under_medication ? 'Ya' : 'Tidak' }}
        </span>
      </div>

      <div class="section-title">Pemeriksaan Fisik</div>

      <div class="row-item">
        <span class="label">Tekanan Darah</span>
        <span class="value">{{ $record->blood_pressure_systolic }}/{{ $record->blood_pressure_diastolic }} mmHg</span>
      </div>
      <div class="row-item">
        <span class="label">Suhu Tubuh</span>
        <span class="value">{{ $record->body_temperature }}&deg;C</span>
      </div>
      <div class="row-item">
        <span class="label">Denyut Jantung</span>
        <span class="value {{ $record->heart_rate_status == 'normal' ? 'badge-ok' : 'badge-warn' }}">
          {{ ucfirst($record->heart_rate_status) }}
        </span>
      </div>

    </div>

    <div class="tear bottom"></div>

    <div class="ticket-footer">
      <div class="id-label">No. Dokumen</div>
      <div class="id-value">FC-{{ str_pad($record->id, 6, '0', STR_PAD_LEFT) }}</div>
      <div class="timestamp">Diterbitkan: {{ \Carbon\Carbon::parse($record->created_at)->format('d/m/Y H:i') }}</div>
      <div class="timestamp">Oleh: {{ $record->created_by_name ?? '-' }}</div>
    </div>

  </div>

</div>

</body>
</html>