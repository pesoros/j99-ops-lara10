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
          <label>Ruang lingkup</label>
          <select class="form-control select2bs4" name="area_uuid" style="width: 100%;">
            @foreach ($scopes as $scopeItem)
                <option value="{{ $scopeItem->uuid }}" @selected($current->parts_area_uuid == $scopeItem->uuid)>
                    {{ $scopeItem->name }}
                </option>
            @endForeach
          </select>
        </div>
        <div class="form-group">
          <label for="item_name">Nama bagian</label>
          <input type="text" class="form-control" id="item_name" name="item_name" placeholder="Masukkan nama bagian" value="{{ $current->name }}">
        </div>
        <div class="form-group">
          <label for="item_code">Kode</label>
          <input type="text" class="form-control" id="item_code" name="item_code" placeholder="Masukkan kode" value="{{ $current->code }}">
        </div>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Submit</button>
      <a href="{{ url('masterdata/partsscope') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
    </div>
  </form>
</div>
 
@endsection