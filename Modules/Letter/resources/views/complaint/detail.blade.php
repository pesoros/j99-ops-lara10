@extends('layouts.main', ['title' => $title ])

@section('content')
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
  <div class="row">
    <div class="col-12">
      <!-- Main content -->
      <div class="invoice p-3 mb-3">
        <!-- title row -->
        <div class="row">
          <div class="col-12">
            <h4>
              <img src="http://localhost:8000/assets/images/logo/j99-logo-wide.png" alt="J99 Logo" height="38" style="opacity: .8">
              <small class="float-right">{{ dateFormat($detailComplaint->created_at) }}</small>
            </h4>
          </div>
          <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
          <div class="col-sm-12 invoice-col">
            <p class="lead">Detail complaint</p>
            <div class="table-responsive">
              <table class="table">
                <tr>
                  <th>Nama bus :</th>
                  <td>{{ $detailComplaint->busname }}</td>
                </tr>
                <tr>
                  <th>Deskripsi :</th>
                  <td>{{ $detailComplaint->description }}</td>
                </tr>
                @if ($detailComplaint->workorder_numberid)
                  <tr>
                    <th>Nomor SPK :</th>
                    <td>{{ $detailComplaint->workorder_numberid }}</td>
                  </tr>
                @endif
              </table>
            </div>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Table row -->
        <div class="row">
          <div class="col-12 table-responsive">
            <p class="lead">Kerusakan</p>
            <table class="table table-striped">
              <thead>
              <tr>
                <th width="3">No</th>
                <th>Bagian</th>
                <th>Deskripsi</th>
              </tr>
              </thead>
              <tbody>
                @foreach ($damages as $key => $damage)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $damage->areacode }}-{{ $damage->scopecode }} | {{ $damage->scopename }}</td>
                    <td>{{ $damage->description }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.invoice -->
      <div>
        @if (permissionCheck('add') && !$detailComplaint->workorder_numberid)
          <a href="{{ url('letter/complaint/add/createworkorder/'.$detailComplaint->uuid) }}" onclick="return confirm('Anda yakin membuat SPK berdasarkan keluhan ini?')" class="btn bg-gradient-primary btn-sm">Buat SPK berdasarkan keluhan ini</a>
        @endif
      </div>
    </div><!-- /.col -->
  </div><!-- /.row -->
</div>
 
@endsection
