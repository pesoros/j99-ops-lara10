@extends('layouts.main', ['title' => $title ])

@section('content')
 
<div class="card">
  <div class="card-header">
      <h3 class="card-title">List {{ $title }}</h3>
      <div class="float-right">
        @if (permissionCheck('add'))
          <a href="{{ url('letter/roadwarrant/add') }}" class="btn btn-secondary btn-sm">
            Buat SPJ AKAP
          </a>
          <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#pariwisata-modal">
            Buat SPJ Pariwisata
          </button>
        @endif
      </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="datatable-def" class="table table-bordered table-striped">
      <thead>
      <tr>
        <th>No</th>
        <th>Nomor SPJ</th>
        <th>Kategori Bus</th>
        <th>Nama Bus</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
      </thead>
      <tbody>
        @foreach ($list as $key => $value)
          <tr>
            <td width="20" class="text-center">{{ intval($key) + 1 }}</td>
            <td>{{ $value->numberid }}</td>
            <td>{{ intval($value->category) === 1 ? 'AKAP' : 'Pariwisata' }}</td>
            <td>{{ $value->busname }} {{ $value->is_replacement_bus ? '(Bus Pengganti)' : '' }}</td>
            <td>
              @if (intval($value->category) === 1 && $value->manifest_uuid == NULL)
                @if (intval($value->status) === 1)
                  <span class="badge badge-secondary">Draft</span>                                        
                @elseif (intval($value->status) === 2)
                  <span class="badge badge-info">Marker</span>      
                @elseif (intval($value->status) === 3)
                  <span class="badge badge-primary">Aktif</span>
                @elseif (intval($value->status) === 4)
                  <span class="badge badge-success">Sudah di transfer</span>
                @elseif (intval($value->status) === 5)
                  <span class="badge badge-danger">Perjalanan selesai</span>
                @elseif (intval($value->status) === 6)
                  <span class="badge bg-orange">SPJ Selesai</span>
                @endif
              @else
                @if (intval($value->status) === 1)
                  <span class="badge badge-warning">Aktif</span>                                        
                @elseif (intval($value->status) === 2)
                  <span class="badge badge-success">Selesai</span>                                        
                @endif
              @endif
              </td>
            <td>
              <div class="btn-group btn-block">
                @if (permissionCheck('show')) <a href="{{ url('letter/roadwarrant/show/detail/'.$value->category.'/'.$value->uuid) }}" class="btn btn-warning btn-sm">Detail</a> @endif
                @if (intval($value->status) < 3)
                  @if (permissionCheck('edit')) <a href="{{ url('letter/roadwarrant/edit/'.$value->category.'/'.$value->uuid) }}" class="btn btn-success btn-sm">Edit</a> @endif
                @endif
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <!-- /.card-body -->
</div>

<div class="modal fade" id="pariwisata-modal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Pilih reservasi untuk membuat SPJ</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="datatable-def" class="table table-bordered table-striped">
          <thead>
          <tr>
            <th>No</th>
            <th>Nomor booking</th>
            <th>Nama Bus</th>
            <th>Tujuan</th>
            <th>Tanggal</th>
            <th>Aksi</th>
          </tr>
          </thead>
          <tbody>
            @foreach ($bookavailable as $key => $book)
              <tr>
                <td width="20" class="text-center">{{ intval($key) + 1 }}</td>
                <td>{{ $book->booking_code }}</td>
                <td>{{ $book->customer_name }}</td>
                <td>{{ $book->city_to }}</td>
                <td>{{ dateTimeFormat($book->start_date) }} - {{ dateTimeFormat($book->finish_date) }}</td>
                <td>
                  <div class="btn-group btn-block">
                    @if (permissionCheck('add')) <a href="{{ url('letter/roadwarrant/add/'.$book->uuid) }}" class="btn btn-warning btn-sm">Buat SPJ</a> @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-success" data-dismiss="modal">Batal</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
 
@endsection