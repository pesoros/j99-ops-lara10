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

@if (session('success'))
  <div class="alert alert-success alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
  <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
  {{ session('success') }}
  </div>
@endif

@if (session('failed'))
  <div class="alert alert-danger alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
  {{ session('failed') }}
  </div>
@endif
 
<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Form {{ $title }}</h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form action="{{ url()->current() }}" method="post">
    @csrf
    <div class="card-body row">
      <div class="col-sm-6">
        <div class="form-group">
          <label for="bus_name">Nama bus</label>
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="bus_name" value="{{ $expense->nominal }}" readonly>
          </div>
        </div>
        <div class="form-group">
          <label for="class_name">Kelas bus</label>
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="class_name" value="{{ $roadwarrant->classname }}" readonly>
          </div>
        </div>
        <div class="form-group">
          <label for="seat_count">Jumlah kursi bus</label>
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="seat_count" value="{{ $roadwarrant->seat }} Kursi" readonly>
          </div>
        </div>
        <div class="form-group">
          <label for="km_start">Kilometer awal</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Km</span>
            </div>
            <input type="number" class="form-control" name="km_start" placeholder="0" value="{{ $roadwarrant->km_start }}" required>
          </div>
        </div>
        <div class="form-group">
          <label for="km_end">Kilometer akhir</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Km</span>
            </div>
            <input type="number" class="form-control" name="km_end" placeholder="0" value="{{ $roadwarrant->km_end }}" required>
          </div>
        </div>
        <div class="form-group">
          <label>Driver 1</label>
          <select class="form-control select2bs4" name="driver_1" style="width: 100%;" required>
            <option value="">Pilih</option>
            @foreach ($employee as $employeeItem)
                @if ($employeeItem->position === 'Driver')
                  <option value="{{ $employeeItem->id }}" @selected($roadwarrant->driver_1_id == $employeeItem->id)>
                      {{ $employeeItem->first_name.' '.$employeeItem->second_name }}
                  </option>
                @endif
            @endForeach
          </select>
        </div>
        <div class="form-group">
          <label>Driver 2</label>
          <select class="form-control select2bs4" name="driver_2" style="width: 100%;" required>
            <option value="">Pilih</option>
            @foreach ($employee as $employeeItem)
                @if ($employeeItem->position === 'Driver')
                  <option value="{{ $employeeItem->id }}" @selected($roadwarrant->driver_2_id == $employeeItem->id)>
                      {{ $employeeItem->first_name.' '.$employeeItem->second_name }}
                  </option>
                @endif
            @endForeach
          </select>
        </div>
        <div class="form-group">
          <label>Co driver</label>
          <select class="form-control select2bs4" name="codriver" style="width: 100%;" required>
            <option value="">Pilih</option>
            @foreach ($employee as $employeeItem)
                @if ($employeeItem->position === 'Assistant')
                  <option value="{{ $employeeItem->id }}" @selected($roadwarrant->codriver == $employeeItem->id)>
                      {{ $employeeItem->first_name.' '.$employeeItem->second_name }}
                  </option>
                @endif
            @endForeach
          </select>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group">
          <label for="crew_meal_allowance">Uang makan kru per hari</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Rp</span>
            </div>
            <input type="text" class="form-control moneyform" name="crew_meal_allowance" value="{{ $roadwarrant->crew_meal_allowance }}" placeholder="0" required>
          </div>
        </div>
        <!-- <div class="form-group">
          <label for="fuel_allowance">Uang solar</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Rp</span>
            </div>
            <input type="text" class="form-control moneyform" name="fuel_allowance" value="{{ $roadwarrant->fuel_allowance }}" placeholder="0" required>
          </div>
        </div> -->
        <div class="form-group">
          <label for="trip_allowance">Uang jalan</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Rp</span>
            </div>
            <input type="text" class="form-control moneyform" name="trip_allowance" value="{{ $roadwarrant->trip_allowance }}" placeholder="0" required>
          </div>
        </div>
        <div class="form-group">
          <label for="driver_allowance_1">Uang premi driver 1</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Rp</span>
            </div>
            <input type="text" class="form-control moneyform" name="driver_allowance_1" value="{{ $roadwarrant->driver_allowance_1 }}" placeholder="0" required>
          </div>
        </div>
        <div class="form-group">
          <label for="driver_allowance_2">Uang premi driver 2</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Rp</span>
            </div>
            <input type="text" class="form-control moneyform" name="driver_allowance_2" value="{{ $roadwarrant->driver_allowance_2 }}" placeholder="0" required>
          </div>
        </div>
        <div class="form-group">
          <label for="codriver_allowance">Uang premi co driver</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Rp</span>
            </div>
            <input type="text" class="form-control moneyform" name="codriver_allowance" value="{{ $roadwarrant->codriver_allowance }}" placeholder="0" required>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary" onclick="return confirm('Anda yakin data SPJ yg diisi sudah benar?')">Submit</button>
      <a href="{{ url('letter/roadwarrant') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
    </div>
  </form>
</div>
 
@endsection
