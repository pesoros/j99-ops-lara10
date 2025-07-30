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
    <h3 class="card-title">Edit {{ $title }}</h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form action="{{ url()->current() }}" method="post">
    @csrf
    <div class="card-body">
      <div class="form-group">
        <label for="name">Nama</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama" value="{{ old('name', $user->name) }}">
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email" value="{{ old('email', $user->email) }}">
      </div>
      <div class="form-group">
        <label for="password">Password <small>(Kosongkan jika tidak ingin mengubah)</small></label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password baru">
      </div>
      <div class="form-group">
        <label>Role</label>
        <select class="form-control select2bs4" name="role" style="width: 100%;">
          @foreach ($roles as $role)
              <option value="{{ $role->uuid }}" 
                @selected(old('role', $user->role_uuid) == $role->uuid)>
                {{ $role->title }}
              </option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Update</button>
      <a href="{{ url('usermanagement/account') }}" onclick="return confirm('Anda yakin mau kembali?')" class="btn btn-success">Kembali</a>
    </div>
  </form>
</div>
 
@endsection
