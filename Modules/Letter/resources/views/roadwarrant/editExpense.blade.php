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
          <label for="description">Deskripsi</label>
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="description" value="{{ $expense->description }}" required>
          </div>
        </div>
        <div class="form-group">
          <label for="trip_date">Tanggal Trip</label>
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="trip_date" value="{{ $expense->trip_date }}" readonly>
          </div>
        </div>
        <div class="form-group">
          <label for="action">Action</label>
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="action" value="{{ $expense->action }}" readonly>
          </div>
        </div>
        <div class="form-group">
          <label for="nominal">Nominal</label>
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="nominal" value="{{ $expense->nominal }}" required>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary" onclick="return confirm('Anda yakin data Pengeluaran yg diisi sudah benar?')">Submit</button>
      <a href="{{ url('letter/roadwarrant') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
    </div>
  </form>
</div>
 
@endsection
