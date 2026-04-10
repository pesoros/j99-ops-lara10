@extends('layouts.main', ['title' => $title ])

@section('content')

@if ($errors->any())
<div class="alert alert-danger alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <h5><i class="icon fas fa-ban"></i> Gagal Validasi!</h5>
  @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
  @endforeach
</div>
@endif

<div class="card card-warning">
  <div class="card-header">
    <h3 class="card-title">Form {{ $title }}</h3>
  </div>
  <form action="{{ url()->current() }}" method="POST">
    @csrf
    <div class="card-body row">

      <div class="col-sm-6">
        <div class="form-group">
          <label>Nama Driver <span class="text-danger">*</span></label>
          <select class="form-control select2bs4" name="driver_id" required>
            <option value="">-- Pilih Driver --</option>
            @foreach ($crew_list as $crew)
              <option value="{{ $crew->id }}" {{ old('driver_id', $current->driver_id) == $crew->id ? 'selected' : '' }}>
                {{ trim($crew->first_name . ' ' . $crew->second_name) }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label>Unit Bus <span class="text-danger">*</span></label>
          <select class="form-control select2bs4" name="bus_id" required>
            <option value="">-- Pilih Bus --</option>
            @foreach ($bus_list as $bus)
              <option value="{{ $bus->uuid }}" {{ old('bus_id', $current->bus_id) == $bus->uuid ? 'selected' : '' }}>
                {{ $bus->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label>Rute <span class="text-danger">*</span></label>
          <select class="form-control select2bs4" name="route_id" required>
            <option value="">-- Pilih Rute --</option>
            @foreach ($route_list as $route)
              <option value="{{ $route->id }}" {{ old('route_id', $current->route_id) == $route->id ? 'selected' : '' }}>
                {{ $route->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label>Tanggal <span class="text-danger">*</span></label>
          <input type="date" class="form-control" name="date" value="{{ old('date', $current->date) }}" required>
        </div>

        <div class="form-group">
          <label>Jumlah Hari Kerja <span class="text-danger">*</span></label>
          <input type="number" class="form-control" name="work_day_count" value="{{ old('work_day_count', $current->work_day_count) }}" min="0" required>
        </div>

        <div class="form-group">
          <label>Jam Istirahat (12 jam terakhir) <span class="text-danger">*</span></label>
          <input type="number" class="form-control" name="rest_hours_last_12h" value="{{ old('rest_hours_last_12h', $current->rest_hours_last_12h) }}" min="0" step="0.5" required>
        </div>
      </div>

      <div class="col-sm-6">
        <div class="form-group">
          <label>Tekanan Darah Sistolik <span class="text-danger">*</span></label>
          <input type="number" class="form-control" name="blood_pressure_systolic" value="{{ old('blood_pressure_systolic', $current->blood_pressure_systolic) }}" required>
        </div>

        <div class="form-group">
          <label>Tekanan Darah Diastolik <span class="text-danger">*</span></label>
          <input type="number" class="form-control" name="blood_pressure_diastolic" value="{{ old('blood_pressure_diastolic', $current->blood_pressure_diastolic) }}" required>
        </div>

        <div class="form-group">
          <label>Suhu Tubuh (&deg;C) <span class="text-danger">*</span></label>
          <input type="number" class="form-control" name="body_temperature" value="{{ old('body_temperature', $current->body_temperature) }}" step="0.1" required>
        </div>

        <div class="form-group">
          <label>Status Denyut Jantung <span class="text-danger">*</span></label>
          <select class="form-control" name="heart_rate_status" required>
            <option value="normal" {{ old('heart_rate_status', $current->heart_rate_status) == 'normal' ? 'selected' : '' }}>Normal</option>
            <option value="abnormal" {{ old('heart_rate_status', $current->heart_rate_status) == 'abnormal' ? 'selected' : '' }}>Abnormal</option>
          </select>
        </div>

        <div class="form-group">
          <label class="d-block">Kondisi Kesehatan</label>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="is_sick" name="is_sick" value="1"
              {{ old('is_sick', $current->is_sick) ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_sick">Sedang Sakit</label>
          </div>
          <div class="custom-control custom-checkbox mt-1">
            <input type="checkbox" class="custom-control-input" id="under_medication" name="under_medication" value="1"
              {{ old('under_medication', $current->under_medication) ? 'checked' : '' }}>
            <label class="custom-control-label" for="under_medication">Sedang Konsumsi Obat</label>
          </div>
        </div>

        <div class="form-group">
          <label class="d-block">Kelaikan Kerja</label>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="fit_to_work" name="fit_to_work" value="1"
              {{ old('fit_to_work', $current->fit_to_work) ? 'checked' : '' }}>
            <label class="custom-control-label" for="fit_to_work">Fit / Layak Bertugas</label>
          </div>
        </div>
      </div>

    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-warning">Update</button>
      <a href="{{ url('inspection/fit-check') }}" class="btn btn-secondary">Kembali</a>
    </div>
  </form>
</div>

@endsection