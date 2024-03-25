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
  <form action="{{ url()->current() }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="card-body row">
      <div class="col-sm-6">
        <div class="form-group">
          <label for="first_name">Nama Depan</label>
          <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Masukkan nama depan" value="{{ $current->first_name }}" required>
        </div>
        <div class="form-group">
          <label for="last_name">Nama Belakang</label>
          <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Masukkan nama belakang" value="{{ $current->second_name }}" required>
        </div>
        <div class="form-group">
          <label for="phone">Telephone</label>
          <input type="text" class="form-control" id="phone" name="phone" placeholder="Masukkan nomor telephone" value="{{ $current->phone_no }}" required>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email" value="{{ $current->email_no }}" required>
        </div>
        <div class="form-group">
          <label for="crew_image">Foto crew</label>
          <div class="input-group">
            <input type="file" name="crew_image" class="form-control" value="" accept=".png, .jpg, .jpeg">
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group">
          <label for="idcard">Nomor ID Card</label>
          <input type="text" class="form-control" id="idcard" name="idcard" placeholder="Masukkan nomor idcard" value="{{ $current->document_id }}" required>
        </div>
        <div class="form-group">
          <label for="idcard_image">Foto ID Card</label>
          <div class="input-group">
            <input type="file" name="idcard_image" class="form-control" value="" accept=".png, .jpg, .jpeg">
          </div>
        </div>
        <div class="form-group">
          <label for="blood_group">Golongan Darah</label>
          <input type="text" class="form-control" id="blood_group" name="blood_group" placeholder="Masukkan golongan darah" value="{{ $current->blood_group }}">
        </div>
        <div class="form-group">
          <label>Posisi</label>
          <select class="form-control select2bs4" name="position" style="width: 100%;">
            <option value="">Pilih posisi</option>
            @foreach ($position as $positionItem)
              <option value="{{ $positionItem->type_name }}" @selected($current->position == $positionItem->type_name)>
                  {{ $positionItem->type_name }}
              </option>
            @endForeach
          </select>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="form-group">
          <label>Alamat lengkap</label>
          <textarea class="form-control" name="address" rows="3" placeholder="Masukkan alamat lengkap">{{ $current->address_line_1 }}</textarea>
        </div>
        <div class="form-group">
          <label for="city">Kota</label>
          <input type="text" class="form-control" id="city" name="city" placeholder="Masukkan nama kota" value="{{ $current->city }}">
        </div>
        <div class="form-group">
          <label for="zipcode">Kode pos</label>
          <input type="text" class="form-control" id="zipcode" name="zipcode" placeholder="Masukkan kode pos" value="{{ $current->zip }}">
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