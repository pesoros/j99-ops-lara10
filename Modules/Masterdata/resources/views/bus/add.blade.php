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
          <label for="bus_name">Nama Bus</label>
          <input type="text" class="form-control" id="bus_name" name="bus_name" placeholder="Masukkan nama bus" value="{{ old('bus_name') }}" required>
        </div>
        <div class="form-group">
          <label for="registration_number">Plat Nomor</label>
          <input type="text" class="form-control" id="registration_number" name="registration_number" placeholder="Masukkan plat nomor" value="{{ old('registration_number') }}" required>
        </div>
        <div class="form-group">
          <label for="brand">Brand</label>
          <input type="text" class="form-control" id="brand" name="brand" placeholder="Masukkan nama brand" value="{{ old('brand') }}">
        </div>
        <div class="form-group">
          <label for="model">Model</label>
          <input type="text" class="form-control" id="model" name="model" placeholder="Masukkan nama model" value="{{ old('model') }}">
        </div>
        <div class="form-group">
          <label>Kelas</label>
          <select class="select2 select2-hidden-accessible" multiple="" name="class[]" data-placeholder="Pilih kelas" style="width: 100%;" data-select2-id="7" tabindex="-1" aria-hidden="true" required>
            @foreach ($class as $classItem)
                <option data-select2-id="{{ $classItem->uuid }}" value="{{ $classItem->uuid }}">
                    {{ $classItem->name }}
                </option>
            @endForeach
          </select>
        </div>
        <div class="form-group">
          <label for="brand">Email bus</label>
          <input type="text" class="form-control" id="bus_email" name="bus_email" placeholder="Masukkan email bus" value="{{ old('bus_email') }}">
        </div>
        <div class="form-group">
          <label>Trip assign A</label>
          <select class="form-control select2bs4" name="tras_a" style="width: 100%;" required>
            <option value="" @selected(old('tras_a') == "")>Pilih trip assign A</option>
            @foreach ($tras as $trasItem)
                <option value="{{ $trasItem->trasid }}" @selected(old('tras_a') == $trasItem->trasid)>
                    {{ $trasItem->trasid }} | {{ $trasItem->trip_title }} | {{ $trasItem->reg_no }}
                </option>
            @endForeach
          </select>
        </div>
        <div class="form-group">
          <label>Trip assign B</label>
          <select class="form-control select2bs4" name="tras_b" style="width: 100%;" required>
            <option value="" @selected(old('tras_b') == "")>Pilih trip assign B</option>
            @foreach ($tras as $trasItem)
                <option value="{{ $trasItem->trasid }}" @selected(old('tras_b') == $trasItem->trasid)>
                    {{ $trasItem->trasid }} | {{ $trasItem->trip_title }} | {{ $trasItem->reg_no }}
                </option>
            @endForeach
          </select>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Submit</button>
      <a href="{{ url('masterdata/bus') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
    </div>
  </form>
</div>
 
@endsection