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
        <div class="float-right">
          @if (permissionCheck('add'))
            <a href="{{ url('employee/crew/import/template') }}" class="btn btn-secondary btn-sm mr-1">
              <i class="fas fa-download"></i> Download Template
            </a>
            <button type="button" class="btn btn-success btn-sm mr-1" data-toggle="modal" data-target="#importModal">
              <i class="fas fa-file-upload"></i> Import XLSX
            </button>
            <a href="{{ url('employee/crew/add') }}" class="btn btn-primary btn-sm">
              Tambah data crew
            </a>
          @endif
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <table id="datatable-def" class="table table-bordered table-striped">
        <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Jabatan</th>
          <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
          @foreach ($list as $key => $value)
            <tr>
              <td width="20" class="text-center">{{ intval($key) + 1 }}</td>
              <td>{{ $value->first_name }} {{ $value->second_name }}</td>
              <td>{{ $value->position }}</td>
              <td>
                <div class="btn-group btn-block">
                  <a href="{{ url('employee/crew/detail/'.$value->id) }}" class="btn btn-success btn-sm">Detail</a>
                  @if (permissionCheck('edit')) <a href="{{ url('employee/crew/edit/'.$value->id) }}" class="btn btn-warning btn-sm">Edit</a> @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
 
<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import Data Crew</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form action="{{ url('employee/crew/import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <label>File XLSX <span class="text-danger">*</span></label>
            <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
            <small class="text-muted">
              Gunakan <a href="{{ url('employee/crew/import/template') }}">template</a> yang tersedia.
              Kolom wajib: first_name, last_name, phone, email, position, bank_name, bank_number.
            </small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Import</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection