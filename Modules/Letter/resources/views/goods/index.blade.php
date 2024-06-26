@extends('layouts.main', ['title' => $title ])

@section('content')
 
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
          <th>Nomor SPB</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
          @foreach ($list as $key => $value)
            <tr>
              <td width="20" class="text-center">{{ intval($key) + 1 }}</td>
              <td width="200" >{{ $value->numberid }}</td>
              <td>
                @if (STRVAL($value->status) === '0')
                  <span class="badge badge-secondary">Menunggu</span>                                        
                @endif
                @if (STRVAL($value->status) === '1')
                  <span class="badge badge-warning">Sedang diproses</span>                                        
                @endif
                @if (STRVAL($value->status) === '2')
                  <span class="badge badge-success">Siap diambil</span>                                        
                @endif
                @if (STRVAL($value->status) === '3')
                  <span class="badge badge-danger">Selesai</span>                                        
                @endif
                @if (STRVAL($value->status) === '4')
                  <span class="badge badge-danger">Ditolak</span>                                        
                @endif
              </td>
              <td>
                <div class="btn-group btn-block">
                  @if (permissionCheck('show')) <a href="{{ url('letter/goodsrequest/show/detail/'.$value->uuid) }}" class="btn btn-warning btn-sm">Detail</a> @endif
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