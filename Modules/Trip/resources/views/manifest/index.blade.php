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
        <h3 class="card-title">List {{ $title }}</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <table id="datatable-def" class="table table-bordered table-striped">
        <thead>
        <tr>
          <th>No</th>
          <th>Email</th>
          <th>Trip assign</th>
          <th>Armada</th>
          <th>Tanggal</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
          @foreach ($manifestData as $key => $manifest)
            <tr>
              <td width="20" class="text-center">{{ intval($key) + 1 }}</td>
              <td>{{ $manifest->email_assign }}</td>
              <td>{{ $manifest->trip_assign }} | {{ $manifest->trip_title }}</td>
              <td>{{ $manifest->fleetname ?? $manifest->busname }}</td>
              <td>{{ dateFormat($manifest->trip_date) }}</td>
              <td>
                @if (STRVAL($manifest->status) === '1')
                  <span class="badge badge-warning">Aktif</span>                                        
                @elseif (STRVAL($manifest->status) === '2')
                  <span class="badge badge-success">Selesai</span>                                        
                @endif
              </td>
              <td>
                <div class="btn-group btn-block">
                  <a href="{{ url('trip/manifest/detail/'.$manifest->id) }}" class="btn btn-primary btn-sm">Detail</a>
                  <a href="{{ url('trip/manifest/expenses/'.$manifest->id) }}" class="btn btn-success btn-sm">Keuangan</a>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
 
@endsection