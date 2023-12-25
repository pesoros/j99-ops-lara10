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
      <div class="col-sm-12">
        <div class="form-group">
          <label for="driver_allowance_1[]">Uang premi driver 1</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Rp</span>
            </div>
            <input type="text" class="form-control moneyform" name="driver_allowance_1[]" placeholder="0" required>
          </div>
        </div>
        <div class="form-group">
          <label for="driver_allowance_2[]">Uang premi driver 2</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Rp</span>
            </div>
            <input type="text" class="form-control moneyform" name="driver_allowance_2[]" placeholder="0" required>
          </div>
        </div>
        <div class="form-group">
          <label for="codriver_allowance[]">Uang premi co driver</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Rp</span>
            </div>
            <input type="text" class="form-control moneyform" name="codriver_allowance[]" placeholder="0" required>
          </div>
        </div>
        <div class="form-group">
          <label for="crew_allowance[]">Uang makan kru per hari</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Rp</span>
            </div>
            <input type="text" class="form-control moneyform" name="crew_allowance[]" placeholder="0" required>
          </div>
        </div>
        <div class="form-group">
          <label for="fuel_allowance[]">Uang solar</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Rp</span>
            </div>
            <input type="text" class="form-control moneyform" name="fuel_allowance[]" placeholder="0" required>
          </div>
        </div>
        <div class="form-group">
          <label for="trip_allowance[]">Uang jalan</label>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Rp</span>
            </div>
            <input type="text" class="form-control moneyform" name="trip_allowance[]" placeholder="0" required>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Submit</button>
      <a href="{{ url('letter/roadwarrant') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
    </div>
  </form>
</div>
 
@endsection
