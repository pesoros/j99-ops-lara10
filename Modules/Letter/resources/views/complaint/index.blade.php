@extends('layouts.main', ['title' => $title ])

@section('content')
 
<div class="card">
    <!-- /.card-header -->
    <div class="card-body">
      <table id="datatable-def" class="table table-bordered table-striped">
        <thead>
        <tr>
          <th>No</th>
          <th>Nama Bus</th>
          <th>Jumlah keluhan aktif</th>
          <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
          @foreach ($list as $key => $value)
            <tr>
              <td width="20" class="text-center">{{ intval($key) + 1 }}</td>
              <td>{{ $value->name }} | {{ $value->registration_number }}</td>
              <td>{{ STRVAL($value->damages_active) }}</td>
              <td>
                <div class="btn-group btn-block">
                  @if (permissionCheck('show')) <a href="{{ url('letter/complaint/show/detail/'.$value->uuid) }}" class="btn btn-warning btn-sm">Detail</a> @endif
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