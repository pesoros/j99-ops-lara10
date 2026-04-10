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
  <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
  <h5><i class="icon fas fa-check"></i> Berhasil!</h5>
  {{ session('success') }}
</div>
@endif

@if (session('failed'))
<div class="alert alert-danger alert-dismissible">
  <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
  <h5><i class="icon fas fa-ban"></i> Gagal!</h5>
  {{ session('failed') }}
</div>
@endif

<div class="card">
  <div class="card-header">
    <h3 class="card-title">List {{ $title }}</h3>
    <div class="float-right">
      @if (permissionCheck('add'))
        <a href="{{ url('inspection/fit-check/add') }}" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i> Tambah Fit Check
        </a>
      @endif
    </div>
  </div>
  <div class="card-body">
    <table id="datatable-def" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>Nama Driver</th>
          <th>Unit Bus</th>
          <th>Rute</th>
          <th>TD</th>
          <th>Suhu</th>
          <th>Status</th>
          <th>Dibuat Oleh</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($list as $key => $value)
          <tr>
            <td class="text-center">{{ intval($key) + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($value->date)->format('d/m/Y') }}</td>
            <td>{{ $value->driver_name }}</td>
            <td>{{ $value->bus_unit }}</td>
            <td>{{ $value->route }}</td>
            <td>{{ $value->blood_pressure_systolic }}/{{ $value->blood_pressure_diastolic }}</td>
            <td>{{ $value->body_temperature }}&deg;C</td>
            <td>
              @if ($value->fit_to_work)
                <span class="badge badge-success">Fit</span>
              @else
                <span class="badge badge-danger">Tidak Fit</span>
              @endif
            </td>
            <td>{{ $value->created_by_name ?? '-' }}</td>
            <td>
              <div class="btn-group">
                <a href="{{ url('inspection/fit-check/print/'.$value->id) }}" class="btn btn-info btn-sm" target="_blank">
                  <i class="fas fa-print"></i>
                </a>
                @if (permissionCheck('edit'))
                  <a href="{{ url('inspection/fit-check/edit/'.$value->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i>
                  </a>
                @endif
                @if (permissionCheck('delete'))
                  <a href="{{ url('inspection/fit-check/delete/'.$value->id) }}" class="btn btn-danger btn-sm"
                    onclick="return confirm('Hapus data fit check ini?')">
                    <i class="fas fa-trash"></i>
                  </a>
                @endif
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection