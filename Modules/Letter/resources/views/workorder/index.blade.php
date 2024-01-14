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
          <th>Nomor SPK</th>
          <th>Nama Bus</th>
          <th>Deskripsi</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
          @foreach ($list as $key => $value)
            <tr>
              <td width="20" class="text-center">{{ intval($key) + 1 }}</td>
              <td width="200" >{{ $value->numberid }}</td>
              <td>{{ $value->busname }}</td>
              <td>{{ $value->description }}</td>
              <td>
                @if (STRVAL($value->status) === '0')
                  <span class="badge badge-secondary">Belum dikerjakan</span>                                        
                @endif
                @if (STRVAL($value->status) === '1')
                  <span class="badge badge-warning">Sedang dikerjakan</span>                                        
                @endif
                @if (STRVAL($value->status) === '2')
                  <span class="badge badge-success">Selesai</span>                                        
                @endif
              </td>
              <td>
                <div class="btn-group btn-block">
                  @if (permissionCheck('show')) <a href="{{ url('letter/workorder/show/detail/'.$value->uuid) }}" class="btn btn-warning btn-sm">Detail</a> @endif
                  @if (permissionCheck('add','goodsrequest')) <a href="{{ url('letter/goodsrequest/add?workorder_uuid='.$value->uuid) }}" class="btn btn-secondary btn-sm {{ $value->status !== 1 ? 'disabled' : ''}}">Buat SPB</a> @endif
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