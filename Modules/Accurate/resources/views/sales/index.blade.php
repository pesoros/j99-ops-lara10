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
          <a href="{{ url('accurate/sales/syncbulk') }}" class="btn btn-warning btn-sm loadingscreen">
            Sync Data
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
        <th>Kode Booking</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
      </thead>
      <tbody>
        @foreach ($lists as $key => $value)
          <tr>
            <td width="20" class="text-center">{{ intval($key) + 1 }}</td>
            <td>{{ $value->booking_code }}</td>
            <td>
                @if ($value->accurate_status == 1)
                    Sudah Sinkron
                @else 
                    Belum sinkron
                @endif
            </td>
            <td>
              <div class="btn-group btn-block">
                @if (permissionCheck('show') && $value->accurate_status == NULL) <a href="{{ url('accurate/sales/sync/'.$value->booking_code) }}" class="btn btn-warning btn-sm loadingscreen">Sync</a> @endif
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

@push('extra-scripts')
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
<script type="text/javascript">
    $(function () {
        $('.loadingscreen').click(function(){
            $.LoadingOverlay("show", {
                text    : "Mohon tidak keluar dari halaman"
            });
        });
    });
</script>
@endpush