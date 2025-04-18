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
    <input type="hidden" class="form-control" id="isnew" name="isnew" value="{{ $is_new }}">
    <div class="card-body row">
      <div class="col-sm-12">
        <div class="form-group">
          <label for="typename">Type</label>
          <input type="text" class="form-control" id="typename" name="typename" placeholder="Masukkan nama bagian" value="{{ $fleet->type }}">
        </div>
        <div class="form-group">
          <label for="percentage">Persentase</label>
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="percentage" placeholder="0" value="{{ $percentage }}" required>
            <div class="input-group-prepend">
              <span class="input-group-text">%</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" onclick="return confirm('Anda yakin mau ubah data point?')" class="btn btn-primary">Submit</button>
      <a href="{{ url('masterdata/pointsetting') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
    </div>
  </form>
</div>
 
@endsection