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
 
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">{{ $title }}</h3>
      <div class="float-right">
          <a href="{{ url('employee/crew') }}" class="btn bg-gradient-primary btn-sm">Kembali</a>
          @if (permissionCheck('edit')) <a href="{{ url('employee/crew/edit/'.$current->id) }}" class="btn btn-warning btn-sm">Edit</a> @endif
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <div class="table-responsive">
          <table class="table">
            <tr>
              <th>Nama Depan :</th>
              <td>{{ $current->first_name }}</td>
            </tr>
            <tr>
              <th>Nama Belakang :</th>
              <td>{{ $current->second_name }}</td>
            </tr>
            <tr>
              <th>Jabatan :</th>
              <td>{{ $current->position }}</td>
            </tr>
            <tr>
              <th>Nomor KTP :</th>
              <td>{{ $current->document_id }}</td>
            </tr>
          </table>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="table-responsive">
          <table class="table">
            <tr>
              <th>No. Telepon :</th>
              <td>{{ $current->phone_no }}</td>
            </tr>
            <tr>
              <th>Email :</th>
              <td>{{ $current->email_no }}</td>
            </tr>
            <tr>
              <th>Alamat :</th>
              <td>{{ $current->address_line_1 }} {{ $current->address_line_2 }}</td>
            </tr>
            <tr>
              <th></th>
              <td>{{ $current->country }} {{ $current->city }} {{ $current->zip }}</td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <div class="card-body">
      <table id="datatable-def" class="table table-bordered table-striped">
        <thead>
        <tr>
          <th>No</th>
          <th>Ref SPJ</th>
          <th>Check-In</th>
          <th>Check-Out</th>
          <th>Jarak Tempuh</th>
        </tr>
        </thead>
        <tbody>
          @foreach ($list as $key => $value)
            <tr>
              <td width="20" class="text-center">{{ intval($key) + 1 }}</td>
              <td>{{ $value->roadwarrant_uuid }}</td>
              <td>Time: {{ $value->check_in_time }}<br><a href="https://maps.google.com/?q={{ $value->check_in_lat }},{{ $value->check_in_long }}" target="_blank">Location [Google Maps]</a></td>
              <td>Time: {{ $value->check_out_time }}<br><a href="https://maps.google.com/?q={{ $value->check_out_lat }},{{ $value->check_out_long }}" target="_blank">Location [Google Maps]</a></td>
              <td>{{ $value->distance }} Km</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
 
@endsection